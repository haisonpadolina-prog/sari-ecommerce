<?php

namespace App\Http\Middleware\Finance;

use App\Support\CurrentLogisticsAccount;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/** Blocks only NEW COD dispatch, never remittance, existing routes or login. */
class EnsureRiderCodAssignmentAllowed
{
    public function handle(Request $request, Closure $next): Response
    {
        $riderId = (int) $request->input('rider_id', 0);
        if ($riderId <= 0) return $next($request);
        $orderIds = collect($request->input('order_ids', []))->map(fn ($v) => (int) $v)->filter()->unique();
        if ($orderIds->isEmpty() && $request->route('order')) {
            $order = $request->route('order');
            $orderIds = collect([(int) (is_object($order) ? $order->getKey() : $order)]);
        }
        if ($orderIds->isEmpty()) return $next($request);
        $hasCod = DB::table('marketplace_orders')->whereIn('id',$orderIds)
            ->whereRaw('UPPER(payment_method) = ?', ['COD'])->exists();
        if (! $hasCod) return $next($request);
        $logistics = CurrentLogisticsAccount::resolve($request);
        $restricted = DB::table('cod_custody_cases')
            ->where('logistics_account_id',$logistics->id)
            ->where('courier_account_id',$riderId)
            ->where('status','open')
            ->whereNotNull('cod_restricted_at')->exists();
        if ($restricted) {
            return back()->withErrors(['rider_id' => 'New COD assignments are restricted pending unresolved Rider cash remittance review. Select another eligible Rider or resolve the COD compliance case.']);
        }
        return $next($request);
    }
}
