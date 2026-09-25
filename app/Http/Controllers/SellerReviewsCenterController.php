<?php
namespace App\Http\Controllers;

use App\Models\ProductReview;
use App\Models\SellerAccount;
use App\Models\SellerReviewReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerReviewsCenterController extends Controller
{
    public function index(Request $request): View
    {
        $seller=$this->seller($request);
        $reviews=ProductReview::query()
            ->with(['product','order','buyer','socialBuyer','sellerReply'])
            ->where('seller_account_id',$seller->id)
            ->latest()->paginate(20);
        $base=ProductReview::query()->where('seller_account_id',$seller->id);
        $stats=[
            'count'=>(clone $base)->count(),
            'average'=>round((float)((clone $base)->avg('rating') ?: 0),2),
            'five'=>(clone $base)->where('rating',5)->count(),
            'low'=>(clone $base)->where('rating','<=',2)->count(),
        ];
        return view('seller.reviews-center',compact('seller','reviews','stats'));
    }

    public function reply(Request $request, ProductReview $review): RedirectResponse
    {
        $seller=$this->seller($request);
        abort_unless((int)$review->seller_account_id===(int)$seller->id,404);
        $validated=$request->validate(['reply'=>['required','string','max:1500']]);
        SellerReviewReply::updateOrCreate(
            ['product_review_id'=>$review->id],
            ['seller_account_id'=>$seller->id,'reply'=>$validated['reply']]
        );
        return back()->with('success','Seller reply saved.');
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
