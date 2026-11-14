<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Accounts\SellerAccount;
use App\Models\Catalog\SellerProduct;
use App\Models\Catalog\SellerProductSizeChart;
use App\Services\Products\ProductSizeChartService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SellerProductSizeChartController extends Controller
{
    public function index(Request $request)
    {
        $seller = $this->seller($request);
        $search = trim((string) $request->query('search', ''));
        $products = SellerProduct::query()->where('seller_products.seller_account_id', $seller->id)
            ->whereNull('seller_products.archived_at')
            ->leftJoin('seller_product_size_charts as charts', 'charts.seller_product_id', '=', 'seller_products.id')
            ->select(['seller_products.id','seller_products.name','seller_products.category','charts.updated_at as chart_updated_at'])
            ->when($search !== '', fn ($query) => $query->where('seller_products.name','like','%'.$search.'%'))
            ->orderByDesc('seller_products.id')->paginate(15)->withQueryString();
        return view('seller.products.size-charts', compact('products','search'));
    }

    public function edit(Request $request, SellerProduct $product)
    {
        $this->owned($request, $product);
        $chart = SellerProductSizeChart::query()->where('seller_product_id',$product->id)->first();
        $sizeLabels = $this->labels($product);
        return view('seller.products.size-chart-edit', compact('product','chart','sizeLabels'));
    }

    public function update(Request $request, SellerProduct $product, ProductSizeChartService $charts)
    {
        $this->owned($request, $product);
        $request->validate(['chart_payload'=>['required','string','max:60000']]);
        $payload = json_decode($request->input('chart_payload'), true);
        if (!is_array($payload)) throw ValidationException::withMessages(['chart_payload'=>'Invalid size chart data.']);
        $validated = $charts->validate($payload, $this->labels($product));
        SellerProductSizeChart::query()->updateOrCreate(['seller_product_id'=>$product->id], $validated);
        return redirect()->route('seller.products.size-chart.edit', $product)->with('success','Size chart saved. Buyer size matching now uses these measurements.');
    }

    public function destroy(Request $request, SellerProduct $product)
    {
        $this->owned($request, $product);
        SellerProductSizeChart::query()->where('seller_product_id',$product->id)->delete();
        return redirect()->route('seller.products.size-chart.edit',$product)->with('success','Structured chart removed. Existing size chart images were kept.');
    }

    private function seller(Request $request): SellerAccount
    {
        abort_unless($request->session()->get('is_seller'),403);
        $seller = $request->attributes->get('sellerAccount') ?? SellerAccount::findOrFail((int)$request->session()->get('seller_account_id'));
        abort_unless(($seller->account_status ?: 'active') === 'active',403);
        return $seller;
    }

    private function owned(Request $request, SellerProduct $product): void
    {
        abort_unless((int)$product->seller_account_id === (int)$this->seller($request)->id,403);
        abort_if($product->archived_at !== null,404);
    }

    private function labels(SellerProduct $product): array
    {
        return $product->activeVariants()->get()->flatMap(function ($variant) {
            return collect((array)$variant->option_values)->filter(fn ($value,$key) => in_array(mb_strtolower(trim((string)$key)),['size','shoe size'],true))->values();
        })->filter(fn ($value) => (is_string($value) || is_int($value) || is_float($value)) && trim((string)$value) !== '')->map(fn ($value) => trim((string)$value))
            ->unique(fn ($value) => ProductSizeChartService::sizeKey($value))->values()->all();
    }
}
