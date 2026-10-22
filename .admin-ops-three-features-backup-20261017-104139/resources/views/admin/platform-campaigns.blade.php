@extends('layouts.admin')

@section('title', 'Events & Promotions — SARI Admin')
@section('page-title', 'Events & Promotions')

@section('content')
@php
    $formCampaign = $editing;
    $selectedSellerIds = collect(old('seller_ids', $formCampaign?->sellers?->pluck('id')->all() ?? []))->map(fn($id) => (int) $id)->all();
    $selectedProductIds = collect(old('product_ids', $formCampaign?->products?->pluck('id')->all() ?? []))->map(fn($id) => (int) $id)->all();
    $selectedCategories = collect(old('categories', $formCampaign?->categories?->pluck('category')->all() ?? []))->map(fn($value) => (string) $value)->all();
    $state = old('state', $formCampaign?->state ?? 'published');
    $scope = old('target_scope', $formCampaign?->target_scope ?? 'all');
    $saleEnabled = (bool) old('sale_enabled', $formCampaign?->sale_enabled ?? false);
    $voucherEnabled = (bool) old('voucher_enabled', $formCampaign?->voucher_enabled ?? false);
    $campaignTimezone = \App\Models\Promotions\PlatformCampaign::TIMEZONE;
    $activateImmediately = (bool) old('activate_immediately', $formCampaign ? false : true);
    $startsAt = old(
        'starts_at',
        $formCampaign?->starts_at?->copy()->timezone($campaignTimezone)->format('Y-m-d\TH:i')
            ?? now($campaignTimezone)->format('Y-m-d\TH:i')
    );
    $endsAt = old(
        'ends_at',
        $formCampaign?->ends_at?->copy()->timezone($campaignTimezone)->format('Y-m-d\TH:i')
            ?? now($campaignTimezone)->addDay()->format('Y-m-d\TH:i')
    );
@endphp

