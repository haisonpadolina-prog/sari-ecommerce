@extends('layouts.admin')

@section('title', 'User Activity Log — SARI Admin')
@section('page-title', 'User Activity Log')

@section('content')
<div class="user-activity-page mx-auto w-full max-w-[1500px] pb-8">
    <style>
        .user-activity-page{
            --ua-gold:#D59617;
            --ua-gold-strong:#B97805;
            --ua-ink:#17130F;
            --ua-text:#5F5A54;
            --ua-muted:#8B837A;
            --ua-line:#E5E7EB;
            --ua-line-soft:#ECEFF2;
            --ua-line-strong:#D1D5DB;
            --ua-soft:#FAFBFC;
            color:var(--ua-ink);
            font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
        }
        .ua-main-shadow{box-shadow:0 3px 7px rgba(15,23,42,.055),0 14px 32px rgba(15,23,42,.095),0 30px 68px rgba(15,23,42,.11)}
        .ua-soft-shadow{box-shadow:0 1px 3px rgba(15,23,42,.035),0 7px 18px rgba(15,23,42,.06),0 15px 32px rgba(15,23,42,.065),inset 0 1px 0 rgba(255,255,255,.90)}
        .ua-header{display:flex!important;align-items:flex-end!important;justify-content:space-between!important;gap:18px!important;margin:0!important;padding:0 0 15px!important;border-bottom:1px solid #DFE3E8!important;background:transparent!important;box-shadow:none!important;transform:none!important}
        .ua-eyebrow{margin:0!important;color:#717B87!important;font-size:8px!important;font-weight:800!important;line-height:normal!important;letter-spacing:.15em!important;text-transform:uppercase!important}
        .ua-title{margin:5px 0 0!important;color:#202124!important;font-size:clamp(29px,2.2vw,37px)!important;font-weight:700!important;line-height:1.06!important;letter-spacing:-.045em!important}
        .ua-title span{color:#D59617!important}
        .ua-copy{max-width:760px!important;margin:7px 0 0!important;color:#697481!important;font-size:10px!important;font-weight:400!important;line-height:1.65!important}
        .ua-filter{display:grid;grid-template-columns:minmax(300px,1fr) 190px 190px 150px auto auto;gap:10px;align-items:end;margin-top:14px;border:0!important;border-radius:0!important;background:transparent!important;padding:0!important;box-shadow:none!important}
        .ua-filter-intro strong{display:block;color:#28303A;font-size:14px;font-weight:750;line-height:1.25;letter-spacing:-.018em}.ua-filter-intro span{display:block;max-width:620px;margin-top:5px;color:#74808D;font-size:9.5px;font-weight:400;line-height:1.6}
        .ua-filter-label{display:block;margin-bottom:7px;color:#4B5563;font-size:10px;font-weight:750;line-height:1.2}.ua-filter-field{width:100%;min-width:0}
        .ua-native-select{
            width:100%;
            min-height:44px;
            height:44px;
            appearance:none;
            -webkit-appearance:none;
            border:1px solid #D6DCE2;
            border-radius:8px;
            background:#fff;
            padding:0 12px;
            color:#29313B;
            font:600 11px 'Poppins',sans-serif;
            outline:none;
            box-shadow:0 1px 2px rgba(15,23,42,.025);
        }
        .ua-native-select:focus{border-color:#D59617;box-shadow:0 0 0 3px rgba(213,150,23,.08)}
        .ua-filter>button,.ua-filter>a{display:inline-flex;height:44px;align-items:center;justify-content:center;border-radius:8px;padding:0 16px;font-size:10px;font-weight:750;text-decoration:none;white-space:nowrap;box-shadow:none;transform:none;transition:border-color .15s ease,background-color .15s ease,color .15s ease}
        .ua-filter>button{border:1px solid var(--ua-gold);background:var(--ua-gold);color:#fff}.ua-filter>button:hover{border-color:var(--ua-gold-strong);background:var(--ua-gold-strong)}
        .ua-filter>a{border:1px solid #D6DCE2;background:#fff;color:#56606C}.ua-filter>a:hover{border-color:#BCC4CC;background:#FAFBFC;color:#303844}
        .ua-table-panel{margin-top:14px;overflow:hidden;border:1px solid #DFE3E8;border-radius:8px;background:#fff}
        .ua-table-head{display:flex;min-height:58px;align-items:center;justify-content:space-between;gap:12px;padding:11px 14px;border-bottom:1px solid #ECEFF2;background:#fff}
        .ua-table-head h2{margin:0;color:#303844;font-size:14px;font-weight:750;letter-spacing:-.01em}.ua-table-head p{margin:4px 0 0;color:#74808D;font-size:10px;line-height:1.55}
        .ua-table-wrap{overflow:auto}.ua-table{width:100%;min-width:1180px;border-collapse:collapse}
        .ua-table th{padding:12px 14px;border-bottom:1px solid #ECEFF2;background:#FAFBFC;color:#667085;font-size:10px;font-weight:800;letter-spacing:.055em;text-align:left;text-transform:uppercase;white-space:nowrap}
        .ua-table td{padding:14px;border-bottom:1px solid #EEF1F4;background:#fff;color:#475467;font-size:11px;line-height:1.55;vertical-align:top}
        .ua-table tbody tr:hover td{background:#FFFCF5}.ua-table tbody tr:last-child td{border-bottom:0}
        .ua-name{color:#28303A;font-size:11.5px;font-weight:750;line-height:1.35}.ua-sub{margin-top:3px;color:#74808D;font-size:9.5px;line-height:1.45}
        .ua-badge{display:inline-flex;min-height:26px;align-items:center;border:1px solid var(--ua-line);border-radius:999px;background:#F8F9FA;padding:0 9px;color:#6E675F;font-size:9px;font-weight:700;white-space:nowrap}
        .ua-badge.login{border-color:#D8E7DD;background:#F3F8F5;color:#5B7A65}.ua-badge.logout{border-color:#E8DDC9;background:#FFF9EF;color:#8B6B32}.ua-badge.admin{border-color:#E7D7B4;background:#FFF9EC;color:#8F671E}
        .ua-source{display:inline-flex;align-items:center;gap:6px;color:#5F6772;font-size:10px;font-weight:650;white-space:nowrap}.ua-source::before{content:"";width:7px;height:7px;border-radius:50%;background:#6F8E79}.ua-source.admin::before{background:var(--ua-gold)}
        .ua-empty{padding:38px 18px;color:#8E867E;font-size:11px;line-height:1.6;text-align:center}
        .ua-pagination{display:flex;align-items:center;justify-content:space-between;gap:16px;border-top:1px solid #ECEFF2;padding:12px 14px;background:#FAFBFC}
        .ua-pagination-summary{color:#667085;font-size:10px;font-weight:500}
        .ua-pagination nav{display:flex;align-items:center;gap:5px;flex-wrap:wrap}
        .ua-page-link,.ua-page-disabled,.ua-page-current,.ua-page-ellipsis{display:inline-flex;min-width:36px;height:36px;align-items:center;justify-content:center;border:1px solid #D1D5DB;border-radius:8px;background:#fff;padding:0 10px;color:#475467;font-size:10px;font-weight:700;text-decoration:none}
        .ua-page-link:hover{border-color:#D8B56A;background:#FFF9EF;color:#94620A}
        .ua-page-current{border-color:var(--ua-gold);background:var(--ua-gold);color:#fff}
        .ua-page-disabled{opacity:.42;cursor:not-allowed}
        .ua-page-ellipsis{min-width:24px;border-color:transparent;background:transparent;padding:0 4px;color:#98A2B3}
        @media(max-width:1023px){.ua-filter{grid-template-columns:1fr 1fr}.ua-filter-intro{grid-column:1/-1}.ua-filter-field{width:100%;min-width:0}}
        @media(max-width:639px){.ua-header{display:block!important;padding-bottom:15px!important}.ua-title{font-size:clamp(28px,8vw,34px)!important}.ua-copy{max-width:none!important;font-size:10px!important;line-height:1.65!important}.ua-filter{grid-template-columns:1fr;margin-top:14px;padding:0!important;gap:9px}.ua-filter-intro{grid-column:auto}.ua-filter-intro strong{font-size:13px}.ua-filter-intro span{font-size:9.25px}.ua-filter-field,.ua-filter>button,.ua-filter>a{width:100%}.ua-filter>button,.ua-filter>a,.ua-select-trigger,.ua-native-select{height:44px}.ua-pagination{align-items:stretch;flex-direction:column}.ua-pagination nav{width:100%}.ua-pagination nav>*{flex:0 0 auto}}
            /* USER ACTIVITY LOG — SELLER FILTER PARITY
           Open filter layout + custom role/event dropdowns; backend filters preserved. */

        .user-activity-page .ua-filter.ua-main-shadow{box-shadow:none!important}

        .ua-custom-select{position:relative;width:100%}
        .ua-select-trigger{
            display:none;
            width:100%;
            height:44px;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            border:1px solid #D6DCE2;
            border-radius:8px;
            background:#fff;
            padding:0 11px 0 12px;
            color:#29313B;
            font:600 11px 'Poppins',sans-serif;
            text-align:left;
            cursor:pointer;
            box-shadow:0 1px 2px rgba(15,23,42,.025);
            transition:border-color .15s ease,box-shadow .15s ease,background-color .15s ease;
        }
        .ua-select-trigger:hover{border-color:#BCC4CC;background:#FFFEFB}
        .ua-select-trigger:focus-visible{outline:none;border-color:#D59617;box-shadow:0 0 0 3px rgba(213,150,23,.08)}
        .ua-select-chevron{width:14px;height:14px;flex:0 0 14px;color:#6B7280;transition:transform .16s ease,color .16s ease}
        .ua-custom-select.is-open .ua-select-trigger{border-color:#D59617;box-shadow:0 0 0 3px rgba(213,150,23,.08)}
        .ua-custom-select.is-open .ua-select-chevron{transform:rotate(180deg);color:#B97805}

        .ua-select-menu{
            display:none;
            position:absolute;
            z-index:95;
            top:calc(100% + 6px);
            left:0;
            width:100%;
            overflow:hidden;
            border:1px solid #E1E5EA;
            border-radius:9px;
            background:#fff;
            padding:5px;
            box-shadow:0 4px 10px rgba(15,23,42,.08),0 14px 34px rgba(15,23,42,.13);
        }
        .ua-custom-select.is-open .ua-select-menu{display:block}
        .ua-select-option{
            position:relative;
            display:flex;
            width:100%;
            min-height:38px;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            border:0;
            border-radius:6px;
            background:transparent;
            padding:8px 10px;
            color:#3D4651;
            font:500 10.5px/1.35 'Poppins',sans-serif;
            text-align:left;
            cursor:pointer;
            box-shadow:none;
            transition:background-color .12s ease,color .12s ease;
        }
        .ua-select-option:hover,.ua-select-option:focus-visible{outline:none;background:#F7F8FA;color:#20262E}
        .ua-select-option[aria-selected="true"]{background:#FFF7E6;color:#94620A;font-weight:700}
        .ua-select-option[aria-selected="true"]::after{content:"✓";flex:0 0 auto;color:#D59617;font-size:11px;font-weight:800}

        .ua-custom-select.is-enhanced .ua-native-select{
            position:absolute!important;
            width:1px!important;
            height:1px!important;
            margin:-1px!important;
            padding:0!important;
            overflow:hidden!important;
            clip:rect(0 0 0 0)!important;
            white-space:nowrap!important;
            border:0!important;
            opacity:0!important;
            pointer-events:none!important;
        }
        .ua-custom-select.is-enhanced .ua-select-trigger{display:flex}

        /* User activity table status labels: text-only, no pill containers */
        .ua-table td .ua-badge{
            display:inline;
            min-height:0;
            border:0!important;
            border-radius:0;
            background:transparent!important;
            padding:0;
            color:#111827!important;
            font-size:11px;
            font-weight:700;
            line-height:1.5;
            white-space:normal;
        }
        .ua-table td .ua-badge.login{color:#16A34A!important;font-weight:800}
        .ua-table td .ua-badge.logout{color:#EF4444!important;font-weight:800}
        .ua-table td .ua-badge.admin{color:#111827!important}
    </style>

    <div class="ua-header">
        <div>
            <p class="ua-eyebrow">Security &amp; Audit</p>
            <h1 class="ua-title">User Activity <span>Log</span></h1>
            <p class="ua-copy">Review Buyer, Seller, and Logistics sign-ins, sign-outs, user-submitted state changes, and Admin actions that affected those accounts.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.user-activity') }}" class="ua-filter ua-main-shadow">
        <div class="ua-filter-intro"><strong>Filter user audit trail</strong><span>Records are ordered newest first. No passwords, request bodies, message contents, or uploaded file contents are stored.</span></div>
        <div class="ua-filter-field">
            <span class="ua-filter-label">Account role</span>
            <div class="ua-custom-select" data-ua-select>
                <select name="role" class="ua-native-select" data-ua-native-select>
                    <option value="all" @selected(($filters['role'] ?? 'all') === 'all')>All roles</option>
                    <option value="buyer" @selected(($filters['role'] ?? 'all') === 'buyer')>Buyer</option>
                    <option value="seller" @selected(($filters['role'] ?? 'all') === 'seller')>Seller</option>
                    <option value="logistics" @selected(($filters['role'] ?? 'all') === 'logistics')>Logistics</option>
                </select>
                <button class="ua-select-trigger" type="button" data-ua-select-trigger aria-haspopup="listbox" aria-expanded="false">
                    <span data-ua-select-label>All roles</span>
                    <svg class="ua-select-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="ua-select-menu" role="listbox" aria-label="Account role" data-ua-select-menu>
                    <button type="button" class="ua-select-option" role="option" data-value="all">All roles</button>
                    <button type="button" class="ua-select-option" role="option" data-value="buyer">Buyer</button>
                    <button type="button" class="ua-select-option" role="option" data-value="seller">Seller</button>
                    <button type="button" class="ua-select-option" role="option" data-value="logistics">Logistics</button>
                </div>
            </div>
        </div>

        <div class="ua-filter-field">
            <span class="ua-filter-label">Event type</span>
            <div class="ua-custom-select" data-ua-select>
                <select name="type" class="ua-native-select" data-ua-native-select>
                    <option value="all" @selected(($filters['type'] ?? 'all') === 'all')>All events</option>
                    <option value="login" @selected(($filters['type'] ?? 'all') === 'login')>Sign-ins</option>
                    <option value="logout" @selected(($filters['type'] ?? 'all') === 'logout')>Sign-outs</option>
                    <option value="user_action" @selected(($filters['type'] ?? 'all') === 'user_action')>User actions</option>
                    <option value="admin_action" @selected(($filters['type'] ?? 'all') === 'admin_action')>Admin actions</option>
                </select>
                <button class="ua-select-trigger" type="button" data-ua-select-trigger aria-haspopup="listbox" aria-expanded="false">
                    <span data-ua-select-label>All events</span>
                    <svg class="ua-select-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="ua-select-menu" role="listbox" aria-label="Event type" data-ua-select-menu>
                    <button type="button" class="ua-select-option" role="option" data-value="all">All events</button>
                    <button type="button" class="ua-select-option" role="option" data-value="login">Sign-ins</button>
                    <button type="button" class="ua-select-option" role="option" data-value="logout">Sign-outs</button>
                    <button type="button" class="ua-select-option" role="option" data-value="user_action">User actions</button>
                    <button type="button" class="ua-select-option" role="option" data-value="admin_action">Admin actions</button>
                </div>
            </div>
        </div>

        <div class="ua-filter-field">
            <span class="ua-filter-label">Rows per page</span>
            <div class="ua-custom-select" data-ua-select>
                <select name="per_page" class="ua-native-select" data-ua-native-select>
                    @foreach([10,20,30,50] as $size)
                        <option value="{{ $size }}" @selected((int)($filters['per_page'] ?? 10) === $size)>{{ $size }} rows</option>
                    @endforeach
                </select>
                <button class="ua-select-trigger" type="button" data-ua-select-trigger aria-haspopup="listbox" aria-expanded="false">
                    <span data-ua-select-label>{{ (int)($filters['per_page'] ?? 10) }} rows</span>
                    <svg class="ua-select-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </button>
                <div class="ua-select-menu" role="listbox" aria-label="Rows per page" data-ua-select-menu>
                    @foreach([10,20,30,50] as $size)
                        <button type="button" class="ua-select-option" role="option" data-value="{{ $size }}">{{ $size }} rows</button>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit">Apply</button><a href="{{ route('admin.user-activity') }}">Reset</a>
    </form>

    <section class="ua-table-panel ua-main-shadow">
        <div class="ua-table-head"><div><h2>User audit trail</h2><p>Buyer, Seller, and Logistics account events.</p></div>@if($logs)<span class="ua-badge">{{ number_format($logs->total()) }} records</span>@endif</div>
        @if($logs)
            <div class="ua-table-wrap">
                @if($logs->count())
                    <table class="ua-table">
                        <thead><tr><th>Date &amp; time</th><th>Account</th><th>Role</th><th>Event</th><th>Performed by</th><th>IP address</th><th>Description</th></tr></thead>
                        <tbody>
                            @foreach($logs as $event)
                                <tr>
                                    <td><div class="ua-name">{{ $event['created_at']?->format('M d, Y') ?? '—' }}</div><div class="ua-sub">{{ $event['created_at']?->format('h:i:s A') ?? '—' }}</div></td>
                                    <td><div class="ua-name">{{ $event['user_name'] }}</div><div class="ua-sub">{{ $event['user_email'] }}</div></td>
                                    <td><span class="ua-badge">{{ $event['role'] }}</span></td>
                                    <td><span class="ua-badge {{ $event['action'] === 'login' ? 'login' : ($event['action'] === 'logout' ? 'logout' : ($event['source'] === 'admin' ? 'admin' : '')) }}">{{ $event['action_label'] }}</span></td>
                                    <td><span class="ua-source {{ $event['source'] === 'admin' ? 'admin' : '' }}">{{ $event['performed_by'] }}</span></td>
                                    <td>{{ $event['ip'] }}</td>
                                    <td><div>{{ $event['description'] }}</div>@if($event['route_name'])<div class="ua-sub">{{ $event['route_name'] }}</div>@endif</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="ua-empty">No user activity records match the selected filters yet.</div>
                @endif
            </div>

            @if($logs->hasPages())
                @php
                    $currentPage = $logs->currentPage();
                    $lastPage = $logs->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);

                    if ($currentPage <= 3) {
                        $endPage = min($lastPage, 5);
                    }

                    if ($currentPage >= $lastPage - 2) {
                        $startPage = max(1, $lastPage - 4);
                    }
                @endphp

                <div class="ua-pagination">
                    <span class="ua-pagination-summary">
                        Showing {{ number_format($logs->firstItem() ?? 0) }}–{{ number_format($logs->lastItem() ?? 0) }}
                        of {{ number_format($logs->total()) }} records
                    </span>

                    <nav aria-label="User activity pages">
                        @if($logs->onFirstPage())
                            <span class="ua-page-disabled">Previous</span>
                        @else
                            <a class="ua-page-link" href="{{ $logs->previousPageUrl() }}">Previous</a>
                        @endif

                        @if($startPage > 1)
                            <a class="ua-page-link" href="{{ $logs->url(1) }}">1</a>
                            @if($startPage > 2)
                                <span class="ua-page-ellipsis">…</span>
                            @endif
                        @endif

                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if($page === $currentPage)
                                <span class="ua-page-current" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="ua-page-link" href="{{ $logs->url($page) }}">{{ $page }}</a>
                            @endif
                        @endfor

                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)
                                <span class="ua-page-ellipsis">…</span>
                            @endif
                            <a class="ua-page-link" href="{{ $logs->url($lastPage) }}">{{ $lastPage }}</a>
                        @endif

                        @if($logs->hasMorePages())
                            <a class="ua-page-link" href="{{ $logs->nextPageUrl() }}">Next</a>
                        @else
                            <span class="ua-page-disabled">Next</span>
                        @endif
                    </nav>
                </div>
            @endif
        @else
            <div class="ua-empty">The existing <code>admin_user_activities</code> table is not available yet. Run the project migrations first.</div>
        @endif
    </section>
    <script>
        (() => {
            const roots = Array.from(document.querySelectorAll('[data-ua-select]'));

            const closeAll = (except = null) => {
                roots.forEach((root) => {
                    if (root === except) return;
                    root.classList.remove('is-open');
                    const trigger = root.querySelector('[data-ua-select-trigger]');
                    if (trigger) trigger.setAttribute('aria-expanded', 'false');
                });
            };

            roots.forEach((root) => {
                const nativeSelect = root.querySelector('[data-ua-native-select]');
                const trigger = root.querySelector('[data-ua-select-trigger]');
                const label = root.querySelector('[data-ua-select-label]');
                const options = Array.from(root.querySelectorAll('.ua-select-option'));

                if (!nativeSelect || !trigger || !label || !options.length) return;

                const sync = (value) => {
                    const nativeOption = Array.from(nativeSelect.options).find((option) => option.value === value) || nativeSelect.options[0];
                    nativeSelect.value = nativeOption.value;
                    label.textContent = nativeOption.textContent.trim();

                    options.forEach((option) => {
                        option.setAttribute('aria-selected', option.dataset.value === nativeOption.value ? 'true' : 'false');
                    });
                };

                const close = (returnFocus = false) => {
                    root.classList.remove('is-open');
                    trigger.setAttribute('aria-expanded', 'false');
                    if (returnFocus) trigger.focus();
                };

                const open = () => {
                    closeAll(root);
                    root.classList.add('is-open');
                    trigger.setAttribute('aria-expanded', 'true');
                    const selected = options.find((option) => option.getAttribute('aria-selected') === 'true') || options[0];
                    requestAnimationFrame(() => selected.focus());
                };

                root.classList.add('is-enhanced');
                nativeSelect.tabIndex = -1;
                nativeSelect.setAttribute('aria-hidden', 'true');
                sync(nativeSelect.value);

                trigger.addEventListener('click', () => root.classList.contains('is-open') ? close(false) : open());
                trigger.addEventListener('keydown', (event) => {
                    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                        event.preventDefault();
                        open();
                    }
                });

                options.forEach((option, index) => {
                    option.addEventListener('click', () => {
                        sync(option.dataset.value);
                        nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
                        close(true);
                    });

                    option.addEventListener('keydown', (event) => {
                        let nextIndex = null;
                        if (event.key === 'ArrowDown') nextIndex = (index + 1) % options.length;
                        if (event.key === 'ArrowUp') nextIndex = (index - 1 + options.length) % options.length;
                        if (event.key === 'Home') nextIndex = 0;
                        if (event.key === 'End') nextIndex = options.length - 1;

                        if (nextIndex !== null) {
                            event.preventDefault();
                            options[nextIndex].focus();
                        }

                        if (event.key === 'Escape') {
                            event.preventDefault();
                            close(true);
                        }
                    });
                });

                nativeSelect.addEventListener('change', () => sync(nativeSelect.value));
            });

            document.addEventListener('pointerdown', (event) => {
                if (!event.target.closest('[data-ua-select]')) closeAll();
            });
        })();
    </script>

</div>
@endsection
