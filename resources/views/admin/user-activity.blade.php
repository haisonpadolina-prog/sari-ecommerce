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

        .ua-table tbody tr:hover td{background:#f8fafc}.ua-table tbody tr:last-child td{border-bottom:0}

        .ua-name{color:#28303A;font-size:11.5px;font-weight:750;line-height:1.35}.ua-sub{margin-top:3px;color:#74808D;font-size:9.5px;line-height:1.45}

        .ua-badge{display:inline-flex;min-height:26px;align-items:center;border:1px solid var(--ua-line);border-radius:999px;background:#F8F9FA;padding:0 9px;color:#6E675F;font-size:9px;font-weight:700;white-space:nowrap}

        .ua-badge.login{border-color:#D8E7DD;background:#F3F8F5;color:#5B7A65}.ua-badge.logout{border-color:#e5e7eb;background:#f3f4f6;color:#667085}.ua-badge.admin{border-color:#e5e7eb;background:#f3f4f6;color:#667085}

        .ua-source{display:inline-flex;align-items:center;gap:6px;color:#5F6772;font-size:10px;font-weight:650;white-space:nowrap}.ua-source::before{content:"";width:7px;height:7px;border-radius:50%;background:#6F8E79}.ua-source.admin::before{background:var(--ua-gold)}

        .ua-empty{padding:38px 18px;color:#8E867E;font-size:11px;line-height:1.6;text-align:center}

        .ua-pagination{display:flex;align-items:center;justify-content:space-between;gap:16px;border-top:1px solid #ECEFF2;padding:12px 14px;background:#FAFBFC}

        .ua-pagination-summary{color:#667085;font-size:10px;font-weight:500}

        .ua-pagination nav{display:flex;align-items:center;gap:5px;flex-wrap:wrap}

        .ua-page-link,.ua-page-disabled,.ua-page-current,.ua-page-ellipsis{display:inline-flex;min-width:36px;height:36px;align-items:center;justify-content:center;border:1px solid #D1D5DB;border-radius:8px;background:#fff;padding:0 10px;color:#475467;font-size:10px;font-weight:700;text-decoration:none}

        .ua-page-link:hover{border-color:#cbd5e1;background:#f3f4f6;color:#475467}

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

        .ua-select-trigger:hover{border-color:#BCC4CC;background:#ffffff}

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

        .ua-select-option[aria-selected="true"]{background:#f3f4f6;color:#475467;font-weight:700}

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

        /* =============================================================

           USER ACTIVITY LOG — SELLER ORDERS / REGISTRATION UI PARITY

           Presentation-only; real GET filters and server pagination stay.

           ============================================================= */

        .user-activity-page{

            width:100%;

            max-width:1520px !important;

            padding-bottom:28px;

            --ua-gold:#C88912;

            --ua-gold-strong:#B47B0E;

            --ua-line:#E5E7EB;

            --ua-line-soft:#ECEFF2;

            --ua-text:#475467;

            --ua-muted:#8793A1;

        }

        .user-activity-page .ua-header{

            min-height:0;

            align-items:flex-end !important;

            margin:0 0 20px !important;

            padding:0 0 14px !important;

            border-bottom:1px solid #E9EDF1 !important;

        }

        .user-activity-page .ua-eyebrow{

            font-size:10.5px !important;

            font-weight:800 !important;

            letter-spacing:.14em !important;

            color:#697481 !important;

        }

        .user-activity-page .ua-title{

            margin-top:6px !important;

            font-size:clamp(27px,2.3vw,31px) !important;

            font-weight:760 !important;

            line-height:1.08 !important;

            letter-spacing:-.04em !important;

            color:#2E343B !important;

        }

        .user-activity-page .ua-title span{color:var(--ua-gold) !important}

        .user-activity-page .ua-copy{

            max-width:780px !important;

            margin-top:6px !important;

            font-size:11.5px !important;

            line-height:1.5 !important;

            color:#8793A1 !important;

        }

        .user-activity-page .ua-filter{

            position:relative;

            z-index:20;

            grid-template-columns:minmax(240px,1fr) minmax(145px,190px) minmax(145px,190px) minmax(125px,150px) auto auto;

            gap:12px;

            align-items:end;

            margin-top:20px;

            padding:0 !important;

            border:0 !important;

            background:transparent !important;

            box-shadow:none !important;

        }

        .user-activity-page .ua-filter-intro strong{

            font-size:14px;

            line-height:1.3;

            font-weight:750;

        }

        .user-activity-page .ua-filter-intro span{

            font-size:10.5px;

            line-height:1.5;

        }

        .user-activity-page .ua-filter-label{

            margin-bottom:7px;

            color:#475467;

            font-size:11px;

            font-weight:650;

        }

        .user-activity-page .ua-custom-select{z-index:1}

        .user-activity-page .ua-custom-select.is-open{z-index:40}

        .user-activity-page .ua-native-select,

        .user-activity-page .ua-select-trigger,

        .user-activity-page .ua-filter > button,

        .user-activity-page .ua-filter > a{

            height:48px !important;

            min-height:48px !important;

            border-radius:14px !important;

            font-size:11.5px !important;

            box-shadow:none !important;

            transform:none !important;

        }

        .user-activity-page .ua-native-select,

        .user-activity-page .ua-select-trigger{

            width:100%;

            border:1px solid #DCE2E8 !important;

            background:#FFFFFF !important;

            color:#3F4B59 !important;

            font-weight:600 !important;

        }

        .user-activity-page .ua-select-trigger{padding:0 16px !important}

        .user-activity-page .ua-select-chevron{

            width:17px;

            height:17px;

            flex-basis:17px;

            color:#8793A1 !important;

        }

        .user-activity-page .ua-select-trigger:hover,

        .user-activity-page .ua-native-select:hover{

            border-color:#CFD6DE !important;

            background:#F8FAFC !important;

        }

        .user-activity-page .ua-select-trigger:focus-visible,

        .user-activity-page .ua-native-select:focus,

        .user-activity-page .ua-custom-select.is-open .ua-select-trigger{

            outline:2px solid #A7AFBA !important;

            outline-offset:1px;

            border-color:#9CA3AF !important;

            background:#FFFFFF !important;

            box-shadow:none !important;

        }

        .user-activity-page .ua-custom-select.is-open .ua-select-chevron{color:#667085 !important}

        .user-activity-page .ua-select-menu{

            top:calc(100% + 8px);

            z-index:95;

            border:1px solid #E2E8F0;

            border-radius:14px;

            background:#FFFFFF;

            padding:8px;

            box-shadow:0 16px 32px rgba(15,23,42,.12),0 2px 6px rgba(15,23,42,.05);

        }

        .user-activity-page .ua-select-option{

            min-height:38px;

            border-radius:10px;

            background:#FFFFFF;

            padding:8px 12px;

            color:#475569;

            font-size:11.5px;

            font-weight:600;

        }

        .user-activity-page .ua-select-option:hover,

        .user-activity-page .ua-select-option:focus-visible{

            background:#F8FAFC !important;

            color:#344054 !important;

        }

        .user-activity-page .ua-select-option[aria-selected="true"]{

            background:#F1F5F9 !important;

            color:#344054 !important;

            font-weight:700;

        }

        .user-activity-page .ua-select-option[aria-selected="true"]::after{color:#64748B !important}

        .user-activity-page .ua-filter > button{

            gap:8px;

            border:1px solid var(--ua-gold) !important;

            background:var(--ua-gold) !important;

            color:#FFFFFF !important;

            padding:0 18px !important;

            font-size:12px !important;

        }

        .user-activity-page .ua-filter > button:hover{

            border-color:var(--ua-gold-strong) !important;

            background:var(--ua-gold-strong) !important;

        }

        .user-activity-page .ua-filter > a{

            border:1px solid #DCE2E8 !important;

            background:#FFFFFF !important;

            color:#5F6D7C !important;

            padding:0 18px !important;

            font-size:12px !important;

        }

        .user-activity-page .ua-filter > a:hover{

            border-color:#CFD6DE !important;

            background:#F8FAFC !important;

            color:#344054 !important;

        }

        .user-activity-page .ua-table-panel{

            margin-top:20px;

            overflow:hidden;

            border:1px solid #E5E7EB;

            border-radius:18px;

            background:#FFFFFF;

            box-shadow:0 3px 10px rgba(15,23,42,.035),0 12px 30px rgba(15,23,42,.055);

        }

        .user-activity-page .ua-table-head{

            min-height:74px;

            padding:18px 23px;

            border-bottom:1px solid #E9EDF1;

        }

        .user-activity-page .ua-table-head h2{

            font-size:18px;

            font-weight:780;

            color:#2F3742;

        }

        .user-activity-page .ua-table-head p{

            margin-top:4px;

            font-size:11.5px;

            color:#8B96A4;

        }

        .user-activity-page .ua-table-head > .ua-badge{

            border-color:#E5E7EB;

            background:#F8FAFC;

            color:#64748B;

            font-size:11px;

        }

        .user-activity-page .ua-table-wrap{

            width:100%;

            overflow-x:auto;

            overflow-y:hidden;

            scrollbar-width:thin;

        }

        .user-activity-page .ua-table{min-width:1260px}

        .user-activity-page .ua-table th{

            min-height:48px;

            padding:13px 14px;

            background:#F8FAFC;

            border-bottom:1px solid #E9EDF1;

            color:#8591A0;

            font-size:11px;

            font-weight:750;

            letter-spacing:.075em;

        }

        .user-activity-page .ua-table td{

            padding:17px 14px;

            border-bottom:1px solid #EDF0F3;

            background:#FFFFFF;

            color:#475467;

            font-size:12px;

            vertical-align:middle;

        }

        .user-activity-page .ua-table tbody tr:hover td{background:#F8FAFC}

        .user-activity-page .ua-name{font-size:12.5px;color:#293440}

        .user-activity-page .ua-sub{font-size:11px;color:#8793A1}

        .user-activity-page .ua-source{font-size:11.5px}

        .user-activity-page .ua-table td .ua-badge{font-size:11px}

        .user-activity-page .ua-pagination{

            min-height:64px;

            border-top:1px solid #E9EDF1;

            padding:13px 20px;

            background:#FFFFFF;

        }

        .user-activity-page .ua-pagination-summary{font-size:11px;color:#7B8794}

        .user-activity-page .ua-pagination nav{gap:6px}

        .user-activity-page .ua-page-link,

        .user-activity-page .ua-page-disabled,

        .user-activity-page .ua-page-current{

            min-width:34px;

            height:34px;

            border-radius:10px;

            border-color:#E5E7EB;

            padding:0 10px;

            font-size:11px;

        }

        .user-activity-page .ua-page-link:hover{

            border-color:#CBD5E1;

            background:#F8FAFC;

            color:#475467;

        }

        .user-activity-page .ua-page-current{

            border-color:var(--ua-gold);

            background:var(--ua-gold);

            color:#FFFFFF;

        }

        /* Bulk select is page-scoped and intentionally has no write action. */

        .user-activity-page .ua-check-cell{

            width:48px;

            min-width:48px;

            text-align:center;

            padding-left:17px !important;

            padding-right:7px !important;

        }

        .user-activity-page .ua-row-check{

            appearance:none;

            -webkit-appearance:none;

            display:inline-grid;

            place-items:center;

            width:18px;

            height:18px;

            margin:0;

            border:1.5px solid #CBD5E1;

            border-radius:5px;

            background:#FFFFFF;

            vertical-align:middle;

            cursor:pointer;

        }

        .user-activity-page .ua-row-check::after{

            content:"";

            width:5px;

            height:9px;

            border:solid #FFFFFF;

            border-width:0 2px 2px 0;

            transform:translateY(-1px) rotate(45deg);

            opacity:0;

        }

        .user-activity-page .ua-row-check:checked,

        .user-activity-page .ua-row-check:indeterminate{

            background:var(--ua-gold);

            border-color:var(--ua-gold);

        }

        .user-activity-page .ua-row-check:checked::after{opacity:1}

        .user-activity-page .ua-row-check:indeterminate::after{

            opacity:1;

            width:9px;

            height:2px;

            border:0;

            border-radius:2px;

            background:#FFFFFF;

            transform:none;

        }

        .user-activity-page .ua-row-check:focus-visible{outline:2px solid #8B95A3;outline-offset:3px}

        .user-activity-page tr.ua-is-selected td{background:#F1F5F9 !important}

        .user-activity-page .ua-selection-bar{

            display:flex;

            align-items:center;

            justify-content:space-between;

            flex-wrap:wrap;

            gap:10px;

            padding:10px 19px;

            background:#F8FAFC;

            border-bottom:1px solid #E5E7EB;

            color:#475467;

            font-size:11.5px;

            font-weight:650;

        }

        .user-activity-page .ua-selection-bar[hidden]{display:none !important}

        .user-activity-page .ua-selection-clear{

            min-height:32px;

            border:1px solid #DCE2E8;

            border-radius:9px;

            background:#FFFFFF;

            padding:0 12px;

            color:#475467;

            font:650 11px 'Poppins',sans-serif;

            cursor:pointer;

        }

        .user-activity-page .ua-selection-clear:hover{background:#F1F5F9;border-color:#CBD5E1}

        .user-activity-page .ua-selection-clear:focus-visible{outline:2px solid #94A3B8;outline-offset:2px}

        /* No card around empty icon; neutral gray and readable size. */

        .user-activity-page .ua-empty{

            display:flex;

            flex-direction:column;

            align-items:center;

            justify-content:center;

            gap:0;

            min-height:260px;

            padding:56px 22px 64px;

            background:#FFFFFF;

            text-align:center;

        }

        .user-activity-page .ua-empty-icon{

            display:block;

            width:70px;

            height:70px;

            margin:0 auto;

            padding:0;

            border:0;

            border-radius:0;

            background:transparent;

            color:#9CA3AF;

            stroke-width:1.6;

        }

        .user-activity-page .ua-empty-title{

            margin:16px 0 0;

            font-size:22px;

            font-weight:700;

            line-height:1.35;

            color:#667085;

        }

        .user-activity-page .ua-empty-copy{

            max-width:520px;

            margin:7px auto 0;

            color:#98A2B3;

            font-size:13px;

            font-weight:400;

            line-height:1.6;

        }

        .user-activity-page .ua-empty-copy code{color:#667085}

        @media(max-width:1260px){

            .user-activity-page .ua-filter{

                grid-template-columns:repeat(3,minmax(0,1fr));

                align-items:end;

                gap:12px;

            }

            .user-activity-page .ua-filter-intro{grid-column:1/-1}

            .user-activity-page .ua-filter > button,

            .user-activity-page .ua-filter > a{width:100%}

        }

        @media(max-width:767px){

            .user-activity-page .ua-header{margin-bottom:14px !important}

            .user-activity-page .ua-title{font-size:25px !important}

            .user-activity-page .ua-eyebrow{font-size:9.5px !important}

            .user-activity-page .ua-copy{font-size:10.5px !important}

            .user-activity-page .ua-filter{

                grid-template-columns:1fr 1fr;

                gap:10px;

            }

            .user-activity-page .ua-filter-intro{grid-column:1/-1}

            .user-activity-page .ua-filter > button,

            .user-activity-page .ua-filter > a{width:100%}

            .user-activity-page .ua-table-head{

                flex-direction:column;

                align-items:flex-start;

                gap:7px;

                padding:17px 16px;

            }

            .user-activity-page .ua-pagination{

                flex-direction:column;

                align-items:flex-start;

                gap:12px;

            }

            .user-activity-page .ua-pagination nav{width:100%;flex-wrap:wrap}

            .user-activity-page .ua-empty-icon{width:58px;height:58px}

            .user-activity-page .ua-empty-title{font-size:20px}

            .user-activity-page .ua-empty-copy{font-size:12px}

        }

        @media(max-width:479px){

            .user-activity-page .ua-filter{grid-template-columns:1fr}

            .user-activity-page .ua-filter-intro{grid-column:auto}

        }

        @media(prefers-reduced-motion:reduce){

            .user-activity-page .ua-select-trigger,

            .user-activity-page .ua-row-check{transition:none !important}

        }

    
        /* Reference parity — User Activity Log presentation only. */
        .user-activity-page {
            --ua-gold: #c88912;
            --ua-gold-strong: #a96d08;
            --ua-ink: #29323b;
            --ua-muted: #707b86;
            max-width: 1640px !important;
            min-width: 0;
            padding: 2px 2px 32px;
            font-size: 12px;
            line-height: 1.5;
            container-type: inline-size;
        }
        .user-activity-page *, .user-activity-page *::before, .user-activity-page *::after { box-sizing: border-box; }
        .user-activity-page .ua-header { padding: 2px 0 16px !important; margin-bottom: 0 !important; border-bottom: 0 !important; }
        .user-activity-page .ua-eyebrow { color: #a87316 !important; font-size: 10.5px !important; font-weight: 800 !important; line-height: 1.2 !important; }
        .user-activity-page .ua-title { margin-top: 5px !important; font-size: clamp(29px,2.35vw,34px) !important; font-weight: 750 !important; line-height: 1.04 !important; letter-spacing: -.035em !important; }
        .user-activity-page .ua-filter { grid-template-columns: minmax(220px,1fr) minmax(140px,.7fr) minmax(140px,.7fr) minmax(130px,.6fr) 80px 72px; gap: 10px; margin-top: 18px; }
        .user-activity-page .ua-filter-intro strong { color: #29323b; font-size: 14px; }
        .user-activity-page .ua-filter-label { margin-bottom: 6px; color: #4b5560; font-size: 11px; font-weight: 750; }
        .user-activity-page :is(.ua-select-trigger,.ua-native-select),
        .user-activity-page .ua-filter > :is(button,a) { min-height: 42px !important; height: 42px !important; border-radius: 9px !important; font-size: 12px !important; }
        .user-activity-page :is(.ua-select-trigger,.ua-native-select) { background: #fff !important; border-color: #dfe3e8 !important; color: #333d47 !important; }
        .user-activity-page .ua-select-trigger { padding-inline: 12px !important; font-weight: 650 !important; }
        .user-activity-page .ua-select-chevron { width: 14px; height: 14px; flex-basis: 14px; color: #596470 !important; }
        .user-activity-page .ua-filter > :is(button,a) { padding-inline: 12px !important; font-size: 11px !important; }
        .user-activity-page .ua-filter > button { box-shadow: 0 6px 16px rgba(201,132,8,.14) !important; }
        .user-activity-page .ua-filter > a { border-color: transparent !important; background: transparent !important; color: #667085 !important; }
        .user-activity-page .ua-custom-select.is-enhanced .ua-native-select { width: 1px !important; min-height: 0 !important; height: 1px !important; }
        .user-activity-page .ua-select-menu { top: calc(100% + 6px); max-height: 270px; overflow-y: auto; padding: 5px; border-radius: 12px; border-color: #e1e5e9; box-shadow: 0 18px 42px rgba(31,41,55,.13),0 4px 12px rgba(31,41,55,.04); }
        .user-activity-page .ua-select-option { min-height: 39px; padding: 8px 10px; border-radius: 8px; font-size: 11.5px; font-weight: 550; }
        .user-activity-page .ua-select-option[aria-selected="true"] { background: #f3f4f6 !important; color: #475467 !important; }
        .user-activity-page .ua-select-option[aria-selected="true"]::after { color: #64748b !important; }
        .user-activity-page .ua-table-panel { margin-top: 18px; border: 1px solid #e5e7eb; border-radius: 17px; box-shadow: 0 2px 5px rgba(15,23,42,.045),0 10px 24px rgba(15,23,42,.075),0 24px 54px rgba(15,23,42,.095),inset 0 1px 0 rgba(255,255,255,.92); }
        .user-activity-page .ua-table-head { min-height: 72px; padding: 16px 18px; flex-wrap: wrap; }
        .user-activity-page .ua-table-head h2 { font-size: 14px; font-weight: 750; }
        .user-activity-page .ua-table-head p { color: #707b86; font-size: 10.5px; }
        .user-activity-page .ua-table { min-width: 1120px; }
        .user-activity-page .ua-table th { padding: 12px 14px; color: #667085; font-size: 10.5px; font-weight: 650; letter-spacing: .045em; }
        .user-activity-page .ua-table td { padding: 14px; color: #4c545e; font-size: 12px; line-height: 1.5; }
        .user-activity-page .ua-name { color: #29323b; font-size: 12px; font-weight: 650; }
        .user-activity-page .ua-sub { color: #707b86; font-size: 10.5px; }
        .user-activity-page .ua-source { font-size: 11px; }
        .user-activity-page .ua-row-check { width: 17px; height: 17px; }
        .user-activity-page tr.ua-is-selected td { background: #f1f5f9 !important; }
        .user-activity-page .ua-selection-bar { padding: 11px 18px; font-size: 11px; }
        .user-activity-page .ua-pagination { min-height: 60px; padding: 13px 18px; flex-wrap: wrap; gap: 12px; }
        .user-activity-page .ua-pagination-summary { color: #707b86; font-size: 11px; }
        .user-activity-page :is(.ua-page-link,.ua-page-current,.ua-page-disabled) { min-width: 34px; height: 34px; border-radius: 9px; font-size: 11px; }
        .user-activity-page .ua-empty { min-height: 220px; padding: 40px 20px; }
        .user-activity-page .ua-empty-icon { width: 58px; height: 58px; }
        .user-activity-page .ua-empty-title { font-size: 18px; }
        .user-activity-page .ua-empty-copy { color: #707b86; font-size: 11.5px; }
        /* Hide scrollbar chrome, retaining touch, wheel and keyboard scrolling. */
        .user-activity-page,
        .user-activity-page :is(.ua-table-wrap,.ua-select-menu) { scrollbar-width: none !important; -ms-overflow-style: none; }
        .user-activity-page::-webkit-scrollbar,
        .user-activity-page .ua-table-wrap::-webkit-scrollbar,
        .user-activity-page .ua-select-menu::-webkit-scrollbar { display: none !important; width: 0; height: 0; }
        .user-activity-page :is(button,a,select,.ua-table-wrap):focus-visible { outline: 2px solid #9b6710 !important; outline-offset: 3px; }
        @container (max-width: 1100px) {
            .user-activity-page .ua-filter { grid-template-columns: repeat(3,minmax(0,1fr)); }
            .user-activity-page .ua-filter-intro { grid-column: 1 / -1; }
        }
        @container (max-width: 600px) {
            .user-activity-page .ua-filter { grid-template-columns: repeat(2,minmax(0,1fr)); }
            .user-activity-page .ua-filter-intro { grid-column: 1 / -1; }
            .user-activity-page .ua-pagination { flex-direction: column; align-items: flex-start; }
        }
        @container (max-width: 400px) {
            .user-activity-page .ua-filter { grid-template-columns: 1fr; }
        }
        @media (max-width: 767px) {
            .user-activity-page .ua-title { font-size: 29px !important; }
            .user-activity-page .ua-eyebrow { font-size: 9.5px !important; }
            .user-activity-page .ua-table-panel { border-radius: 14px; box-shadow: 0 2px 4px rgba(15,23,42,.035),0 8px 18px rgba(15,23,42,.06),0 18px 38px rgba(15,23,42,.07); }
            .user-activity-page :is(.ua-select-trigger,.ua-native-select),
            .user-activity-page .ua-filter > :is(button,a) { min-height: 44px !important; height: 44px !important; }
        }
        @media (prefers-reduced-motion: reduce) {
            .user-activity-page * { animation: none !important; transition: none !important; scroll-behavior: auto !important; }
        }

</style>

    <div class="ua-header">

        <div>

            <p class="ua-eyebrow">Security & Audit</p>

            <h1 class="ua-title">User Activity <span>Log</span></h1>

            

        </div>

    </div>

    <form method="GET" action="{{ route('admin.user-activity') }}" class="ua-filter ua-main-shadow">

        <div class="ua-filter-intro"><strong>Filter user audit trail</strong></div>

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

            @if($logs->count())

                <div class="ua-selection-bar" data-ua-selection-bar hidden aria-live="polite">

                    <span data-ua-selected-count>0 selected on this page</span>

                    <button type="button" class="ua-selection-clear" data-ua-clear-selection>Clear selection</button>

                </div>

            @endif

            <div class="ua-table-wrap" tabindex="0" role="region" aria-label="User activity records">

                @if($logs->count())

                    <table class="ua-table">

                        <thead><tr><th class="ua-check-cell"><input type="checkbox" class="ua-row-check" data-ua-select-all aria-label="Select all audit records on this page"></th><th>Date & time</th><th>Account</th><th>Role</th><th>Event</th><th>Performed by</th><th>IP address</th><th>Description</th></tr></thead>

                        <tbody>

                            @foreach($logs as $event)

                                <tr data-ua-audit-row>

                                    <td class="ua-check-cell"><input type="checkbox" class="ua-row-check" data-ua-row-check aria-label="Select audit record {{ $loop->iteration }} on this page"></td>

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

                    <div class="ua-empty" role="status">

                        <svg class="ua-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><circle cx="10.7" cy="10.7" r="6.8"/><path stroke-linecap="round" d="m16 16 4 4M8.5 9h4.4m-4.4 3h2.9"/></svg>

                        <h3 class="ua-empty-title">No matching user activity records</h3>

                        <p class="ua-empty-copy">Change the account role or event type filter to view another group of audit records.</p>

                    </div>

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

            <div class="ua-empty" role="status">

                <svg class="ua-empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/><circle cx="18" cy="18" r="4" fill="#FFFFFF"/><path stroke-linecap="round" d="M18 16v2.3m0 1.7v.1"/></svg>

                <h3 class="ua-empty-title">Activity log unavailable</h3>

                <p class="ua-empty-copy">The <code>admin_user_activities</code> table is not available. Run the existing project migrations to enable this view.</p>

            </div>

        @endif

    </section>

    <script>

        (() => {

            // Read-only selection. Never submits a form or changes any audit record.

            const initializeAuditSelection = () => {

                window.__SARI_USER_ACTIVITY_SELECTION_ABORT__?.abort();

                const page = document.querySelector('.user-activity-page');

                if (!page) return;

                const controller = new AbortController();

                window.__SARI_USER_ACTIVITY_SELECTION_ABORT__ = controller;

                const all = page.querySelector('[data-ua-select-all]');

                const rows = Array.from(page.querySelectorAll('[data-ua-audit-row]'));

                const checks = rows.map(row => row.querySelector('[data-ua-row-check]')).filter(Boolean);

                const bar = page.querySelector('[data-ua-selection-bar]');

                const count = page.querySelector('[data-ua-selected-count]');

                const clear = page.querySelector('[data-ua-clear-selection]');

                if (!all || !checks.length || !bar || !count || !clear) return;

                const sync = () => {

                    const checked = checks.filter(check => check.checked).length;

                    all.checked = checked === checks.length;

                    all.indeterminate = checked > 0 && checked < checks.length;

                    rows.forEach(row => row.classList.toggle('ua-is-selected', Boolean(row.querySelector('[data-ua-row-check]')?.checked)));

                    count.textContent = `${checked} ${checked === 1 ? 'record' : 'records'} selected on this page`;

                    bar.hidden = checked === 0;

                };

                all.addEventListener('change', () => {

                    checks.forEach(check => { check.checked = all.checked; });

                    sync();

                }, { signal:controller.signal });

                checks.forEach(check => check.addEventListener('change', sync, { signal:controller.signal }));

                clear.addEventListener('click', () => {

                    checks.forEach(check => { check.checked = false; });

                    sync();

                    all.focus();

                }, { signal:controller.signal });

                sync();

            };

            window.__SARI_INIT_USER_ACTIVITY_SELECTION__ = initializeAuditSelection;

            if (!window.__SARI_USER_ACTIVITY_LIVEWIRE_LISTENER__) {

                window.__SARI_USER_ACTIVITY_LIVEWIRE_LISTENER__ = true;

                document.addEventListener('livewire:navigated', () => window.__SARI_INIT_USER_ACTIVITY_SELECTION__?.());

            }

            if (document.readyState === 'loading') {

                document.addEventListener('DOMContentLoaded', initializeAuditSelection, { once:true });

            } else {

                initializeAuditSelection();

            }

        })();

    </script>

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
