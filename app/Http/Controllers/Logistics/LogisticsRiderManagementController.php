<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Accounts\CourierAccount;
use App\Models\Orders\MarketplaceOrder;
use App\Services\Registration\AccountRegistrationPolicy;
use App\Support\CurrentLogisticsAccount;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LogisticsRiderManagementController extends Controller
{
    private const ACTIVE_DELIVERY_STATUSES = ['courier_accepted', 'heading_pickup', 'arrived_pickup', 'in_transit', 'arrived_buyer'];

    public function index(Request $request): View
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $riders = CourierAccount::query()->where('logistics_account_id', $logistics->id)->latest('created_at')->get();
        $activeOrders = MarketplaceOrder::query()->where('logistics_account_id', $logistics->id)->whereIn('status', self::ACTIVE_DELIVERY_STATUSES)->whereNotNull('courier_email')->latest('updated_at')->get()->keyBy(fn (MarketplaceOrder $order): string => strtolower((string) $order->courier_email));
        $stats = [
            'total' => $riders->count(),
            'active' => $riders->where('account_status', 'active')->count(),
            'busy' => $riders->filter(fn (CourierAccount $rider): bool => $activeOrders->has(strtolower((string) $rider->email)))->count(),
        ];
        return view('logistics.rider-management', compact('riders', 'activeOrders', 'stats', 'logistics'));
    }

    public function store(Request $request, AccountRegistrationPolicy $policy): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        $request->merge(['email' => $policy->normalizeEmail($request->input('email')), 'contact_no' => $policy->normalizeMobile($request->input('contact_no'))]);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[\pL\s.\'-]+$/u'],
            'last_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[\pL\s.\'-]+$/u'],
            'birthday' => ['required', 'date', 'before_or_equal:' . now()->subYears(18)->toDateString()],
            'email' => ['required', 'email:rfc', 'max:180'],
            'contact_no' => ['required', 'regex:/^09\d{9}$/'],
            'license_number' => ['required', 'regex:/^[A-Za-z]\d{2}-\d{2}-\d{6}$/', Rule::unique('courier_accounts', 'license_number')],
            'license_expiry_date' => ['required', 'date', 'after_or_equal:today'],
            'vehicle_type' => ['required', Rule::in(['Motorcycle', 'Car', 'Van'])],
            'plate_number' => ['required', 'string', 'max:20', Rule::unique('courier_accounts', 'plate_number')],
            'driver_license_document' => ['required', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'orcr_document' => ['required', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        if ($policy->emailInUse($validated['email'])) throw ValidationException::withMessages(['email' => 'This email address is already used by a SARI account.']);
        if ($policy->mobileInUse($validated['contact_no'])) throw ValidationException::withMessages(['contact_no' => 'This mobile number is already used by a SARI account.']);

        $folder = 'registration-documents/rider/' . $logistics->id . '/' . now()->format('Y/m');
        $licensePath = $request->file('driver_license_document')->store($folder . '/license', 'local');
        $orcrPath = $request->file('orcr_document')->store($folder . '/orcr', 'local');
        $token = Str::random(64);
        $birthday = Carbon::parse($validated['birthday']);

        try {
            $rider = CourierAccount::query()->create([
                'registration_application_id' => null, 'logistics_account_id' => $logistics->id,
                'last_name' => trim($validated['last_name']), 'first_name' => trim($validated['first_name']), 'middle_initial' => null,
                'sex' => 'N/A', 'email' => $validated['email'], 'contact_no' => $validated['contact_no'],
                'birthday' => $birthday->toDateString(), 'age' => $birthday->age,
                'province_code' => $logistics->province_code, 'province_name' => $logistics->province_name,
                'municipality_code' => $logistics->municipality_code, 'municipality_name' => $logistics->municipality_name,
                'barangay_code' => $logistics->barangay_code, 'barangay_name' => $logistics->barangay_name,
                'street_address' => $logistics->street_address, 'password' => Hash::make(Str::random(64)),
                'vehicle_type' => $validated['vehicle_type'], 'plate_number' => strtoupper(trim($validated['plate_number'])),
                'orcr_path' => $orcrPath, 'id_path' => $licensePath, 'driver_license_path' => $licensePath,
                'license_number' => strtoupper($validated['license_number']), 'license_expiry_date' => $validated['license_expiry_date'],
                'account_status' => 'pending_activation', 'availability_status' => 'offline',
                'activation_token_hash' => hash('sha256', $token), 'activation_expires_at' => now()->addHours(24), 'activated_at' => null,
            ]);

            $this->sendActivationEmail($rider, $token);
        } catch (\Throwable $e) {
            isset($rider) && $rider->exists ? $rider->forceDelete() : null;
            Storage::disk('local')->delete([$licensePath, $orcrPath]);
            report($e);
            return back()->withErrors(['email' => 'The Rider could not be created because the activation email could not be delivered. Please try again.'])->withInput();
        }

        return back()->with('success', 'Rider created as Pending Activation. A 24-hour activation link was sent to the Rider.');
    }

    public function resendActivation(Request $request, CourierAccount $rider): RedirectResponse
    {
        $logistics = CurrentLogisticsAccount::resolve($request);
        abort_unless((int) $rider->logistics_account_id === (int) $logistics->id, 403);
        abort_unless($rider->account_status === 'pending_activation', 422);

        $token = Str::random(64);
        $rider->forceFill(['activation_token_hash' => hash('sha256', $token), 'activation_expires_at' => now()->addHours(24)])->save();

        try { $this->sendActivationEmail($rider, $token); }
        catch (\Throwable $e) { report($e); return back()->withErrors(['activation' => 'Unable to resend the activation email.']); }

        return back()->with('success', 'A new activation link was sent. The previous link is no longer valid.');
    }

    private function sendActivationEmail(CourierAccount $rider, string $token): void
    {
        $url = route('rider.activation.show', ['token' => $token]);
        Mail::raw("Your SARI Rider account is ready for activation. This link expires in 24 hours:\n\n{$url}\n\nIf you did not expect this invitation, ignore this email.", fn ($message) => $message->to($rider->email)->subject('Activate your SARI Rider account'));
    }
}
