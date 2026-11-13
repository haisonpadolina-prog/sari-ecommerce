<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Accounts\BuyerAccount;
use App\Models\Messaging\BuyerSellerMessage;
use App\Models\Orders\MarketplaceOrder;
use App\Models\Orders\MarketplaceOrderEvent;
use App\Models\Platform\SellerNotification;
use App\Services\Messaging\BuyerSellerOrderConfirmationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Mobile equivalent of web BuyerSellerMessageController::confirmAddress. */
class MobileBuyerAddressConfirmationController extends Controller
{
    public function __construct(private readonly BuyerSellerOrderConfirmationService $chat) {}

    public function confirm(Request $request, int $order): JsonResponse
    {
        /** @var BuyerAccount $buyer */
        $buyer = $request->user();

        $confirmed = DB::transaction(function () use ($buyer, $order): bool {
            $locked = MarketplaceOrder::query()
                ->whereKey($order)
                ->where('buyer_account_id', $buyer->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_if(in_array((string) $locked->status, ['cancelled', 'delivered'], true), 409,
                'This order can no longer be confirmed.');

            if ($locked->delivery_address_confirmed_at !== null) {
                return false;
            }

            $locked->forceFill(['delivery_address_confirmed_at' => now()])->save();
            $message = BuyerSellerMessage::create([
                'seller_account_id' => $locked->seller_account_id,
                'buyer_account_id' => $buyer->id,
                'buyer_social_account_id' => null,
                'marketplace_order_id' => $locked->id,
                'message_type' => 'address_confirmed',
                'sender_role' => 'buyer',
                'body' => 'I confirm that my delivery address and phone number for order '
                    .$locked->order_number.' are correct.',
            ]);

            MarketplaceOrderEvent::create([
                'marketplace_order_id' => $locked->id,
                'seller_account_id' => $locked->seller_account_id,
                'audience' => 'both',
                'type' => 'address_confirmed',
                'title' => 'Buyer confirmed delivery details',
                'message' => 'Delivery address and contact number confirmed by the buyer.',
                'status' => $locked->status,
            ]);

            SellerNotification::create([
                'seller_account_id' => $locked->seller_account_id,
                'type' => 'buyer_message',
                'title' => 'Delivery details confirmed',
                'message' => 'The buyer confirmed delivery details for order '.$locked->order_number.'.',
                'action_url' => route('seller.messages', ['buyer' => 'account-'.$buyer->id]),
            ]);

            $this->chat->announce($message);
            return true;
        }, 3);

        return response()->json([
            'success' => true,
            'data' => [
                'confirmed' => true,
                'already_confirmed' => !$confirmed,
            ],
            'message' => $confirmed ? 'Delivery details confirmed.' : 'Delivery details were already confirmed.',
        ])->header('Cache-Control', 'no-store');
    }
}
