<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use App\Models\SellerAccount;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerReviewController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->session()->get('is_seller'), 403);

        $seller = SellerAccount::findOrFail($request->session()->get('seller_account_id'));

        $reviews = ProductReview::query()
            ->with(['product', 'order', 'buyer', 'socialBuyer'])
            ->where('seller_account_id', $seller->id)
            ->latest('id')
            ->get();

        $average = $reviews->isEmpty() ? 0 : round((float) $reviews->avg('rating'), 2);

        return view('seller.reviews', compact('seller', 'reviews', 'average'));
    }
}
