@extends('layouts.admin')



@section('title', 'Admin Recent Activity — SARI Admin')

@section('page-title', 'Admin Recent Activity')



@section('content')

<div class="admin-activity-page mx-auto w-full max-w-[1500px] pb-8">
    <style>
        .admin-activity-page{
            --aa-gold:#D59617;
            --aa-gold-strong:#B97805;
            --aa-ink:#111827;
            --aa-text:#475467;
            --aa-muted:#667085;
            --aa-line:#E5E7EB;
            --aa-line-soft:#EEF1F4;
            --aa-soft:#F8FAFC;
            color:var(--aa-ink);
            font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
        }

        .aa-main-shadow{
            box-shadow:
                0 1px 3px rgba(15,23,42,.035),
                0 8px 22px rgba(15,23,42,.065),
                0 20px 44px rgba(15,23,42,.075);
        }

        .aa-header{
            display:flex!important;
            align-items:flex-end!important;
            justify-content:space-between!important;
            gap:18px!important;
            margin:0!important;
            padding:0 0 16px!important;
            border-bottom:1px solid #DFE3E8!important;
            background:transparent!important;
            box-shadow:none!important;
        }
        .aa-eyebrow{
            margin:0!important;
            color:#717B87!important;
            font-size:9px!important;
            font-weight:800!important;
            line-height:normal!important;
            letter-spacing:.15em!important;
            text-transform:uppercase!important;
        }
        .aa-title{
            margin:5px 0 0!important;
            color:#202124!important;
            font-size:clamp(29px,2.2vw,37px)!important;
            font-weight:700!important;
            line-height:1.06!important;
            letter-spacing:-.045em!important;
        }
        .aa-title span{color:var(--aa-gold)!important}
        .aa-copy{
            max-width:820px!important;
            margin:8px 0 0!important;
            color:#697481!important;
            font-size:11px!important;
            font-weight:400!important;
            line-height:1.65!important;
        }

        .aa-filter{
            display:flex;
            align-items:flex-end;
            justify-content:space-between;
            gap:18px;
            margin-top:16px;
            border:0!important;
            background:transparent!important;
            padding:0!important;
            box-shadow:none!important;
        }
        .aa-filter-copy strong{
            display:block;
            color:#28303A;
            font-size:15px;
            font-weight:750;
            line-height:1.25;
            letter-spacing:-.018em;
        }
        .aa-filter-copy span{
            display:block;
            max-width:680px;
            margin-top:5px;
            color:#74808D;
            font-size:10px;
            font-weight:400;
            line-height:1.6;
        }
        .aa-filter-controls{display:flex;align-items:flex-end;gap:9px}
        .aa-filter-label{
            display:block;
            margin-bottom:7px;
            color:#4B5563;
            font-size:10px;
            font-weight:750;
            line-height:1.2;
        }
        .aa-filter-field{width:220px;min-width:220px}

        .aa-native-select{
            width:100%;
            height:44px;
            appearance:none;
            -webkit-appearance:none;
            border:1px solid #D6DCE2;
            border-radius:10px;
            background:#fff;
            padding:0 12px;
            color:#29313B;
            font:600 11px 'Poppins',sans-serif;
            outline:none;
        }

        .aa-filter-controls>button,
        .aa-filter-controls>a{
            display:inline-flex;
            height:44px;
            align-items:center;
            justify-content:center;
            border-radius:10px;
            padding:0 16px;
            font-size:10px;
            font-weight:750;
            text-decoration:none;
            white-space:nowrap;
            transition:border-color .15s ease,background-color .15s ease,color .15s ease,box-shadow .15s ease;
        }
        .aa-filter-controls>button{
            border:1px solid var(--aa-gold);
            background:var(--aa-gold);
            color:#fff;
        }
        .aa-filter-controls>button:hover{
            border-color:var(--aa-gold-strong);
            background:var(--aa-gold-strong);
        }
        .aa-filter-controls>a{
            border:1px solid #D6DCE2;
            background:#fff;
            color:#475467;
        }
        .aa-filter-controls>a:hover{
            border-color:#BFC7D0;
            background:#F8FAFC;
            color:#1F2937;
        }

        .aa-panel{
            margin-top:16px;
            overflow:hidden;
            border:1px solid #E1E5EA;
            border-radius:14px;
            background:#fff;
        }
        .aa-panel-head{
            display:flex;
            min-height:66px;
            align-items:center;
            justify-content:space-between;
            gap:14px;
            padding:13px 16px;
            border-bottom:1px solid #ECEFF2;
            background:#fff;
        }
        .aa-panel-head h2{
            margin:0;
            color:#303844;
            font-size:14px;
            font-weight:750;
            letter-spacing:-.01em;
        }
        .aa-panel-head p{
            margin:4px 0 0;
            color:#74808D;
            font-size:10px;
            line-height:1.55;
        }
        .aa-count{
            display:inline-flex;
            min-height:28px;
            align-items:center;
            border:1px solid #E5E7EB;
            border-radius:999px;
            background:#F8F9FA;
            padding:0 10px;
            color:#667085;
            font-size:9px;
            font-weight:700;
            white-space:nowrap;
        }

        .aa-table-wrap{overflow:auto}
        .aa-table{
            width:100%;
            min-width:1180px;
            border-collapse:collapse;
        }
        .aa-table th{
            padding:12px 14px;
            border-bottom:1px solid #E9EDF1;
            background:#F8FAFC;
            color:#667085;
            font-size:10px;
            font-weight:800;
            letter-spacing:.055em;
            text-align:left;
            text-transform:uppercase;
            white-space:nowrap;
        }
        .aa-table td{
            padding:14px;
            border-bottom:1px solid #EEF1F4;
            background:#fff;
            color:#475467;
            font-size:11px;
            line-height:1.55;
            vertical-align:top;
        }
        .aa-table tbody tr:hover td{background:#F9FAFB}
        .aa-table tbody tr:last-child td{border-bottom:0}

        .aa-name{
            color:#28303A;
            font-size:11.5px;
            font-weight:750;
            line-height:1.35;
        }
        .aa-sub{
            margin-top:3px;
            color:#74808D;
            font-size:9.5px;
            line-height:1.45;
        }

        /* Event labels: text only. */
        .aa-table td .aa-badge{
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
        .aa-table td .aa-badge.login{
            color:#16A34A!important;
            font-weight:800;
        }
        .aa-table td .aa-badge.logout{
            color:#EF4444!important;
            font-weight:800;
        }
        .aa-table td .aa-badge.action{
            color:#111827!important;
            font-weight:700;
        }

        .aa-method{
            display:inline-block;
            color:#111827;
            font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono",monospace;
            font-size:10px;
            font-weight:700;
            letter-spacing:.01em;
        }

        .aa-empty{
            padding:42px 18px;
            color:#7D8792;
            font-size:11px;
            line-height:1.6;
            text-align:center;
        }

        .aa-pagination{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:16px;
            border-top:1px solid #ECEFF2;
            padding:12px 14px;
            background:#FAFBFC;
        }
        .aa-pagination-summary{
            color:#667085;
            font-size:10px;
            font-weight:500;
        }
        .aa-pagination nav{
            display:flex;
            align-items:center;
            gap:5px;
            flex-wrap:wrap;
        }
        .aa-page-link,
        .aa-page-disabled,
        .aa-page-current,
        .aa-page-ellipsis{
            display:inline-flex;
            min-width:36px;
            height:36px;
            align-items:center;
            justify-content:center;
            border:1px solid #D1D5DB;
            border-radius:8px;
            background:#fff;
            padding:0 10px;
            color:#475467;
            font-size:10px;
            font-weight:700;
            text-decoration:none;
        }
        .aa-page-link:hover{
            border-color:#D6B15A;
            background:#F8FAFC;
            color:#94620A;
        }
        .aa-page-current{
            border-color:var(--aa-gold);
            background:var(--aa-gold);
            color:#fff;
        }
        .aa-page-disabled{opacity:.42;cursor:not-allowed}
        .aa-page-ellipsis{
            min-width:24px;
            border-color:transparent;
            background:transparent;
            padding:0 4px;
            color:#98A2B3;
        }

        /* Custom dropdown */
        .aa-custom-select{position:relative;width:100%}
        .aa-select-trigger{
            display:none;
            width:100%;
            height:44px;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            border:1px solid #D6DCE2;
            border-radius:10px;
            background:#fff;
            padding:0 11px 0 12px;
            color:#29313B;
            font:600 11px 'Poppins',sans-serif;
            text-align:left;
            cursor:pointer;
            box-shadow:0 1px 2px rgba(15,23,42,.025);
            transition:border-color .15s ease,box-shadow .15s ease,background-color .15s ease;
        }
        .aa-select-trigger:hover{border-color:#BCC4CC;background:#fff}
        .aa-select-trigger:focus-visible{
            outline:none;
            border-color:var(--aa-gold);
            box-shadow:0 0 0 3px rgba(213,150,23,.10);
        }
        .aa-select-chevron{
            width:15px;
            height:15px;
            flex:0 0 15px;
            color:#667085;
            transition:transform .16s ease,color .16s ease;
        }
        .aa-custom-select.is-open .aa-select-trigger{
            border-color:var(--aa-gold);
            box-shadow:0 0 0 3px rgba(213,150,23,.10);
        }
        .aa-custom-select.is-open .aa-select-chevron{
            transform:rotate(180deg);
            color:var(--aa-gold-strong);
        }
        .aa-select-menu{
            display:none;
            position:absolute;
            z-index:95;
            top:calc(100% + 7px);
            left:0;
            width:100%;
            overflow:hidden;
            border:1px solid #E1E5EA;
            border-radius:11px;
            background:#fff;
            padding:5px;
            box-shadow:0 6px 14px rgba(15,23,42,.09),0 18px 38px rgba(15,23,42,.13);
        }
        .aa-custom-select.is-open .aa-select-menu{display:block}
        .aa-select-option{
            position:relative;
            display:flex;
            width:100%;
            min-height:40px;
            align-items:center;
            justify-content:space-between;
            gap:10px;
            border:0;
            border-radius:7px;
            background:transparent;
            padding:9px 10px;
            color:#3D4651;
            font:500 10.5px/1.35 'Poppins',sans-serif;
            text-align:left;
            cursor:pointer;
            transition:background-color .12s ease,color .12s ease;
        }
        .aa-select-option:hover,
        .aa-select-option:focus-visible{
            outline:none;
            background:#F3F4F6;
            color:#111827;
        }
        .aa-select-option[aria-selected="true"]{
            background:var(--aa-gold);
            color:#fff;
            font-weight:700;
        }
        .aa-select-option[aria-selected="true"]::after{
            content:"✓";
            flex:0 0 auto;
            color:#fff;
            font-size:12px;
            font-weight:900;
        }
        .aa-custom-select.is-enhanced .aa-native-select{
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
        .aa-custom-select.is-enhanced .aa-select-trigger{display:flex}

        @media(max-width:900px){
            .aa-filter{align-items:stretch;flex-direction:column;gap:14px}
            .aa-filter-controls{width:100%}
            .aa-filter-field{width:100%;min-width:0;flex:1}
        }
        @media(max-width:639px){
            .aa-header{display:block!important;padding-bottom:15px!important}
            .aa-title{font-size:clamp(28px,8vw,34px)!important}
            .aa-copy{max-width:none!important;font-size:10.5px!important}
            .aa-filter-copy strong{font-size:14px}
            .aa-filter-copy span{font-size:9.5px}
            .aa-filter-controls{align-items:stretch;flex-direction:column;gap:9px}
            .aa-filter-field,.aa-filter-controls>button,.aa-filter-controls>a{width:100%}
            .aa-pagination{align-items:stretch;flex-direction:column}
            .aa-pagination nav{width:100%}
        }
    </style>



    <div class="aa-header">

        <div><p class="aa-eyebrow">Administrator Audit</p><h1 class="aa-title">Admin Recent <span>Activity</span></h1><p class="aa-copy">Review this administrator’s sign-ins, sign-outs, and successful state-changing Admin requests. Sensitive form data, passwords, message contents, and uploaded file contents are never stored in this log.</p></div>

    </div>



    <form method="GET" action="{{ route('admin.recent-activity') }}" class="aa-filter aa-main-shadow">

        <div class="aa-filter-copy"><strong>Filter recent Admin activity</strong><span>This page only shows the currently signed-in administrator’s audit trail.</span></div>

        <div class="aa-filter-controls">

            <div class="aa-filter-field">

                <span class="aa-filter-label">Event type</span>

                <div class="aa-custom-select" data-aa-select>

                    <select name="type" class="aa-native-select" data-aa-native-select>

                        <option value="all" @selected($filter === 'all')>All events</option>

                        <option value="login" @selected($filter === 'login')>Sign-ins</option>

                        <option value="logout" @selected($filter === 'logout')>Sign-outs</option>

                        <option value="action" @selected($filter === 'action')>Admin actions</option>

                    </select>



                    <button class="aa-select-trigger" type="button" data-aa-select-trigger aria-haspopup="listbox" aria-expanded="false">

                        <span data-aa-select-label>All events</span>

                        <svg class="aa-select-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true">

                            <path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>

                        </svg>

                    </button>



                    <div class="aa-select-menu" role="listbox" aria-label="Event type" data-aa-select-menu>

                        <button type="button" class="aa-select-option" role="option" data-value="all">All events</button>

                        <button type="button" class="aa-select-option" role="option" data-value="login">Sign-ins</button>

                        <button type="button" class="aa-select-option" role="option" data-value="logout">Sign-outs</button>

                        <button type="button" class="aa-select-option" role="option" data-value="action">Admin actions</button>

                    </div>

                </div>

            </div>

            <button type="submit">Apply</button>

            <a href="{{ route('admin.recent-activity') }}">Reset</a>

        </div>

    </form>



    <section class="aa-panel aa-main-shadow">

        <div class="aa-panel-head"><div><h2>Recent administrator events</h2><p>Newest activity appears first. Dates and times are displayed using Philippine time.</p></div>@if($logs)<span class="aa-count">{{ number_format($logs->total()) }} records</span>@endif</div>

        @if($logs)

            <div class="aa-table-wrap">

                @if($logs->count())

                    <table class="aa-table"><thead><tr><th>Date & time</th><th>Event</th><th>Method</th><th>Route / path</th><th>IP address</th><th>Status</th><th>Description</th></tr></thead><tbody>

                    @foreach($logs as $event)

                        <tr>

                            <td>
                                <div class="aa-name">{{ $event['created_at']?->copy()->timezone('Asia/Manila')->format('M d, Y') ?? '—' }}</div>
                                <div class="aa-sub">{{ $event['created_at']?->copy()->timezone('Asia/Manila')->format('h:i A') ?? '—' }}</div>
                            </td>

                            <td><span class="aa-badge {{ $event['action'] === 'login' ? 'login' : ($event['action'] === 'logout' ? 'logout' : 'action') }}">{{ $event['action_label'] }}</span></td>

                            <td><span class="aa-method">{{ $event['method'] }}</span></td>

                            <td><div class="aa-name">{{ $event['route_name'] ?? '—' }}</div>@if($event['path'])<div class="aa-sub">{{ $event['path'] }}</div>@endif</td>

                            <td>{{ $event['ip'] }}</td>

                            <td>{{ $event['response_status'] ?? '—' }}</td>

                            <td>{{ $event['description'] }}</td>

                        </tr>

                    @endforeach

                    </tbody></table>

                @else

                    <div class="aa-empty">No Admin activity matches this filter yet.</div>

                @endif

            </div>

            @if($logs->hasPages())
                @php
                    $currentPage = $logs->currentPage();
                    $lastPage = $logs->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);

                    if (($endPage - $startPage) < 4) {
                        if ($startPage === 1) {
                            $endPage = min($lastPage, 5);
                        } elseif ($endPage === $lastPage) {
                            $startPage = max(1, $lastPage - 4);
                        }
                    }
                @endphp
                <div class="aa-pagination">
                    <span class="aa-pagination-summary">
                        Showing {{ number_format($logs->firstItem() ?? 0) }}–{{ number_format($logs->lastItem() ?? 0) }}
                        of {{ number_format($logs->total()) }} records
                    </span>

                    <nav aria-label="Admin activity pagination">
                        @if($logs->onFirstPage())
                            <span class="aa-page-disabled">Previous</span>
                        @else
                            <a class="aa-page-link" href="{{ $logs->previousPageUrl() }}">Previous</a>
                        @endif

                        @if($startPage > 1)
                            <a class="aa-page-link" href="{{ $logs->url(1) }}">1</a>
                            @if($startPage > 2)
                                <span class="aa-page-ellipsis">…</span>
                            @endif
                        @endif

                        @for($page = $startPage; $page <= $endPage; $page++)
                            @if($page === $currentPage)
                                <span class="aa-page-current" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="aa-page-link" href="{{ $logs->url($page) }}">{{ $page }}</a>
                            @endif
                        @endfor

                        @if($endPage < $lastPage)
                            @if($endPage < $lastPage - 1)
                                <span class="aa-page-ellipsis">…</span>
                            @endif
                            <a class="aa-page-link" href="{{ $logs->url($lastPage) }}">{{ $lastPage }}</a>
                        @endif

                        @if($logs->hasMorePages())
                            <a class="aa-page-link" href="{{ $logs->nextPageUrl() }}">Next</a>
                        @else
                            <span class="aa-page-disabled">Next</span>
                        @endif
                    </nav>
                </div>
            @endif

        @else

            <div class="aa-empty">The existing <code>admin_user_activities</code> table is not available yet. Run the project migrations first.</div>

        @endif

    </section>

    <script>

        (() => {

            const roots = document.querySelectorAll('[data-aa-select]');



            roots.forEach((root) => {

                const nativeSelect = root.querySelector('[data-aa-native-select]');

                const trigger = root.querySelector('[data-aa-select-trigger]');

                const label = root.querySelector('[data-aa-select-label]');

                const menu = root.querySelector('[data-aa-select-menu]');

                const options = Array.from(root.querySelectorAll('.aa-select-option'));



                if (!nativeSelect || !trigger || !label || !menu || !options.length) return;



                const sync = (value) => {

                    const nativeOption = Array.from(nativeSelect.options).find((option) => option.value === value)

                        || nativeSelect.options[0];



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

                    root.classList.add('is-open');

                    trigger.setAttribute('aria-expanded', 'true');



                    const selected = options.find((option) => option.getAttribute('aria-selected') === 'true') || options[0];

                    requestAnimationFrame(() => selected.focus());

                };



                root.classList.add('is-enhanced');

                nativeSelect.tabIndex = -1;

                nativeSelect.setAttribute('aria-hidden', 'true');

                sync(nativeSelect.value);



                trigger.addEventListener('click', () => {

                    root.classList.contains('is-open') ? close(false) : open();

                });



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



                document.addEventListener('pointerdown', (event) => {

                    if (!root.contains(event.target)) close(false);

                });



                nativeSelect.addEventListener('change', () => sync(nativeSelect.value));

            });

        })();

    </script>



</div>

@endsection
