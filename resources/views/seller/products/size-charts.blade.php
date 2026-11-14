@extends('layouts.seller')
@section('title','Product Size Charts — SARI')
@section('page-title','Product Size Charts')
@section('content')
<main style="max-width:1520px;margin:auto;padding:20px 24px 32px;font-family:Poppins,ui-sans-serif,system-ui,sans-serif;color:var(--sari-dark-e23e64dd1e, #2f3742)">
    <p style="font-size:10.5px;font-weight:800;letter-spacing:.14em;color:var(--sari-dark-685eb4fc40, #a87316)">PRODUCT MEASUREMENTS</p>
    <h1 style="margin:6px 0 8px;font-size:29px;font-weight:750;line-height:1.15">Product Size Charts</h1>
    <p style="font-size:12px;color:var(--sari-dark-a63aa16d7c, #8793a1);line-height:1.7">Enter seller-verified measurements for each product. Buyer AI matches these ranges to buyer measurements; it does not guess from height or weight.</p>
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;margin:20px 0">
        <input type="search" name="search" value="{{ $search }}" placeholder="Search product name..." aria-label="Search product name" style="flex:1;min-width:160px;height:48px;border:1px solid var(--sari-dark-d8c2716225, #dce2e8);border-radius:14px;padding:0 16px;background:var(--sari-dark-950baec030, white);font:inherit;font-size:12px">
        <button style="height:48px;border:1px solid #c88912;border-radius:14px;padding:0 20px;background:#c88912;color:white;font:inherit;font-size:12px;font-weight:700">Search</button>
        <a href="{{ route('seller.products.index') }}" style="align-self:center;font-size:12px;color:var(--sari-dark-a63aa16d7c, #59636e)">Back to Products</a>
    </form>
    <section style="background:var(--sari-dark-950baec030, white);border:1px solid var(--sari-dark-d8c2716225, #e5e7eb);border-radius:18px;overflow:hidden;box-shadow:0 3px 10px var(--sari-dark-7593018246, rgba(15,23,42,.035)),0 12px 30px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055))">
        <div style="padding:21px 23px;border-bottom:1px solid var(--sari-dark-d8c2716225, #e9edf1);font-size:18px;font-weight:750">Product Measurements</div>
        <div style="overflow-x:auto"><table style="width:100%;min-width:520px;border-collapse:collapse;text-align:left;font-size:12px">
            <thead style="background:var(--sari-dark-950baec030, #f8fafc);color:var(--sari-dark-a63aa16d7c, #8591a0);font-size:11px;text-transform:uppercase"><tr><th style="padding:14px 20px">Product</th><th>Chart</th><th style="padding:14px 20px;text-align:right">Action</th></tr></thead>
            <tbody>@forelse($products as $product)
                <tr style="border-top:1px solid var(--sari-dark-d8c2716225, #edf0f3)"><td style="padding:17px 20px"><strong>{{ $product->name }}</strong><p style="margin:4px 0 0;color:var(--sari-dark-a63aa16d7c, #8793a1);font-size:11px">{{ $product->category }}</p></td><td style="color:var(--sari-dark-a63aa16d7c, #667383)">{{ $product->chart_updated_at ? 'Structured chart saved' : 'Not configured' }}</td><td style="padding:17px 20px;text-align:right"><a href="{{ route('seller.products.size-chart.edit',$product) }}" style="color:var(--sari-dark-685eb4fc40, #a87316);font-weight:650">{{ $product->chart_updated_at ? 'Edit measurements' : 'Add measurements' }}</a></td></tr>
            @empty<tr><td colspan="3" style="padding:48px 20px;text-align:center;color:var(--sari-dark-a63aa16d7c, #8793a1)">No matching products.</td></tr>@endforelse</tbody>
        </table></div>
        <footer style="padding:16px 20px;border-top:1px solid var(--sari-dark-d8c2716225, #e9edf1)">{{ $products->links() }}</footer>
    </section>
</main>
@endsection
