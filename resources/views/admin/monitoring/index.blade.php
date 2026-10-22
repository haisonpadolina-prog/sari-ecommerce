@extends('layouts.admin')
@section('title', 'Platform Monitoring — SARI Admin')
@section('page-title', 'Platform Monitoring')
@section('content')
@php
    $sections = [
        'sellers' => ['label' => 'Stores & Sellers', 'count' => $overview['sellers']],
        'logistics' => ['label' => 'Logistics Providers', 'count' => $overview['logistics']],
        'buyers' => ['label' => 'Buyers', 'count' => $overview['buyers']],
        'riders' => ['label' => 'Riders', 'count' => $overview['riders']],
        'sla' => ['label' => 'Delivery SLA', 'count' => $slaSummary['breached']],
    ];
    $statusLabel = static fn ($value) => match (strtolower((string) $value)) {
        'deactivated' => 'Suspended',
        'suspended' => 'Timed suspension',
        'blocked' => 'Blocked',
        'banned' => 'Banned',
        'pending_activation' => 'Pending activation',
        default => ucfirst(str_replace('_', ' ', (string) ($value ?: 'active'))),
    };
@endphp
<div class="pm-page mx-auto w-full max-w-[1500px] pb-8">
    <style>
        .pm-page{--pm-gold:#D59617;--pm-gold-strong:#B97805;--pm-ink:#202124;--pm-muted:#697481;--pm-line:#DFE3E8;--pm-soft:#F7F8FA;font-family:'Poppins',ui-sans-serif,system-ui,sans-serif;color:var(--pm-ink)}
        .pm-header{padding-bottom:15px;border-bottom:1px solid var(--pm-line)}
        .pm-eyebrow{margin:0;color:#717B87;font-size:8px;font-weight:800;letter-spacing:.15em;text-transform:uppercase}
        .pm-title{margin:5px 0 0;font-size:clamp(29px,2.2vw,37px);font-weight:700;line-height:1.06;letter-spacing:-.045em}
        .pm-title span{color:var(--pm-gold)}
        .pm-copy{max-width:820px;margin:7px 0 0;color:var(--pm-muted);font-size:10px;line-height:1.65}
        .pm-metrics{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:10px;margin-top:14px}
        .pm-metric{min-height:72px;border:1px solid var(--pm-line);border-radius:8px;background:#fff;padding:12px 13px;box-shadow:0 1px 3px rgba(15,23,42,.025),0 7px 18px rgba(15,23,42,.045)}
        .pm-metric span{display:block;color:#74808D;font-size:7.5px;font-weight:700;text-transform:uppercase;letter-spacing:.08em}
        .pm-metric strong{display:block;margin-top:7px;color:#2D333B;font-size:20px;line-height:1;font-weight:700}
        .pm-tabs{display:flex;gap:6px;margin-top:14px;overflow:auto;padding-bottom:2px}
        .pm-tab{display:inline-flex;min-height:36px;align-items:center;gap:7px;border:1px solid #DDE2E7;border-radius:8px;background:#fff;padding:0 11px;color:#606A75;font-size:8.8px;font-weight:700;text-decoration:none;white-space:nowrap}
        .pm-tab:hover{border-color:#D9BE83;background:#F3F4F6;color:#8F6418}
        .pm-tab.is-active{border-color:var(--pm-gold);background:var(--pm-gold);color:#fff}
        .pm-tab b{display:inline-flex;min-width:20px;height:20px;align-items:center;justify-content:center;border-radius:999px;background:rgba(255,255,255,.16);padding:0 6px;font-size:7px}
        .pm-filter{display:grid;grid-template-columns:minmax(280px,1fr) 180px auto auto;gap:9px;align-items:end;margin-top:14px}
        .pm-field label{display:block;margin-bottom:6px;color:#4B5563;font-size:8.8px;font-weight:750}
        .pm-control{width:100%;height:42px;border:1px solid #D6DCE2;border-radius:7px;background:#fff;padding:0 11px;color:#29313B;font:500 9.8px 'Poppins',sans-serif;outline:none}
        .pm-control:focus{border-color:var(--pm-gold);box-shadow:0 0 0 3px rgba(213,150,23,.08)}
        .pm-btn{display:inline-flex;height:42px;align-items:center;justify-content:center;border-radius:7px;padding:0 14px;font-size:8.8px;font-weight:750;text-decoration:none;white-space:nowrap}
        .pm-btn.primary{border:1px solid var(--pm-gold);background:var(--pm-gold);color:#fff}
        .pm-btn.primary:hover{background:var(--pm-gold-strong);border-color:var(--pm-gold-strong)}
        .pm-btn.secondary{border:1px solid #D6DCE2;background:#fff;color:#56606C}
        .pm-panel{margin-top:14px;overflow:hidden;border:1px solid var(--pm-line);border-radius:8px;background:#fff;box-shadow:0 3px 7px rgba(15,23,42,.04),0 14px 32px rgba(15,23,42,.075),0 30px 68px rgba(15,23,42,.07)}
        .pm-panel-head{display:flex;min-height:58px;align-items:center;justify-content:space-between;gap:12px;padding:11px 14px;border-bottom:1px solid #ECEFF2}
        .pm-panel-head h2{margin:0;color:#303844;font-size:11px;font-weight:750}
        .pm-panel-head p{margin:3px 0 0;color:#74808D;font-size:8.4px;line-height:1.55}
        .pm-count{display:inline-flex;min-height:22px;align-items:center;border:1px solid #E5E7EB;border-radius:999px;background:#F8F9FA;padding:0 7px;color:#6E675F;font-size:6.8px;font-weight:700}
        .pm-table-wrap{overflow:auto}
        .pm-table{width:100%;min-width:1050px;border-collapse:collapse}
        .pm-table th{padding:10px 12px;border-bottom:1px solid #ECEFF2;background:#FAFBFC;color:#717B87;font-size:7.5px;font-weight:800;letter-spacing:.06em;text-align:left;text-transform:uppercase;white-space:nowrap}
        .pm-table td{padding:12px;border-bottom:1px solid #F0F2F4;background:#fff;color:#46505D;font-size:8.6px;line-height:1.5;vertical-align:middle}
        .pm-table tbody tr:hover td{background:#F3F4F6}
        .pm-table tbody tr:last-child td{border-bottom:0}
        .pm-name{color:#28303A;font-size:9.2px;font-weight:750}
        .pm-sub{margin-top:2px;color:#7D8792;font-size:7.5px}
        .pm-status{display:inline-flex;min-height:23px;align-items:center;border:1px solid #DCE7DF;border-radius:999px;background:#F5F9F6;padding:0 8px;color:#567362;font-size:6.8px;font-weight:750;white-space:nowrap}
        .pm-status.deactivated,.pm-status.suspended{border-color:#E8DDC9;background:#FFF9EF;color:#8B6B32}
        .pm-status.blocked{border-color:#EAD7CB;background:#FFF6F1;color:#9A5E42}
        .pm-status.banned{border-color:#E7C9C9;background:#FFF3F3;color:#A24F4F}
        .pm-sla{display:inline-flex;min-height:23px;align-items:center;border:1px solid #DDE3E8;border-radius:999px;background:#F7F9FA;padding:0 8px;color:#68737E;font-size:6.8px;font-weight:800;white-space:nowrap}
        .pm-sla.warning{border-color:#E9D9AA;background:#FFFAEC;color:#8A6925}
        .pm-sla.high{border-color:#E8CFBC;background:#FFF7F1;color:#965B38}
        .pm-sla.critical{border-color:#E6C1C1;background:#FFF3F3;color:#A24747}
        .pm-sla.normal{border-color:#D8E5DC;background:#F5FAF6;color:#567462}
        .pm-view{display:inline-flex;height:31px;align-items:center;border:1px solid #DEC98E;border-radius:7px;background:#FFF9EC;padding:0 10px;color:#8D641C;font-size:7.5px;font-weight:750;text-decoration:none}
        .pm-pagination{padding:10px 14px;border-top:1px solid #ECEFF2;background:#FAFBFC}
        .pm-pagination nav{font-size:9px}
        .pm-empty{padding:40px 18px;color:#7D8792;font-size:9px;text-align:center}
        @media(max-width:1000px){.pm-metrics{grid-template-columns:repeat(2,minmax(0,1fr))}.pm-filter{grid-template-columns:1fr 1fr}.pm-filter .pm-field:first-child{grid-column:1/-1}}
        @media(max-width:639px){.pm-metrics{grid-template-columns:1fr 1fr}.pm-filter{grid-template-columns:1fr}.pm-filter .pm-field:first-child{grid-column:auto}.pm-btn{width:100%}}
    
.pm-page .pm-metrics { gap:12px; }
.pm-page .pm-metric {position:relative;min-height:86px;padding:15px 16px;border:1px solid #E4E7EC;border-radius:12px;background:#fff;box-shadow:0 2px 6px rgba(16,24,40,.04);}
.pm-page .pm-metric > span:first-child {padding-right:34px;color:#667085;font-size:10px;font-weight:600;line-height:1.35;letter-spacing:normal;text-transform:none;}
.pm-page .pm-metric strong {margin-top:5px;color:#101828;font-size:24px;line-height:1;letter-spacing:-.035em;}
.pm-page .pm-metric > .pm-metric-icon {position:absolute;top:14px;right:16px;display:inline-flex;width:25px;height:25px;padding:0;background:transparent;border:0;border-radius:0;box-shadow:none;pointer-events:none;}
.pm-page .pm-metric-icon svg {display:block;width:25px;height:25px;}
.pm-page .pm-view {justify-content:center;min-height:34px;height:auto;padding:8px 12px;border:1px solid var(--pm-gold);border-radius:8px;background:var(--pm-gold);color:#fff;font-size:10px;font-weight:600;line-height:1.4;text-align:center;text-decoration:none;}
.pm-page .pm-view:hover {background:var(--pm-gold-strong);border-color:var(--pm-gold-strong);color:#fff;}
.pm-page .pm-view:focus-visible {outline:2px solid var(--pm-gold-strong);outline-offset:3px;}
@media (min-width:1001px) and (max-width:1279px) {.pm-page .pm-metrics {grid-template-columns:repeat(3,minmax(0,1fr));}}

/* Monitoring section selection uses charcoal. */
.pm-page .pm-tab.is-active,
.pm-page .pm-tab.is-active:hover {
    background: #252A32;
    border-color: #252A32;
    color: #FFFFFF;
}
.pm-page .pm-tab:not(.is-active):hover {
    background: #F3F4F6;
    border-color: #CBD2DB;
    color: #252A32;
}
.pm-page .pm-tab.is-active b {
    background: rgba(255,255,255,.14);
    color: #FFFFFF;
}
.pm-page .pm-tab:focus-visible {
    outline: 2px solid #475467;
    outline-offset: 3px;
}
</style>
    <div class="pm-header">
        <p class="pm-eyebrow">Operations & Account Oversight</p>
        <h1 class="pm-title">Platform <span>Monitoring</span></h1>
        <p class="pm-copy">Monitor SARI stores, Logistics providers and hubs, Buyers, Riders, account access, and delivery SLA breaches from one administrator workspace.</p>
    </div>
    @if(session('success'))
        <div class="mt-3 rounded-[8px] border border-[#d7e7dc] bg-[#f5faf6] px-3 py-2 text-[9px] font-semibold text-[#51715d]">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mt-3 rounded-[8px] border border-[#ead2cc] bg-[#fff6f3] px-3 py-2 text-[9px] text-[#9a5a49]">{{ $errors->first() }}</div>
    @endif
    <div class="pm-metrics">
        <div class="pm-metric"><span>Stores</span><strong>{{ number_format($overview['sellers']) }}</strong><span class="pm-metric-icon" style="color:#C47522" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18l-2-5H5L3 9ZM5 9v11h14V9M9 20v-6h6v6"/></svg></span></div>
        <div class="pm-metric"><span>Logistics</span><strong>{{ number_format($overview['logistics']) }}</strong><span class="pm-metric-icon" style="color:#7C3AED" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h11v12H3zM14 10h4l3 3v5h-7"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg></span></div>
        <div class="pm-metric"><span>Buyers</span><strong>{{ number_format($overview['buyers']) }}</strong><span class="pm-metric-icon" style="color:#2563EB" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.5"/><path d="M5 21a7 7 0 0 1 14 0"/></svg></span></div>
        <div class="pm-metric"><span>Riders</span><strong>{{ number_format($overview['riders']) }}</strong><span class="pm-metric-icon" style="color:#0F766E" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="17" r="3"/><circle cx="19" cy="17" r="3"/><path d="M8 17h8l-4-7H8M15 5h3l2 8M10 7h3"/></svg></span></div>
        <div class="pm-metric"><span>Active deliveries</span><strong>{{ number_format($overview['active_deliveries']) }}</strong><span class="pm-metric-icon" style="color:#AC7A16" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5ZM3 8v10l9 4 9-4V8M12 13v9"/></svg></span></div>
        <div class="pm-metric"><span>SLA breached</span><strong>{{ number_format($slaSummary['breached']) }}</strong><span class="pm-metric-icon" style="color:#C45454" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 10 18H2L12 3ZM12 9v5M12 17h.01"/></svg></span></div>
    </div>
    <div class="pm-tabs" role="tablist" aria-label="Monitoring sections">
        @foreach($sections as $key => $meta)
            <a class="pm-tab {{ $section === $key ? 'is-active' : '' }}" href="{{ route('admin.monitoring', ['section' => $key]) }}">
                {{ $meta['label'] }} <b>{{ number_format($meta['count']) }}</b>
            </a>
        @endforeach
    </div>
    <form method="GET" action="{{ route('admin.monitoring') }}" class="pm-filter">
        <input type="hidden" name="section" value="{{ $section }}">
        <div class="pm-field">
            <label for="monitorSearch">Search</label>
            <input
                id="monitorSearch"
                class="pm-control"
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="{{ $section === 'sla' ? 'Order, Seller, Logistics, Rider...' : 'Name, email, store, business, plate, location...' }}"
            >
        </div>
        @if($section !== 'sla')
            <div class="pm-field">
                <label for="monitorStatus">Account status</label>
                <select id="monitorStatus" class="pm-control" name="status">
                    <option value="all" @selected($status === 'all')>All statuses</option>
                    <option value="active" @selected($status === 'active')>Active</option>
                    <option value="deactivated" @selected($status === 'deactivated')>Suspended</option>
                    <option value="blocked" @selected($status === 'blocked')>Blocked</option>
                    <option value="banned" @selected($status === 'banned')>Banned</option>
                </select>
            </div>
        @else
            <div class="pm-field">
                <label>SLA summary</label>
                <div class="pm-control" style="display:flex;align-items:center;gap:8px">
                    <span>{{ number_format($slaSummary['critical']) }} critical</span>
                    <span>·</span>
                    <span>{{ number_format($slaSummary['warning']) }} nearing breach</span>
                </div>
            </div>
        @endif
        <button class="pm-btn primary" type="submit">Apply</button>
        <a class="pm-btn secondary" href="{{ route('admin.monitoring', ['section' => $section]) }}">Reset</a>
    </form>
    <section class="pm-panel">
        <div class="pm-panel-head">
            <div>
                <h2>{{ $sections[$section]['label'] }}</h2>
                <p>
                    {{ $section === 'sla'
                        ? 'One SLA engine is shared with Security Center. Review active delivery stages, thresholds, elapsed time, and breaches here.'
                        : 'Open a record to review profile, operations, orders, delivery activity, products, hubs, and administrative controls.' }}
                </p>
            </div>
            <span class="pm-count">
                {{ number_format($section === 'sla' ? $records->count() : $records->total()) }}
                {{ $section === 'sla' ? 'active orders' : 'records' }}
            </span>
        </div>
        <div class="pm-table-wrap">
            @if($records->count())
                <table class="pm-table">
                    @if($section === 'sla')
                        <thead><tr><th>Order</th><th>Stage</th><th>Elapsed</th><th>SLA</th><th>Seller</th><th>Logistics</th><th>Rider</th><th>Stage started</th></tr></thead>
                        <tbody>
                        @foreach($records as $record)
                            <tr>
                                <td>
                                    <div class="pm-name">{{ $record['order_number'] }}</div>
                                    <div class="pm-sub">Last update {{ $record['last_updated_at']?->diffForHumans() ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="pm-name">{{ $record['status_label'] }}</div>
                                    <div class="pm-sub">{{ $record['threshold_hours'] }}h expected window</div>
                                </td>
                                <td>
                                    <div class="pm-name">{{ number_format((float)$record['elapsed_hours'],1) }}h</div>
                                    <div class="pm-sub">
                                        @if($record['is_breached'])
                                            {{ number_format((float)$record['overdue_hours'],1) }}h overdue
                                        @else
                                            {{ number_format(max(0,(float)$record['threshold_hours']-(float)$record['elapsed_hours']),1) }}h remaining
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="pm-sla {{ $record['severity'] }}">
                                        {{ $record['is_breached'] ? strtoupper($record['severity']).' BREACH' : ($record['severity']==='warning' ? 'WARNING' : 'ON TRACK') }}
                                    </span>
                                </td>
                                <td>
                                    @if($record['seller_id'])
                                        <a class="pm-view" href="{{ route('admin.monitoring.sellers.show',$record['seller_id']) }}">{{ $record['seller_name'] }}</a>
                                    @else
                                        {{ $record['seller_name'] }}
                                    @endif
                                </td>
                                <td>
                                    @if($record['logistics_id'])
                                        <a class="pm-view" href="{{ route('admin.monitoring.logistics.show',$record['logistics_id']) }}">{{ $record['logistics_name'] }}</a>
                                    @else
                                        {{ $record['logistics_name'] }}
                                    @endif
                                </td>
                                <td><div>{{ $record['rider_name'] }}</div><div class="pm-sub">{{ $record['rider_email'] ?: 'No rider assigned' }}</div></td>
                                <td>{{ $record['stage_started_at']?->timezone('Asia/Manila')->format('M j, h:i A') ?? '—' }} <span class="pm-sub">PHT</span></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @elseif($section === 'sellers')
                        <thead><tr><th>Store</th><th>Owner</th><th>Location</th><th>Products</th><th>Orders</th><th>Delivered</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @foreach($records as $record)
                            @php $recordStatus = ($record->suspended_until && now()->lt($record->suspended_until) && strtolower((string)($record->account_status ?: 'active')) === 'active') ? 'suspended' : strtolower((string) ($record->account_status ?: 'active')); @endphp
                            <tr>
                                <td><div class="pm-name">{{ $record->store_name ?: 'Unnamed Store' }}</div><div class="pm-sub">{{ $record->email }}</div></td>
                                <td><div class="pm-name">{{ trim(($record->first_name ?? '').' '.($record->last_name ?? '')) ?: '—' }}</div><div class="pm-sub">{{ $record->contact_no ?: 'No contact' }}</div></td>
                                <td>{{ collect([$record->municipality_name, $record->province_name])->filter()->implode(', ') ?: '—' }}</td>
                                <td>{{ number_format((int) $record->products_count) }} <span class="pm-sub">({{ number_format((int) $record->live_products_count) }} live)</span></td>
                                <td>{{ number_format((int) $record->marketplace_orders_count) }}</td>
                                <td>{{ number_format((int) $record->delivered_orders_count) }}</td>
                                <td><span class="pm-status {{ $recordStatus }}">{{ $statusLabel($recordStatus) }}</span></td>
                                <td><a class="pm-view" href="{{ route('admin.monitoring.sellers.show', $record) }}">View store</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @elseif($section === 'logistics')
                        <thead><tr><th>Provider</th><th>Representative</th><th>Service area</th><th>Hubs</th><th>Riders</th><th>Active deliveries</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @foreach($records as $record)
                            @php $recordStatus = strtolower((string) ($record->account_status ?: 'active')); @endphp
                            <tr>
                                <td><div class="pm-name">{{ $record->business_name ?: $record->displayName() }}</div><div class="pm-sub">{{ $record->email }}</div></td>
                                <td>{{ trim(($record->first_name ?? '').' '.($record->last_name ?? '')) ?: '—' }}</td>
                                <td><div>{{ collect([$record->municipality_name, $record->province_name])->filter()->implode(', ') ?: '—' }}</div><div class="pm-sub">{{ \Illuminate\Support\Str::limit((string) $record->service_areas, 55) }}</div></td>
                                <td>{{ number_format((int) $record->facilities_count) }}</td>
                                <td>{{ number_format((int) $record->riders_count) }}</td>
                                <td>{{ number_format((int) $record->active_deliveries_count) }}</td>
                                <td><span class="pm-status {{ $recordStatus }}">{{ $statusLabel($recordStatus) }}</span></td>
                                <td><a class="pm-view" href="{{ route('admin.monitoring.logistics.show', $record) }}">View network</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @elseif($section === 'buyers')
                        <thead><tr><th>Buyer</th><th>Account type</th><th>Location</th><th>Total orders</th><th>Successful</th><th>Cancelled</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @foreach($records as $record)
                            @php $recordStatus = strtolower((string) ($record->account_status ?: 'active')); @endphp
                            <tr>
                                <td><div class="pm-name">{{ $record->display_name }}</div><div class="pm-sub">{{ $record->email ?: 'No email' }}</div></td>
                                <td>{{ $record->role_key === 'social_buyer' ? 'Social Buyer' : 'Registered Buyer' }}</td>
                                <td>{{ collect([$record->municipality_name, $record->province_name])->filter()->implode(', ') ?: '—' }}</td>
                                <td>{{ number_format((int) $record->orders_count) }}</td>
                                <td>{{ number_format((int) $record->delivered_orders_count) }}</td>
                                <td>{{ number_format((int) $record->cancelled_orders_count) }}</td>
                                <td><span class="pm-status {{ $recordStatus }}">{{ $statusLabel($recordStatus) }}</span></td>
                                <td><a class="pm-view" href="{{ route('admin.monitoring.buyers.show', ['role' => $record->role_key, 'id' => $record->id]) }}">View buyer</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @else
                        <thead><tr><th>Rider</th><th>Logistics</th><th>Registered area</th><th>Vehicle</th><th>Active load</th><th>Delivered</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        @foreach($records as $record)
                            @php $recordStatus = strtolower((string) ($record->account_status ?: 'active')); @endphp
                            <tr>
                                <td><div class="pm-name">{{ trim(($record->first_name ?? '').' '.($record->last_name ?? '')) ?: 'SARI Rider' }}</div><div class="pm-sub">{{ $record->email }}</div></td>
                                <td>{{ $record->logistics?->displayName() ?? 'Unlinked' }}</td>
                                <td>{{ collect([$record->barangay_name, $record->municipality_name, $record->province_name])->filter()->implode(', ') ?: '—' }}</td>
                                <td><div>{{ $record->vehicle_type ?: '—' }}</div><div class="pm-sub">{{ $record->plate_number ?: 'No plate' }}</div></td>
                                <td>{{ number_format((int) $record->active_deliveries_count) }}</td>
                                <td>{{ number_format((int) $record->delivered_orders_count) }}</td>
                                <td><span class="pm-status {{ $recordStatus }}">{{ $statusLabel($recordStatus) }}</span></td>
                                <td><a class="pm-view" href="{{ route('admin.monitoring.riders.show', $record) }}">View rider</a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    @endif
                </table>
            @else
                <div class="pm-empty">No records match the selected filters.</div>
            @endif
        </div>
        @if($section !== 'sla' && $records->hasPages())
            <div class="pm-pagination">{{ $records->links() }}</div>
        @endif
    </section>
</div>
@endsection
