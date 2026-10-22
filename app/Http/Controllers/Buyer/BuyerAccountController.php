<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\Buyer\BuyerIdentityService;
use App\Services\Shared\AccountProfileService;
use App\Models\Platform\PlatformComplaint;
use App\Models\Messaging\PlatformConversation;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BuyerAccountController extends Controller
{
    public function __construct(private readonly BuyerIdentityService $identity)
    {
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_buyer')) {
            return redirect()->route('login');
        }

        $this->identity->guard($request);

        $buyerAccount = $this->identity->account($request);

        $complaints = collect();
        $complaintConversations = collect();

        if ($buyerAccount) {
            $complaints = PlatformComplaint::query()
                ->where('reporter_role', 'buyer')
                ->whereRaw('LOWER(reporter_identifier) = ?', [strtolower((string) $buyerAccount->email)])
                ->latest('id')
                ->limit(5)
                ->get();

            if ($complaints->isNotEmpty()) {
                $complaintConversations = PlatformConversation::query()
                    ->where('context_type', 'platform_complaint')
                    ->whereIn('context_id', $complaints->pluck('id'))
                    ->pluck('uuid', 'context_id');
            }
        }

        return view('buyer.account', [
            'buyerAccount' => $buyerAccount,
            'socialAccount' => $this->identity->social($request),
            'profile' => $this->identity->snapshot($request),
            'addresses' => $buyerAccount
                ? $buyerAccount->addresses()
                    ->orderByDesc('is_default')
                    ->orderByDesc('updated_at')
                    ->orderByDesc('id')
                    ->get()
                : collect(),
            'complaints' => $complaints,
            'complaintConversations' => $complaintConversations,
        ]);
    }

    public function update(Request $request, AccountProfileService $profiles): RedirectResponse
    {
        $this->identity->guard($request);

        if ($buyer = $this->identity->account($request)) {
            $validated = $request->validate([
                'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'first_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[\pL\s.\'\-]+$/u'],
                'middle_initial' => ['nullable', 'string', 'max:2', 'regex:/^\pL{1,2}$/u'],
                'last_name' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[\pL\s.\'\-]+$/u'],
                'sex' => ['nullable', Rule::in(['Male', 'Female'])],
                'birthday' => [
                    'required',
                    'date',
                    'after_or_equal:1900-01-01',
                    'before_or_equal:' . now()->subYears(18)->toDateString(),
                ],
                'contact_no' => ['required', 'string', 'regex:/^(?:09\d{9}|\+639\d{9})$/'],
                'street_address' => ['required', 'string', 'max:500'],
            ], [
                'first_name.regex' => 'First name may only contain letters, spaces, apostrophes, periods, and hyphens.',
                'last_name.regex' => 'Last name may only contain letters, spaces, apostrophes, periods, and hyphens.',
                'middle_initial.regex' => 'Middle initial is optional and may contain up to two letters only.',
                'middle_initial.max' => 'Middle initial may contain up to two letters only.',
                'birthday.before_or_equal' => 'You must be 18 years old or older.',
                'contact_no.regex' => 'Enter a Philippine mobile number as 09XXXXXXXXX or +639XXXXXXXXX.',
            ]);

            unset($validated['profile_image']);

            $validated['first_name'] = $this->cleanName($validated['first_name']);
            $validated['last_name'] = $this->cleanName($validated['last_name']);
            $validated['middle_initial'] = filled($validated['middle_initial'] ?? null)
                ? mb_strtoupper(trim((string) $validated['middle_initial']))
                : null;

            // Registration currently allows Sex to be optional. If the user leaves
            // it blank, preserve the existing database value rather than trying to
            // write NULL into older buyer_accounts schemas where sex is required.
            if (blank($validated['sex'] ?? null)) {
                unset($validated['sex']);
            }

            $birthday = Carbon::parse($validated['birthday'])->startOfDay();
            $validated['birthday'] = $birthday->toDateString();
            $validated['age'] = $birthday->age;
            $validated['contact_no'] = $this->normalizeMobile((string) $validated['contact_no']);
            $validated['street_address'] = trim((string) $validated['street_address']);

            $profiles->save($buyer, $validated, $request);
            $buyer->refresh();

            $request->session()->put([
                'buyer_name' => trim($buyer->first_name . ' ' . $buyer->last_name),
                'buyer_email' => $buyer->email,
            ]);

            return back()->with('success', 'Buyer account information updated successfully.');
        }

        $social = $this->identity->social($request);
        abort_unless($social, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
        ]);

        $social->update(['name' => $validated['name']]);
        $request->session()->put('buyer_name', $social->name);

        return back()->with('success', 'Social buyer display name updated. Shipping details are entered during checkout.');
    }

    public function document(
        Request $request,
        string $document
    ): BinaryFileResponse {
        $this->identity->guard($request);

        $buyer = $this->identity->account($request);
        abort_unless($buyer, 404);

        $column = match ($document) {
            'id' => 'id_path',
            default => abort(404),
        };

        $path = (string) ($buyer->{$column} ?? '');

        abort_unless(
            $path !== '' && Storage::disk('local')->exists($path),
            404,
            'Verification document not found.'
        );

        return response()->file(
            Storage::disk('local')->path($path),
            ['Cache-Control' => 'private, no-store']
        );
    }

    private function normalizeMobile(string $value): string
    {
        $value = trim($value);

        if (str_starts_with($value, '+63')) {
            return '0' . substr($value, 3);
        }

        return preg_replace('/\D+/', '', $value) ?? '';
    }

    private function cleanName(string $value): string
    {
        return preg_replace('/\s+/u', ' ', trim($value)) ?: trim($value);
    }
}
