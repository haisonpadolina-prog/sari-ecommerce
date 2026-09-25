<?php
namespace App\Http\Controllers;

use App\Models\SellerAccount;
use App\Models\SellerInventoryMovement;
use App\Models\SellerProduct;
use App\Models\SellerProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SellerInventoryController extends Controller
{
    public function index(Request $request): View
    {
        $seller=$this->seller($request);
        $products=SellerProduct::query()
            ->with('activeVariants')
            ->where('seller_account_id',$seller->id)
            ->whereNull('archived_at')
            ->orderBy('name')
            ->paginate(20);

        $all=SellerProduct::query()->where('seller_account_id',$seller->id)->whereNull('archived_at');
        $stats=[
            'products'=>(clone $all)->count(),
            'units'=>(int) (clone $all)->sum('stock'),
            'low_stock'=>(clone $all)->whereColumn('stock','<=','low_stock_threshold')->where('stock','>',0)->count(),
            'out_of_stock'=>(clone $all)->where('stock','<=',0)->count(),
        ];

        $movements=SellerInventoryMovement::query()
            ->where('seller_account_id',$seller->id)
            ->latest()->limit(20)->get();

        return view('seller.inventory',compact('seller','products','stats','movements'));
    }

    public function adjust(Request $request, SellerProduct $product): RedirectResponse
    {
        $seller=$this->seller($request);
        abort_unless((int)$product->seller_account_id===(int)$seller->id,404);

        $validated=$request->validate([
            'variant_id'=>['nullable','integer'],
            'quantity'=>['required','integer','min:0','max:1000000'],
            'reason'=>['required','string','max:120'],
            'note'=>['nullable','string','max:500'],
        ]);

        if ($product->activeVariants()->exists() && empty($validated['variant_id'])) {
            return back()->withErrors(['variant_id'=>'Select a variant before adjusting stock for this product.']);
        }

        DB::transaction(function() use($product,$seller,$validated){
            $variant=null;
            if (!empty($validated['variant_id'])) {
                $variant=SellerProductVariant::query()
                    ->whereKey((int)$validated['variant_id'])
                    ->where('seller_product_id',$product->id)
                    ->lockForUpdate()->firstOrFail();
            }

            $target=$variant ?: SellerProduct::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();
            $before=(int)$target->stock;
            $after=(int)$validated['quantity'];
            $target->forceFill(['stock'=>$after])->save();

            if ($variant) {
                $total=(int)SellerProductVariant::query()
                    ->where('seller_product_id',$product->id)
                    ->where('is_active',true)->sum('stock');
                SellerProduct::query()->whereKey($product->id)->update(['stock'=>$total]);
            }

            SellerInventoryMovement::create([
                'seller_account_id'=>$seller->id,
                'seller_product_id'=>$product->id,
                'seller_product_variant_id'=>$variant?->id,
                'quantity_before'=>$before,
                'quantity_after'=>$after,
                'quantity_delta'=>$after-$before,
                'reason'=>$validated['reason'],
                'note'=>$validated['note'] ?? null,
            ]);
        });

        return back()->with('success','Inventory updated successfully.');
    }

    private function seller(Request $request): SellerAccount
    {
        $seller = $request->attributes->get('sellerAccount');
        if ($seller instanceof SellerAccount) {
            return $seller;
        }
        return SellerAccount::query()->findOrFail((int) $request->session()->get('seller_account_id'));
    }
}
