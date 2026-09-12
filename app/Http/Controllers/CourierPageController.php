<?php

namespace App\Http\Controllers;

use App\Models\CourierAccount;
use App\Models\LogisticsMessage;
use App\Models\MarketplaceOrder;
use App\Models\RiderEarning;
use App\Models\RiderPayoutRequest;
use App\Models\RiderPayoutRequestItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourierPageController extends Controller
{
    private const ACTIVE = ['courier_accepted','heading_pickup','arrived_pickup','in_transit','arrived_buyer'];

    private function guard(Request $request): void
    {
        abort_unless($request->session()->get('is_courier'), 403, 'Rider session required.');
    }

    private function courierAccount(Request $request): CourierAccount
    {
        $this->guard($request);
        $id = (int) $request->session()->get('courier_account_id');
        if ($id > 0 && ($account = CourierAccount::find($id))) {
            return $account;
        }

        $email = strtolower((string) $request->session()->get('courier_email', 'courier@gmail.com'));
        $account = CourierAccount::query()->whereRaw('LOWER(email) = ?', [$email])->first();
        if ($account) {
            $request->session()->put('courier_account_id', $account->id);
            return $account;
        }

        $account = CourierAccount::create([
            'registration_application_id' => null,
            'last_name' => 'Rider', 'first_name' => 'SARI', 'middle_initial' => null,
            'sex' => 'Male', 'email' => $email, 'contact_no' => '09123456789',
            'birthday' => '2000-01-01', 'age' => 26,
            'province_code' => 'TEST', 'province_name' => 'Metro Manila',
            'municipality_code' => 'TEST', 'municipality_name' => 'Manila',
            'barangay_code' => 'TEST', 'barangay_name' => 'Test Barangay',
            'street_address' => 'SARI Rider Test Address',
            'password' => Hash::make('courier123'),
            'vehicle_type' => 'Motorcycle', 'plate_number' => 'TEST-001',
            'account_status' => 'active', 'approved_at' => now(),
            'availability_status' => 'online', 'rating' => 5.00,
        ]);
        $request->session()->put('courier_account_id', $account->id);
        return $account;
    }

    private function ordersFor(CourierAccount $account)
    {
        return MarketplaceOrder::query()->whereRaw('LOWER(courier_email) = ?', [strtolower($account->email)]);
    }

    public function earnings(Request $request): View
    {
        $account = $this->courierAccount($request);

        $ledger = RiderEarning::query()
            ->with('order')
            ->where('courier_account_id', $account->id)
            ->orderByDesc('earned_at')
            ->get();

        $active = $this->ordersFor($account)->whereIn('status', self::ACTIVE)->get();
        $today = now()->startOfDay();
        $week = now()->startOfWeek();
        $month = now()->startOfMonth();

        $earnings = [
            'today' => (float) $ledger->filter(fn ($earning) => $earning->earned_at?->gte($today))->sum('delivery_fee_amount'),
            'week' => (float) $ledger->filter(fn ($earning) => $earning->earned_at?->gte($week))->sum('delivery_fee_amount'),
            'month' => (float) $ledger->filter(fn ($earning) => $earning->earned_at?->gte($month))->sum('delivery_fee_amount'),
            'pending' => (float) $active->sum('delivery_fee'),
            'available' => (float) $ledger->where('status', 'available')->sum('delivery_fee_amount'),
        ];

        $daily = collect(range(6, 0))->map(function (int $daysAgo) use ($ledger): array {
            $date = now()->subDays($daysAgo);

            return [
                'day' => $date->format('D'),
                'amount' => (float) $ledger
                    ->filter(fn ($earning) => $earning->earned_at?->isSameDay($date))
                    ->sum('delivery_fee_amount'),
            ];
        })->all();

        $transactions = $ledger->take(20)->map(fn ($earning): array => [
            'order' => $earning->order?->order_number ?: '—',
            'date' => $earning->earned_at?->format('M d, Y · h:i A') ?? '—',
            'type' => 'Delivery Fee',
            'amount' => (float) $earning->delivery_fee_amount,
            'status' => match ($earning->status) {
                'available' => 'Available',
                'reserved' => 'Reserved for payout',
                'paid' => 'Paid',
                default => ucfirst((string) $earning->status),
            },
        ])->values()->all();

        $previousWeekStart = now()->copy()->subWeek()->startOfWeek();
        $previousWeekEnd = now()->copy()->subWeek()->endOfWeek();
        $previousWeek = (float) $ledger
            ->filter(fn ($earning) => $earning->earned_at?->between($previousWeekStart, $previousWeekEnd))
            ->sum('delivery_fee_amount');

        $weeklyChange = $previousWeek > 0
            ? (($earnings['week'] - $previousWeek) / $previousWeek) * 100
            : null;

        $payoutRequests = RiderPayoutRequest::query()
            ->where('courier_account_id', $account->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('courier.earnings', compact(
            'earnings',
            'daily',
            'transactions',
            'weeklyChange',
            'payoutRequests'
        ));
    }

    public function history(Request $request): View
    {
        $account = $this->courierAccount($request);
        $history = $this->ordersFor($account)->whereIn('status',['delivered','cancelled'])->latest('updated_at')->limit(100)->get()->map(fn ($order): array => [
            'order' => $order->order_number,
            'customer' => $order->buyer_name,
            'route' => ($order->pickup_name ?: 'Seller') . ' → ' . ($order->buyer_name ?: 'Buyer'),
            'date' => ($order->delivered_at ?: $order->updated_at)?->format('M d, Y') ?? '—',
            'time' => ($order->delivered_at ?: $order->updated_at)?->format('h:i A') ?? '—',
            'fee' => (float) $order->delivery_fee,
            'status' => $order->status === 'delivered' ? 'Completed' : 'Cancelled',
            'is_today' => ($order->delivered_at ?: $order->updated_at)?->isToday() ?? false,
        ])->all();

        $completed = collect($history)->where('status','Completed');
        $historyStats = [
            'today' => $completed->where('is_today', true)->count(),
            'total' => $completed->count(),
            'success_rate' => count($history) > 0 ? round(($completed->count() / count($history)) * 100, 1) : 0,
            'average_fee' => $completed->count() > 0 ? (float) $completed->avg('fee') : 0,
        ];

        return view('courier.history', compact('history','historyStats'));
    }

    public function requestPayout(Request $request): RedirectResponse
    {
        $account = $this->courierAccount($request);

        return DB::transaction(function () use ($account): RedirectResponse {
            if (RiderPayoutRequest::query()
                ->where('courier_account_id', $account->id)
                ->where('status', 'pending')
                ->lockForUpdate()
                ->exists()) {
                return back()->withErrors([
                    'payout' => 'You already have a pending payout request.',
                ]);
            }

            $available = RiderEarning::query()
                ->where('courier_account_id', $account->id)
                ->where('status', 'available')
                ->orderBy('earned_at')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $amount = round((float) $available->sum('delivery_fee_amount'), 2);

            if ($amount <= 0 || $available->isEmpty()) {
                return back()->withErrors([
                    'payout' => 'No available rider earnings to request.',
                ]);
            }

            $payout = RiderPayoutRequest::query()->create([
                'courier_account_id' => $account->id,
                'amount' => $amount,
                'status' => 'pending',
            ]);

            foreach ($available as $earning) {
                RiderPayoutRequestItem::query()->create([
                    'rider_payout_request_id' => $payout->id,
                    'rider_earning_id' => $earning->id,
                    'amount' => (float) $earning->delivery_fee_amount,
                ]);

                $earning->forceFill([
                    'status' => 'reserved',
                ])->save();
            }

            return back()->with('success', 'Payout request submitted to Admin.');
        });
    }

    public function earningsStatement(Request $request): StreamedResponse
    {
        $account = $this->courierAccount($request);
        $earnings = RiderEarning::query()
            ->with('order')
            ->where('courier_account_id', $account->id)
            ->latest('earned_at')
            ->get();

        return response()->streamDownload(function () use ($earnings): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Order', 'Earned At', 'Buyer', 'Delivery Fee', 'Ledger Status']);

            foreach ($earnings as $earning) {
                fputcsv($out, [
                    $earning->order?->order_number,
                    $earning->earned_at?->toDateTimeString(),
                    $earning->order?->buyer_name,
                    $earning->delivery_fee_amount,
                    $earning->status,
                ]);
            }

            fclose($out);
        }, 'rider-earnings-statement.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function historyExport(Request $request): StreamedResponse
    {
        $account = $this->courierAccount($request);
        $orders = $this->ordersFor($account)->whereIn('status',['delivered','cancelled'])->latest('updated_at')->get();
        return response()->streamDownload(function () use ($orders): void {
            $out = fopen('php://output','w');
            fputcsv($out,['Order','Buyer','Status','Delivery Fee','Updated At']);
            foreach ($orders as $order) {
                fputcsv($out,[$order->order_number,$order->buyer_name,$order->status,$order->delivery_fee,$order->updated_at?->toDateTimeString()]);
            }
            fclose($out);
        }, 'rider-delivery-history.csv', ['Content-Type'=>'text/csv']);
    }

    public function messages(Request $request): View
    {
        $account = $this->courierAccount($request);
        LogisticsMessage::query()->where('courier_account_id',$account->id)->where('sender_role','logistics')->whereNull('read_at')->update(['read_at'=>now()]);
        $messages = LogisticsMessage::query()->where('courier_account_id',$account->id)->oldest('id')->get();
        return view('courier.messages', compact('account','messages'));
    }

    public function sendMessage(Request $request): RedirectResponse
    {
        $account = $this->courierAccount($request);
        $validated = $request->validate(['body'=>['required','string','max:3000']]);
        LogisticsMessage::create([
            'courier_account_id'=>$account->id,
            'sender_role'=>'rider',
            'body'=>$validated['body'],
        ]);
        return back()->with('success','Message sent to Logistics.');
    }

    public function profile(Request $request): View
    {
        $account = $this->courierAccount($request);
        $completed = $this->ordersFor($account)->where('status','delivered')->count();
        $profile = [
            'full_name' => trim($account->first_name.' '.$account->last_name),
            'email' => $account->email,
            'phone' => $account->contact_no,
            'address' => trim($account->street_address.', '.$account->barangay_name.', '.$account->municipality_name.', '.$account->province_name, ', '),
            'vehicle_type' => $account->vehicle_type,
            'vehicle_model' => $account->vehicle_model ?: '—',
            'plate_number' => $account->plate_number,
            'license_number' => $account->license_number ?: '—',
            'rating' => number_format((float) ($account->rating ?? 5), 2),
            'completed_deliveries' => $completed,
            'joined' => $account->created_at?->format('F Y') ?? '—',
            'availability_status' => $account->availability_status ?: 'online',
        ];
        return view('courier.profile', compact('account','profile'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $account = $this->courierAccount($request);
        $validated = $request->validate([
            'first_name'=>['required','string','max:120'],
            'last_name'=>['required','string','max:120'],
            'contact_no'=>['required','string','max:30'],
            'email'=>['required','email','max:255',Rule::unique('courier_accounts','email')->ignore($account->id)],
            'street_address'=>['required','string','max:1000'],
            'vehicle_type'=>['required','string','max:120'],
            'vehicle_model'=>['nullable','string','max:120'],
            'plate_number'=>['required','string','max:120'],
            'license_number'=>['nullable','string','max:120'],
            'current_password'=>['nullable','string'],
            'password'=>['nullable','string','min:8','confirmed'],
        ]);

        if (!empty($validated['password'])) {
            if (!Hash::check((string) ($validated['current_password'] ?? ''), $account->password)) {
                return back()->withErrors(['current_password'=>'Current password is incorrect.']);
            }
            $account->password = Hash::make($validated['password']);
        }

        $account->fill([
            'first_name'=>$validated['first_name'], 'last_name'=>$validated['last_name'],
            'contact_no'=>$validated['contact_no'], 'email'=>strtolower($validated['email']),
            'street_address'=>$validated['street_address'], 'vehicle_type'=>$validated['vehicle_type'],
            'vehicle_model'=>$validated['vehicle_model'] ?? null, 'plate_number'=>$validated['plate_number'],
            'license_number'=>$validated['license_number'] ?? null,
        ])->save();
        $request->session()->put('courier_email',$account->email);
        $request->session()->put('courier_name',trim($account->first_name.' '.$account->last_name));
        return back()->with('success','Rider profile updated.');
    }

    public function availability(Request $request): RedirectResponse
    {
        $account = $this->courierAccount($request);
        $validated = $request->validate(['availability_status'=>['required',Rule::in(['online','offline'])]]);
        $account->update(['availability_status'=>$validated['availability_status']]);
        return back()->with('success','Availability updated.');
    }
}