<div class="campaign-page mx-auto w-full max-w-[1500px] pb-8">
    <style>
        .campaign-page{--cp-gold:#D59617;--cp-gold-strong:#B97805;--cp-ink:#202124;--cp-muted:#697481;--cp-line:#DFE3E8;--cp-soft:#F8F9FA;font-family:'Poppins',sans-serif;color:var(--cp-ink)}
        .cp-head{padding-bottom:15px;border-bottom:1px solid var(--cp-line)}
        .cp-eyebrow{margin:0;color:#717B87;font-size:8px;font-weight:800;letter-spacing:.15em;text-transform:uppercase}
        .cp-title{margin:5px 0 0;font-size:clamp(29px,2.2vw,37px);font-weight:700;line-height:1.06;letter-spacing:-.045em}.cp-title span{color:var(--cp-gold)}
        .cp-copy{max-width:820px;margin:7px 0 0;color:var(--cp-muted);font-size:10px;line-height:1.65}
        .cp-time-strip{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:14px;border:1px solid #E5D6B3;border-radius:8px;background:#FFFAEF;padding:10px 12px}.cp-time-strip strong{font-size:9px;color:#7C5818}.cp-time-strip span{font-size:8px;color:#8A7654}.cp-immediate{grid-column:1/-1;display:flex;align-items:flex-start;gap:9px;border:1px solid #E7D7B4;border-radius:8px;background:#FFF9EC;padding:10px 11px}.cp-immediate input{width:15px;height:15px;margin-top:1px;accent-color:var(--cp-gold)}.cp-immediate strong{display:block;font-size:9px;color:#75531A}.cp-immediate span{display:block;margin-top:3px;color:#8A7654;font-size:7.6px;line-height:1.5}.cp-field input[readonly]{background:#F7F8FA;color:#8A929C;cursor:not-allowed}
        .cp-alert{margin-top:14px;border:1px solid #D9E8DD;border-radius:8px;background:#F4FAF6;padding:11px 13px;color:#4D7359;font-size:9px}
        .cp-error{border-color:#E8D1D1;background:#FFF7F7;color:#965C5C}
        .cp-layout{display:grid;grid-template-columns:minmax(0,1.08fr) minmax(410px,.92fr);gap:14px;margin-top:14px;align-items:start}
        .cp-panel{border:1px solid var(--cp-line);border-radius:8px;background:#fff;box-shadow:0 3px 7px rgba(15,23,42,.045),0 14px 32px rgba(15,23,42,.075)}
        .cp-panel-head{min-height:58px;padding:11px 14px;border-bottom:1px solid #ECEFF2}.cp-panel-head h2{margin:0;font-size:11px;font-weight:750}.cp-panel-head p{margin:3px 0 0;color:#74808D;font-size:8.4px;line-height:1.55}
        .cp-body{padding:14px}.cp-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}.cp-span-2{grid-column:1/-1}
        .cp-field label,.cp-label{display:block;margin-bottom:6px;color:#4B5563;font-size:8.8px;font-weight:750}.cp-field input,.cp-field select,.cp-field textarea{width:100%;border:1px solid #D6DCE2;border-radius:7px;background:#fff;color:#29313B;font:500 9.8px 'Poppins',sans-serif;outline:none;box-shadow:none}.cp-field input,.cp-field select{height:42px;padding:0 11px}.cp-field textarea{min-height:84px;resize:vertical;padding:10px 11px;line-height:1.55}.cp-field input:focus,.cp-field select:focus,.cp-field textarea:focus{border-color:var(--cp-gold);box-shadow:0 0 0 3px rgba(213,150,23,.08)}
        .cp-multi{min-height:126px!important;height:126px!important;padding:7px!important}.cp-help{margin-top:5px;color:#87919D;font-size:7.6px;line-height:1.5}
        .cp-section{grid-column:1/-1;border-top:1px solid #ECEFF2;padding-top:13px}.cp-section-head{display:flex;align-items:center;justify-content:space-between;gap:12px}.cp-section-head strong{font-size:10.5px}.cp-toggle{display:inline-flex;align-items:center;gap:7px;color:#56606C;font-size:8px;font-weight:700}.cp-toggle input{width:15px;height:15px;accent-color:var(--cp-gold)}
        .cp-actions{display:flex;justify-content:flex-end;gap:8px;border-top:1px solid #ECEFF2;margin-top:14px;padding-top:13px}.cp-btn{display:inline-flex;height:40px;align-items:center;justify-content:center;border-radius:7px;padding:0 14px;font-size:8.8px;font-weight:750;text-decoration:none;cursor:pointer}.cp-btn-primary{border:1px solid var(--cp-gold);background:var(--cp-gold);color:#fff}.cp-btn-primary:hover{border-color:var(--cp-gold-strong);background:var(--cp-gold-strong)}.cp-btn-secondary{border:1px solid #D6DCE2;background:#fff;color:#56606C}.cp-btn-danger{border:1px solid #E5CACA;background:#FFF8F8;color:#985C5C}
        .cp-list{display:grid;gap:9px;padding:12px}.cp-item{border:1px solid #E5E7EB;border-radius:8px;background:#fff;padding:12px}.cp-item-top{display:flex;align-items:flex-start;justify-content:space-between;gap:10px}.cp-item h3{margin:0;font-size:10.5px;font-weight:750}.cp-meta{margin-top:4px;color:#7B8692;font-size:7.8px;line-height:1.55}.cp-status{display:inline-flex;min-height:22px;align-items:center;border:1px solid #E5E7EB;border-radius:999px;background:#F8F9FA;padding:0 8px;font-size:6.8px;font-weight:750}.cp-status.Active{border-color:#D6E8DC;background:#F3F9F5;color:#4E765B}.cp-status.Scheduled{border-color:#EADCB9;background:#FFF9EC;color:#8C681F}.cp-status.Paused,.cp-status.Draft{color:#707984}.cp-benefits{display:flex;flex-wrap:wrap;gap:6px;margin-top:9px}.cp-chip{display:inline-flex;min-height:23px;align-items:center;border-radius:999px;background:#F7F8FA;padding:0 8px;color:#56606C;font-size:7px;font-weight:700}.cp-item-actions{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px}.cp-item-actions form{display:inline-flex}.cp-item-actions .cp-btn{height:31px;padding:0 10px;font-size:7.3px}.cp-empty{padding:32px 16px;color:#7B8692;font-size:9px;text-align:center}
        [data-target-panel][hidden],[data-benefit-panel][hidden]{display:none!important}
        @media(max-width:1100px){.cp-layout{grid-template-columns:1fr}.cp-panel-list{order:2}}
        @media(max-width:639px){.cp-grid{grid-template-columns:1fr}.cp-span-2,.cp-section{grid-column:auto}.cp-actions{align-items:stretch;flex-direction:column}.cp-btn{width:100%}.cp-item-top{flex-direction:column}.cp-title{font-size:clamp(28px,8vw,34px)}}
    </style>

    <div class="cp-head">
        <p class="cp-eyebrow">Marketing & Buyer Promotions</p>
        <h1 class="cp-title">Events & <span>Promotions</span></h1>
        <p class="cp-copy">Create scheduled SARI campaigns such as 1.1, 2.2, 11.11, and 12.12. Platform discounts only change what Buyers pay; Seller product prices remain untouched and Seller receivables are preserved separately.</p>
    </div>

    @if(session('success'))<div class="cp-alert">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="cp-alert cp-error">{{ $errors->first() }}</div>@endif

    <div class="cp-time-strip">
        <strong>Philippine Standard Time (PHT · UTC+8)</strong>
        <span id="campaignPhilippineClock">{{ now($campaignTimezone)->format('M d, Y · h:i:s A') }} PHT</span>
    </div>

    <div class="cp-layout">
        <section class="cp-panel">
            <div class="cp-panel-head"><h2>{{ $formCampaign ? 'Edit campaign' : 'Create campaign' }}</h2><p>New campaigns publish immediately by default. All Admin inputs, Buyer availability, and status checks use Philippine Standard Time.</p></div>
            <div class="cp-body">
                <form method="POST" action="{{ $formCampaign ? route('admin.platform-campaigns.update', $formCampaign) : route('admin.platform-campaigns.store') }}" data-campaign-form>
                    @csrf
                    @if($formCampaign) @method('PATCH') @endif
                    <div class="cp-grid">
                        <div class="cp-field cp-span-2"><label>Campaign name</label><input name="name" value="{{ old('name', $formCampaign?->name) }}" maxlength="140" required placeholder="12.12 SARI Mega Sale"></div>
                        <div class="cp-field cp-span-2"><label>Description</label><textarea name="description" maxlength="1200" placeholder="Short Buyer-facing campaign description">{{ old('description', $formCampaign?->description) }}</textarea></div>
                        <div class="cp-field"><label>State</label><select name="state"><option value="draft" @selected($state==='draft')>Draft</option><option value="published" @selected($state==='published')>Published / Scheduled</option><option value="paused" @selected($state==='paused')>Paused</option></select></div>
                        <div class="cp-field"><label>Target scope</label><select name="target_scope" data-target-scope><option value="all" @selected($scope==='all')>All Buyer products</option><option value="sellers" @selected($scope==='sellers')>Selected Sellers</option><option value="products" @selected($scope==='products')>Selected Products</option><option value="categories" @selected($scope==='categories')>Selected Categories</option></select></div>
                        <label class="cp-immediate">
                            <input type="hidden" name="activate_immediately" value="0">
                            <input type="checkbox" name="activate_immediately" value="1" data-activate-now @checked($activateImmediately)>
                            <span><strong>Activate immediately when Published</strong><span>The server uses the exact current Philippine time. No manual time adjustment is needed. Uncheck only when scheduling for later.</span></span>
                        </label>
                        <div class="cp-field"><label>Starts at <span class="cp-help">Philippine time</span></label><input name="starts_at" type="datetime-local" value="{{ $startsAt }}" data-starts-at @readonly($activateImmediately)></div>
                        <div class="cp-field"><label>Ends at <span class="cp-help">Philippine time</span></label><input name="ends_at" type="datetime-local" value="{{ $endsAt }}" required></div>

                        <div class="cp-field cp-span-2" data-target-panel="sellers" @hidden($scope!=='sellers')><label>Eligible Sellers</label><select class="cp-multi" name="seller_ids[]" multiple>@foreach($sellers as $seller)<option value="{{ $seller->id }}" @selected(in_array((int)$seller->id,$selectedSellerIds,true))>#{{ $seller->id }} · {{ $seller->store_name ?: $seller->email }}</option>@endforeach</select><p class="cp-help">Hold Ctrl/Command to select multiple Sellers.</p></div>
                        <div class="cp-field cp-span-2" data-target-panel="products" @hidden($scope!=='products')><label>Eligible Products</label><select class="cp-multi" name="product_ids[]" multiple>@foreach($products as $product)<option value="{{ $product->id }}" @selected(in_array((int)$product->id,$selectedProductIds,true))>#{{ $product->id }} · {{ $product->name }} · {{ $product->category ?: 'General' }}</option>@endforeach</select><p class="cp-help">Only the selected product IDs receive the Admin event price.</p></div>
                        <div class="cp-field cp-span-2" data-target-panel="categories" @hidden($scope!=='categories')><label>Eligible Categories</label><select class="cp-multi" name="categories[]" multiple>@foreach($categories as $category)<option value="{{ $category }}" @selected(in_array((string)$category,$selectedCategories,true))>{{ $category }}</option>@endforeach</select></div>

                        <div class="cp-section">
                            <div class="cp-section-head"><div><strong>Automatic Buyer sale</strong><p class="cp-help">Applies automatically to eligible Buyer product prices. Seller stored prices are not modified.</p></div><label class="cp-toggle"><input type="hidden" name="sale_enabled" value="0"><input type="checkbox" name="sale_enabled" value="1" data-benefit-toggle="sale" @checked($saleEnabled)> Enable sale</label></div>
                            <div class="cp-grid" data-benefit-panel="sale" @hidden(!$saleEnabled) style="margin-top:11px">
                                <div class="cp-field"><label>Discount type</label><select name="sale_discount_type"><option value="percentage" @selected(old('sale_discount_type',$formCampaign?->sale_discount_type)==='percentage')>Percentage</option><option value="fixed" @selected(old('sale_discount_type',$formCampaign?->sale_discount_type)==='fixed')>Fixed ₱ per unit</option></select></div>
                                <div class="cp-field"><label>Discount value</label><input name="sale_discount_value" type="number" min="0" step="0.01" value="{{ old('sale_discount_value',$formCampaign?->sale_discount_value ?? 10) }}"></div>
                                <div class="cp-field"><label>Maximum discount / unit</label><input name="sale_max_discount" type="number" min="0" step="0.01" value="{{ old('sale_max_discount',$formCampaign?->sale_max_discount) }}" placeholder="Optional"></div>
                            </div>
                        </div>

                        <div class="cp-section">
                            <div class="cp-section-head"><div><strong>SARI event voucher</strong><p class="cp-help">One platform voucher can be used per checkout. Usage and per-Buyer limits are validated server-side.</p></div><label class="cp-toggle"><input type="hidden" name="voucher_enabled" value="0"><input type="checkbox" name="voucher_enabled" value="1" data-benefit-toggle="voucher" @checked($voucherEnabled)> Enable voucher</label></div>
                            <div class="cp-grid" data-benefit-panel="voucher" @hidden(!$voucherEnabled) style="margin-top:11px">
                                <div class="cp-field"><label>Voucher code</label><input name="voucher_code" value="{{ old('voucher_code',$formCampaign?->voucher_code) }}" maxlength="64" placeholder="SARI1212" style="text-transform:uppercase"></div>
                                <div class="cp-field"><label>Discount type</label><select name="voucher_discount_type"><option value="fixed" @selected(old('voucher_discount_type',$formCampaign?->voucher_discount_type)==='fixed')>Fixed ₱</option><option value="percentage" @selected(old('voucher_discount_type',$formCampaign?->voucher_discount_type)==='percentage')>Percentage</option></select></div>
                                <div class="cp-field"><label>Discount value</label><input name="voucher_discount_value" type="number" min="0" step="0.01" value="{{ old('voucher_discount_value',$formCampaign?->voucher_discount_value ?? 100) }}"></div>
                                <div class="cp-field"><label>Minimum eligible spend</label><input name="voucher_minimum_spend" type="number" min="0" step="0.01" value="{{ old('voucher_minimum_spend',$formCampaign?->voucher_minimum_spend ?? 0) }}"></div>
                                <div class="cp-field"><label>Maximum voucher discount</label><input name="voucher_maximum_discount" type="number" min="0" step="0.01" value="{{ old('voucher_maximum_discount',$formCampaign?->voucher_maximum_discount) }}" placeholder="Optional"></div>
                                <div class="cp-field"><label>Total usage limit</label><input name="voucher_usage_limit" type="number" min="1" value="{{ old('voucher_usage_limit',$formCampaign?->voucher_usage_limit) }}" placeholder="Unlimited"></div>
                                <div class="cp-field"><label>Uses per Buyer</label><input name="voucher_per_buyer_limit" type="number" min="1" value="{{ old('voucher_per_buyer_limit',$formCampaign?->voucher_per_buyer_limit ?? 1) }}"></div>
                            </div>
                        </div>
                    </div>
                    <div class="cp-actions">@if($formCampaign)<a class="cp-btn cp-btn-secondary" href="{{ route('admin.platform-campaigns') }}">New campaign</a>@endif<button class="cp-btn cp-btn-primary" type="submit">{{ $formCampaign ? 'Save changes' : 'Create campaign' }}</button></div>
                </form>
            </div>
        </section>

        <section class="cp-panel cp-panel-list">
            <div class="cp-panel-head"><h2>Campaign schedule</h2><p>Buyer-only platform events. Newest scheduled campaigns appear first.</p></div>
            @if($campaigns->count())
                <div class="cp-list">
                    @foreach($campaigns as $campaign)
                        @php $status=$campaign->displayStatus(); @endphp
                        <article class="cp-item">
                            <div class="cp-item-top"><div><h3>{{ $campaign->name }}</h3><p class="cp-meta">{{ $campaign->startsAtManila()?->format('M d, Y h:i A') }} → {{ $campaign->endsAtManila()?->format('M d, Y h:i A') }} PHT<br>Scope: {{ ucfirst($campaign->target_scope) }}</p></div><span class="cp-status {{ $status }}">{{ $status }}</span></div>
                            <div class="cp-benefits">
                                @if($campaign->sale_enabled)<span class="cp-chip">Sale: {{ $campaign->sale_discount_type==='percentage' ? rtrim(rtrim(number_format((float)$campaign->sale_discount_value,2,'.',''),'0'),'.').'%' : '₱'.number_format((float)$campaign->sale_discount_value,2) }}</span>@endif
                                @if($campaign->voucher_enabled)<span class="cp-chip">Voucher: {{ $campaign->voucher_code }}</span><span class="cp-chip">Used {{ number_format((int)$campaign->voucher_used_count) }}{{ $campaign->voucher_usage_limit ? ' / '.number_format((int)$campaign->voucher_usage_limit) : '' }}</span>@endif
                                <span class="cp-chip">{{ $campaign->redemptions_count }} redemptions</span>
                            </div>
                            <div class="cp-item-actions">
                                <a class="cp-btn cp-btn-secondary" href="{{ route('admin.platform-campaigns',['edit'=>$campaign->id]) }}">Edit</a>
                                @if($campaign->state==='published')<form method="POST" action="{{ route('admin.platform-campaigns.pause',$campaign) }}">@csrf<button class="cp-btn cp-btn-secondary">Pause</button></form>@else<form method="POST" action="{{ route('admin.platform-campaigns.publish',$campaign) }}">@csrf<button class="cp-btn cp-btn-primary">Publish</button></form>@endif
                                @if($campaign->redemptions_count===0)<form method="POST" action="{{ route('admin.platform-campaigns.destroy',$campaign) }}" onsubmit="return confirm('Delete this campaign?')">@csrf @method('DELETE')<button class="cp-btn cp-btn-danger">Delete</button></form>@endif
                            </div>
                        </article>
                    @endforeach
                </div>
                @if($campaigns->hasPages())<div style="padding:0 12px 12px">{{ $campaigns->links() }}</div>@endif
            @else
                <div class="cp-empty">No platform campaigns yet. Create your first 1.1, 11.11, 12.12, payday, or anniversary event.</div>
            @endif
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    function initCampaignForm(){
        const form=document.querySelector('[data-campaign-form]'); if(!form||form.dataset.bound==='1')return; form.dataset.bound='1';
        const scope=form.querySelector('[data-target-scope]');
        const syncScope=()=>{const value=scope?.value||'all';form.querySelectorAll('[data-target-panel]').forEach(p=>p.hidden=p.dataset.targetPanel!==value)};
        scope?.addEventListener('change',syncScope); syncScope();
        form.querySelectorAll('[data-benefit-toggle]').forEach(toggle=>{const sync=()=>{const panel=form.querySelector('[data-benefit-panel="'+toggle.dataset.benefitToggle+'"]');if(panel)panel.hidden=!toggle.checked};toggle.addEventListener('change',sync);sync()});
        const activateNow=form.querySelector('[data-activate-now]');
        const startsAt=form.querySelector('[data-starts-at]');
        const syncStart=()=>{if(startsAt&&activateNow)startsAt.readOnly=activateNow.checked};
        activateNow?.addEventListener('change',syncStart); syncStart();
    }
    function updatePhilippineClock(){
        const node=document.getElementById('campaignPhilippineClock'); if(!node)return;
        node.textContent=new Intl.DateTimeFormat('en-PH',{
            timeZone:'Asia/Manila',month:'short',day:'2-digit',year:'numeric',
            hour:'2-digit',minute:'2-digit',second:'2-digit',hour12:true
        }).format(new Date())+' PHT';
    }
    document.addEventListener('DOMContentLoaded',()=>{initCampaignForm();updatePhilippineClock();setInterval(updatePhilippineClock,1000)},{once:true});
    document.addEventListener('livewire:navigated',()=>{initCampaignForm();updatePhilippineClock()});
})();
</script>
@endpush
