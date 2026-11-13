<?php

namespace App\Http\Middleware\Api;

use App\Models\Accounts\BuyerAccount;
use App\Models\Platform\AdminUserActivity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

/** Mirror successful mobile Buyer actions in the same activity source used by web. */
final class RecordMobileBuyerActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = $request->user();
        if (!$user instanceof BuyerAccount || $response->getStatusCode() >= 400
            || !in_array($request->method(), ['POST','PATCH','DELETE'], true)) {
            return $response;
        }
        $path = $request->path();
        $action = match (true) {
            preg_match('#/buyer/profile$#', $path) === 1 => 'buyer_account_update',
            preg_match('#/buyer/addresses$#', $path) === 1 && $request->isMethod('POST') => 'buyer_account_addresses_store',
            preg_match('#/buyer/addresses/\d+$#', $path) === 1 && $request->isMethod('PATCH') => 'buyer_account_addresses_update',
            preg_match('#/buyer/addresses/\d+/default$#', $path) === 1 => 'buyer_account_addresses_default',
            preg_match('#/buyer/addresses/\d+$#', $path) === 1 && $request->isMethod('DELETE') => 'buyer_account_addresses_destroy',
            preg_match('#/buyer/cart/items$#', $path) === 1 => 'buyer_cart_items_store',
            preg_match('#/buyer/cart/items/\d+$#', $path) === 1 && $request->isMethod('PATCH') => 'buyer_cart_items_update',
            preg_match('#/buyer/cart/items/\d+$#', $path) === 1 && $request->isMethod('DELETE') => 'buyer_cart_items_destroy',
            preg_match('#/buyer/checkout$#', $path) === 1 => 'buyer_checkout_store',
            preg_match('#/buyer/orders/\d+/cancel$#', $path) === 1 => 'buyer_orders_cancel',
            preg_match('#/buyer/orders/\d+/reviews$#', $path) === 1 => 'buyer_orders_reviews_store',
            preg_match('#/buyer/messages/\d+$#', $path) === 1 && $request->isMethod('POST') => 'buyer_messages_send',
            default => null,
        };
        try {
        if ($action !== null && Schema::hasTable('admin_user_activities')) {
            AdminUserActivity::query()->create([
                'admin_account_id'=>null,'user_role'=>'buyer','user_id'=>(int)$user->id,
                'action'=>$action,'description'=>'Buyer action submitted from SARI mobile.',
                'metadata'=>['source'=>'mobile','method'=>$request->method(),
                    'path'=>'/'.$request->path(),'response_status'=>$response->getStatusCode()],
            ]);
        }
        } catch (Throwable $e) {
            // Log failure must never change an already completed checkout/order response.
            Log::warning('Mobile activity event could not be saved', ['exception'=>get_class($e)]);
        }
        return $response;
    }
}
