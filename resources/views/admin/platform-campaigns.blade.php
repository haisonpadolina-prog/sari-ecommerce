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
        /* ============================================================
           EVENTS & PROMOTIONS — CLEAN MODERN ADMIN UI
           Visual-only redesign. Existing campaign routes, form names,
           states, toggles, schedule logic, and JavaScript stay intact.
           ============================================================ */

        .campaign-page {
            --cp-gold: #D59617;
            --cp-gold-strong: #B97805;
            --cp-gold-soft: #FFF9EC;
            --cp-ink: #111827;
            --cp-text: #344054;
            --cp-muted: #7C8592;
            --cp-subtle: #98A2B3;
            --cp-line: #E5E7EB;
            --cp-line-strong: #D1D5DB;
            --cp-divider: #ECEFF2;
            --cp-soft: #F8FAFC;
            --cp-danger: #B05757;
            --cp-success: #3E7B58;
            --cp-shadow:
                0 2px 5px rgba(15,23,42,.045),
                0 10px 24px rgba(15,23,42,.075),
                0 24px 54px rgba(15,23,42,.085),
                inset 0 1px 0 rgba(255,255,255,.92);
            --cp-shadow-soft:
                0 1px 3px rgba(15,23,42,.035),
                0 7px 18px rgba(15,23,42,.055),
                inset 0 1px 0 rgba(255,255,255,.9);

            width: 100%;
            max-width: 1800px !important;
            margin-inline: auto;
            padding-bottom: 30px !important;
            color: var(--cp-ink);
            font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .campaign-page *,
        .campaign-page *::before,
        .campaign-page *::after {
            box-sizing: border-box;
        }

        .campaign-page button,
        .campaign-page input,
        .campaign-page select,
        .campaign-page textarea,
        .campaign-page a {
            transition:
                border-color .15s ease,
                background-color .15s ease,
                color .15s ease,
                box-shadow .15s ease,
                opacity .15s ease;
        }

        /* ---------------- OPEN DOCUMENT HEADER ---------------- */
        .cp-head {
            padding: 2px 2px 17px !important;
            border-bottom: 1px solid var(--cp-divider) !important;
            background: transparent;
        }

        .cp-eyebrow {
            margin: 0 0 5px !important;
            color: var(--cp-gold-strong) !important;
            font-size: 8px !important;
            font-weight: 800 !important;
            line-height: 1.15 !important;
            letter-spacing: .16em !important;
            text-transform: uppercase;
        }

        .cp-title {
            margin: 0 !important;
            color: var(--cp-ink) !important;
            font-size: clamp(29px, 2.1vw, 36px) !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            letter-spacing: -.035em !important;
        }

        .cp-title span {
            color: var(--cp-gold) !important;
        }

        .cp-copy {
            max-width: 920px !important;
            margin: 8px 0 0 !important;
            color: var(--cp-muted) !important;
            font-size: 10.5px !important;
            line-height: 1.65 !important;
        }

        /* ---------------- FEEDBACK / TIME ---------------- */
        .cp-alert {
            margin-top: 14px !important;
            border: 1px solid #D9E8DD !important;
            border-radius: 11px !important;
            background: #F5FAF6 !important;
            padding: 11px 13px !important;
            color: #477157 !important;
            font-size: 9px !important;
            line-height: 1.55 !important;
            box-shadow: none !important;
        }

        .cp-alert.cp-error {
            border-color: #F0D3D3 !important;
            background: #FFF7F7 !important;
            color: #9A5555 !important;
        }

        /* ---------------- MAIN WORKSPACE ---------------- */
        .cp-layout {
            display: grid !important;
            grid-template-columns: minmax(0, 1.18fr) minmax(390px, .82fr) !important;
            gap: 16px !important;
            margin-top: 16px !important;
            align-items: start !important;
        }

        .cp-panel {
            overflow: hidden !important;
            border: 1px solid var(--cp-line) !important;
            border-radius: 14px !important;
            background: #FFFFFF !important;
            box-shadow: var(--cp-shadow) !important;
        }

        .cp-panel-head {
            min-height: 66px !important;
            padding: 14px 16px !important;
            border-bottom: 1px solid var(--cp-divider) !important;
            background: #FFFFFF !important;
        }

        .cp-panel-head h2 {
            margin: 0 !important;
            color: #1D2939 !important;
            font-size: 12px !important;
            font-weight: 700 !important;
            line-height: 1.35 !important;
        }

        .cp-panel-head p {
            max-width: 760px;
            margin: 4px 0 0 !important;
            color: #8A94A3 !important;
            font-size: 8.5px !important;
            line-height: 1.55 !important;
        }

        .cp-body {
            padding: 16px !important;
        }

        .cp-grid {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 13px !important;
        }

        .cp-span-2 {
            grid-column: 1 / -1;
        }

        /* ---------------- FORM CONTROLS ---------------- */
        .cp-field label,
        .cp-label {
            display: block !important;
            margin-bottom: 6px !important;
            color: #475467 !important;
            font-size: 8.7px !important;
            font-weight: 700 !important;
            line-height: 1.35 !important;
        }

        .cp-field input,
        .cp-field select,
        .cp-field textarea {
            width: 100% !important;
            border: 1px solid #DDE1E6 !important;
            border-radius: 10px !important;
            background: #FFFFFF !important;
            color: #29313B !important;
            font: 500 9.5px 'Poppins', sans-serif !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .cp-field input,
        .cp-field select {
            height: 42px !important;
            padding: 0 11px !important;
        }

        .cp-field textarea {
            min-height: 92px !important;
            resize: vertical !important;
            padding: 10px 11px !important;
            line-height: 1.6 !important;
        }

        .cp-field input::placeholder,
        .cp-field textarea::placeholder {
            color: #A5ADB7 !important;
        }

        .cp-field input:hover,
        .cp-field select:hover,
        .cp-field textarea:hover {
            border-color: #C9CFD6 !important;
        }

        .cp-field input:focus,
        .cp-field select:focus,
        .cp-field textarea:focus {
            border-color: var(--cp-gold) !important;
            box-shadow: 0 0 0 3px rgba(213,150,23,.09) !important;
        }

        .cp-field input[readonly] {
            border-color: #E5E7EB !important;
            background: #F7F8FA !important;
            color: #98A2B3 !important;
            cursor: not-allowed !important;
        }

        .cp-help {
            margin-top: 5px !important;
            color: #8A94A3 !important;
            font-size: 7.5px !important;
            font-weight: 400 !important;
            line-height: 1.5 !important;
        }

        .cp-multi {
            min-height: 132px !important;
            height: 132px !important;
            padding: 7px !important;
        }

        .cp-multi option {
            border-radius: 5px;
            padding: 5px 7px;
        }

        /* ---------------- IMMEDIATE / BENEFIT GROUPS ---------------- */
        .cp-immediate {
            grid-column: 1 / -1 !important;
            display: flex !important;
            align-items: flex-start !important;
            gap: 10px !important;
            border: 1px solid #E5E7EB !important;
            border-radius: 11px !important;
            background: #F8F9FA !important;
            padding: 11px 12px !important;
        }

        .cp-immediate input[type="checkbox"],
        .cp-toggle input[type="checkbox"] {
            appearance: none !important;
            -webkit-appearance: none !important;
            position: relative !important;
            display: inline-grid !important;
            width: 16px !important;
            height: 16px !important;
            flex: 0 0 16px !important;
            place-items: center !important;
            border: 1px solid #CBD2D9 !important;
            border-radius: 5px !important;
            background: #FFFFFF !important;
            cursor: pointer !important;
            transition: border-color .15s ease, background-color .15s ease, box-shadow .15s ease !important;
        }

        .cp-immediate input[type="checkbox"]::after,
        .cp-toggle input[type="checkbox"]::after {
            content: "✓";
            color: #FFFFFF !important;
            font-family: Arial, sans-serif !important;
            font-size: 11px !important;
            font-weight: 800 !important;
            line-height: 1 !important;
            opacity: 0;
            transform: translateY(-.2px) scale(.82);
        }

        .cp-immediate input[type="checkbox"]:checked,
        .cp-toggle input[type="checkbox"]:checked {
            border-color: var(--cp-gold) !important;
            background: var(--cp-gold) !important;
        }

        .cp-immediate input[type="checkbox"]:checked::after,
        .cp-toggle input[type="checkbox"]:checked::after {
            opacity: 1;
            transform: translateY(-.2px) scale(1);
        }

        .cp-immediate input[type="checkbox"]:focus-visible,
        .cp-toggle input[type="checkbox"]:focus-visible {
            outline: none !important;
            border-color: var(--cp-gold) !important;
            box-shadow: 0 0 0 3px rgba(213,150,23,.12) !important;
        }

        .cp-immediate input[type="checkbox"] {
            margin-top: 1px !important;
        }

        .cp-immediate strong {
            display: block !important;
            color: #344054 !important;
            font-size: 9px !important;
            font-weight: 700 !important;
        }

        .cp-immediate span span {
            display: block !important;
            margin-top: 3px !important;
            color: #667085 !important;
            font-size: 7.6px !important;
            line-height: 1.55 !important;
        }

        .cp-section {
            grid-column: 1 / -1 !important;
            border: 1px solid #E7EAEE !important;
            border-radius: 12px !important;
            background: #FAFBFC !important;
            padding: 13px !important;
        }

        .cp-section-head {
            display: flex !important;
            align-items: flex-start !important;
            justify-content: space-between !important;
            gap: 16px !important;
        }

        .cp-section-head > div {
            min-width: 0;
        }

        .cp-section-head strong {
            color: #344054 !important;
            font-size: 10px !important;
            font-weight: 700 !important;
        }

        .cp-toggle {
            display: inline-flex !important;
            flex: 0 0 auto !important;
            align-items: center !important;
            gap: 7px !important;
            color: #56606C !important;
            font-size: 8px !important;
            font-weight: 700 !important;
            white-space: nowrap;
        }

        .cp-section .cp-grid {
            margin-top: 12px !important;
            padding-top: 12px !important;
            border-top: 1px solid #ECEFF2 !important;
        }

        /* ---------------- FORM ACTIONS ---------------- */
        .cp-actions {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 8px !important;
            margin-top: 16px !important;
            padding-top: 14px !important;
            border-top: 1px solid var(--cp-divider) !important;
        }

        .cp-btn {
            display: inline-flex !important;
            min-height: 40px !important;
            height: 40px !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            border-radius: 9px !important;
            padding: 0 14px !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 8.6px !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            box-shadow: none !important;
        }

        .cp-btn-primary {
            border: 1px solid var(--cp-gold) !important;
            background: var(--cp-gold) !important;
            color: #FFFFFF !important;
            box-shadow: 0 4px 10px rgba(213,150,23,.11) !important;
        }

        .cp-btn-primary:hover,
        .cp-btn-primary:focus-visible {
            outline: none !important;
            border-color: var(--cp-gold-strong) !important;
            background: var(--cp-gold-strong) !important;
        }

        .cp-btn-secondary {
            border: 1px solid #DDE1E6 !important;
            background: #FFFFFF !important;
            color: #667085 !important;
        }

        .cp-btn-secondary:hover,
        .cp-btn-secondary:focus-visible {
            outline: none !important;
            border-color: #C9CFD6 !important;
            background: #F9FAFB !important;
            color: #344054 !important;
        }

        .cp-btn-danger {
            border: 1px solid #E7CCCC !important;
            background: #FFF8F8 !important;
            color: #A55757 !important;
        }

        .cp-btn-danger:hover,
        .cp-btn-danger:focus-visible {
            outline: none !important;
            border-color: #DDB9B9 !important;
            background: #FFF1F1 !important;
            color: #934A4A !important;
        }

        /* ---------------- CAMPAIGN SCHEDULE — CLEAN FLAT LIST ---------------- */
        .cp-panel-list .cp-panel-head {
            background: #FFFFFF !important;
        }

        .cp-list {
            display: block !important;
            padding: 0 !important;
            background: #FFFFFF !important;
        }

        .cp-item {
            position: relative !important;
            overflow: visible !important;
            border: 0 !important;
            border-bottom: 1px solid #ECEFF2 !important;
            border-radius: 0 !important;
            background: #FFFFFF !important;
            padding: 15px 16px !important;
            box-shadow: none !important;
        }

        .cp-item:last-child {
            border-bottom: 0 !important;
        }

        .cp-item::before {
            display: none !important;
        }

        .cp-item:hover {
            border-color: #ECEFF2 !important;
            background: #FAFBFC !important;
        }

        .cp-item-top {
            display: flex !important;
            align-items: flex-start !important;
            justify-content: space-between !important;
            gap: 14px !important;
        }

        .cp-item-top > div {
            min-width: 0;
        }

        .cp-item h3 {
            margin: 0 !important;
            color: #172033 !important;
            font-size: 11.5px !important;
            font-weight: 750 !important;
            line-height: 1.35 !important;
            letter-spacing: -.01em !important;
        }

        .cp-meta {
            margin-top: 5px !important;
            color: #7C8592 !important;
            font-size: 7.8px !important;
            line-height: 1.65 !important;
        }

        .cp-status {
            display: inline-flex !important;
            min-height: 22px !important;
            flex: 0 0 auto !important;
            align-items: center !important;
            gap: 5px !important;
            border: 0 !important;
            border-radius: 999px !important;
            background: #F4F6F8 !important;
            padding: 0 8px !important;
            color: #667085 !important;
            font-size: 6.8px !important;
            font-weight: 750 !important;
            white-space: nowrap !important;
        }

        .cp-status::before {
            content: "";
            width: 5px !important;
            height: 5px !important;
            border-radius: 50% !important;
            background: currentColor !important;
        }

        .cp-status.Active {
            background: #ECFDF3 !important;
            color: #15803D !important;
        }

        .cp-status.Scheduled {
            background: #FFF7E6 !important;
            color: #A56A00 !important;
        }

        .cp-status.Paused,
        .cp-status.Draft {
            background: #F2F4F7 !important;
            color: #667085 !important;
        }

        .cp-benefits {
            display: flex !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 0 !important;
            margin-top: 9px !important;
            color: #667085 !important;
        }

        .cp-chip {
            display: inline-flex !important;
            min-height: 0 !important;
            align-items: center !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            padding: 0 !important;
            color: #667085 !important;
            font-size: 7.4px !important;
            font-weight: 600 !important;
            line-height: 1.45 !important;
        }

        .cp-chip + .cp-chip::before {
            content: "•";
            margin: 0 7px !important;
            color: #C0C6CE !important;
            font-size: 7px !important;
        }

        .cp-item-actions {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            gap: 5px !important;
            margin-top: 11px !important;
            padding-top: 0 !important;
            border-top: 0 !important;
        }

        .cp-item-actions form {
            display: inline-flex !important;
            margin: 0 !important;
        }

        .cp-action-icon {
            display: inline-grid !important;
            width: 30px !important;
            height: 30px !important;
            min-width: 30px !important;
            place-items: center !important;
            border: 0 !important;
            border-radius: 8px !important;
            background: transparent !important;
            padding: 0 !important;
            text-decoration: none !important;
            cursor: pointer !important;
            box-shadow: none !important;
            transition: background-color .14s ease, color .14s ease, transform .14s ease !important;
        }

        .cp-action-icon svg {
            width: 15px !important;
            height: 15px !important;
            display: block !important;
            stroke: currentColor !important;
        }

        .cp-action-icon:hover,
        .cp-action-icon:focus-visible {
            outline: none !important;
            transform: translateY(-1px) !important;
        }

        .cp-action-edit {
            color: #1677FF !important;
        }

        .cp-action-edit:hover,
        .cp-action-edit:focus-visible {
            background: #EAF3FF !important;
            color: #0068F0 !important;
        }

        .cp-action-pause {
            color: #111827 !important;
        }

        .cp-action-pause:hover,
        .cp-action-pause:focus-visible {
            background: #F1F3F5 !important;
            color: #000000 !important;
        }

        .cp-action-publish {
            color: #111827 !important;
        }

        .cp-action-publish:hover,
        .cp-action-publish:focus-visible {
            background: #F1F3F5 !important;
            color: #000000 !important;
        }

        .cp-action-delete {
            color: #FF2D2D !important;
        }

        .cp-action-delete:hover,
        .cp-action-delete:focus-visible {
            background: #FFF0F0 !important;
            color: #E60000 !important;
        }

        .cp-empty {
            padding: 44px 18px !important;
            color: #7C8592 !important;
            font-size: 9px !important;
            line-height: 1.6 !important;
            text-align: center !important;
        }

        [data-target-panel][hidden],
        [data-benefit-panel][hidden] {
            display: none !important;
        }

        /* Laravel pagination inside schedule panel */
        .cp-panel-list nav {
            font-family: 'Poppins', sans-serif !important;
        }

        /* ---------------- RESPONSIVE ---------------- */
        @media (max-width: 1200px) {
            .cp-layout {
                grid-template-columns: 1fr !important;
            }

            .cp-panel-list {
                order: 2;
            }
        }

        @media (max-width: 700px) {
            .campaign-page {
                padding-bottom: 20px !important;
            }

            .cp-title {
                font-size: clamp(27px, 8vw, 34px) !important;
            }

            .cp-copy {
                font-size: 10px !important;
            }

            .cp-grid {
                grid-template-columns: 1fr !important;
            }

            .cp-span-2,
            .cp-section {
                grid-column: auto !important;
            }

            .cp-section-head {
                flex-direction: column !important;
            }

            .cp-actions {
                align-items: stretch !important;
                flex-direction: column-reverse !important;
            }

            .cp-btn {
                width: 100% !important;
            }

            .cp-item-top {
                flex-direction: column !important;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .campaign-page * {
                transition: none !important;
                scroll-behavior: auto !important;
            }
        }


        /* ---------------- CLEAN CUSTOM SINGLE-SELECT DROPDOWNS ---------------- */
        .cp-select-shell {
            position: relative !important;
            width: 100% !important;
            z-index: 1;
        }

        .cp-select-shell.is-open {
            z-index: 80 !important;
        }

        .cp-native-select {
            position: absolute !important;
            width: 1px !important;
            height: 1px !important;
            margin: -1px !important;
            padding: 0 !important;
            overflow: hidden !important;
            clip: rect(0 0 0 0) !important;
            clip-path: inset(50%) !important;
            white-space: nowrap !important;
            border: 0 !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        .cp-select-trigger {
            display: flex !important;
            width: 100% !important;
            height: 42px !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 10px !important;
            border: 1px solid #DDE1E6 !important;
            border-radius: 10px !important;
            background: #FFFFFF !important;
            padding: 0 11px !important;
            color: #29313B !important;
            font: 500 9.5px 'Poppins', sans-serif !important;
            text-align: left !important;
            cursor: pointer !important;
            box-shadow: 0 1px 2px rgba(15,23,42,.02) !important;
        }

        .cp-select-trigger:hover {
            border-color: #C9CFD6 !important;
            background: #FFFFFF !important;
        }

        .cp-select-trigger:focus-visible,
        .cp-select-shell.is-open .cp-select-trigger {
            outline: none !important;
            border-color: var(--cp-gold) !important;
            box-shadow: 0 0 0 3px rgba(213,150,23,.09) !important;
        }

        .cp-select-trigger-label {
            min-width: 0 !important;
            overflow: hidden !important;
            color: #29313B !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
        }

        .cp-select-chevron {
            width: 15px !important;
            height: 15px !important;
            flex: 0 0 15px !important;
            color: #667085 !important;
            transition: transform .16s ease !important;
        }

        .cp-select-shell.is-open .cp-select-chevron {
            transform: rotate(180deg) !important;
        }

        .cp-select-menu {
            position: absolute !important;
            z-index: 100 !important;
            top: calc(100% + 6px) !important;
            right: 0 !important;
            left: 0 !important;
            display: grid !important;
            max-height: 240px !important;
            overflow-y: auto !important;
            gap: 2px !important;
            padding: 5px !important;
            border: 1px solid #E2E6EA !important;
            border-radius: 11px !important;
            background: #FFFFFF !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
            transform: translateY(-4px) !important;
            box-shadow:
                0 12px 28px rgba(15,23,42,.10),
                0 3px 8px rgba(15,23,42,.045) !important;
            transition: opacity .14s ease, transform .14s ease, visibility .14s ease !important;
            scrollbar-width: thin;
            scrollbar-color: #D1D5DB transparent;
        }

        .cp-select-shell.is-open .cp-select-menu {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
            transform: translateY(0) !important;
        }

        .cp-select-option {
            display: flex !important;
            width: 100% !important;
            min-height: 36px !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 10px !important;
            border: 0 !important;
            border-radius: 8px !important;
            background: transparent !important;
            padding: 0 9px !important;
            color: #344054 !important;
            font: 500 9px 'Poppins', sans-serif !important;
            text-align: left !important;
            cursor: pointer !important;
        }

        .cp-select-option:hover,
        .cp-select-option:focus-visible {
            outline: none !important;
            background: #F8FAFC !important;
            color: #1D2939 !important;
        }

        .cp-select-option.is-selected {
            background: #F8FAFC !important;
            color: #1D2939 !important;
            font-weight: 650 !important;
        }

        .cp-select-option-check {
            display: grid !important;
            width: 18px !important;
            height: 18px !important;
            flex: 0 0 18px !important;
            place-items: center !important;
            border-radius: 6px !important;
            background: transparent !important;
            color: transparent !important;
        }

        .cp-select-option.is-selected .cp-select-option-check {
            background: var(--cp-gold) !important;
            color: #FFFFFF !important;
        }

        .cp-select-option-check svg {
            width: 11px !important;
            height: 11px !important;
            display: block !important;
        }

        @media (max-width: 700px) {
            .cp-select-trigger {
                min-height: 44px !important;
                height: 44px !important;
                font-size: 9.8px !important;
            }

            .cp-select-option {
                min-height: 39px !important;
                font-size: 9.4px !important;
            }
        }

    </style>

    <div class="cp-head">

        <p class="cp-eyebrow">Marketing & Buyer Promotions</p>

        <h1 class="cp-title">Events & <span>Promotions</span></h1>

        <p class="cp-copy">Create scheduled SARI campaigns such as 1.1, 2.2, 11.11, and 12.12. Platform discounts only change what Buyers pay; Seller product prices remain untouched and Seller receivables are preserved separately.</p>

    </div>



    @if(session('success'))<div class="cp-alert">{{ session('success') }}</div>@endif

    @if($errors->any())<div class="cp-alert cp-error">{{ $errors->first() }}</div>@endif

    @if(!$campaignFeatureEnabled)

        <div class="cp-alert cp-error">

            Platform campaigns are currently disabled in Platform Settings. Campaign records remain editable, but Buyer event discounts and SARI platform vouchers will not apply until the feature is enabled again.

        </div>

    @endif




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

                            <div class="cp-item-actions" aria-label="Campaign actions">

                                <a class="cp-action-icon cp-action-edit" href="{{ route('admin.platform-campaigns',['edit'=>$campaign->id]) }}" title="Edit campaign" aria-label="Edit campaign">
                                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg>
                                </a>

                                @if($campaign->state==='published')
                                    <form method="POST" action="{{ route('admin.platform-campaigns.pause',$campaign) }}">@csrf
                                        <button type="submit" class="cp-action-icon cp-action-pause" title="Pause campaign" aria-label="Pause campaign">
                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.9" stroke-linecap="round"><path d="M9 6v12"></path><path d="M15 6v12"></path></svg>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.platform-campaigns.publish',$campaign) }}">@csrf
                                        <button type="submit" class="cp-action-icon cp-action-publish" title="{{ $campaign->state === 'paused' ? 'Resume campaign' : 'Publish campaign' }}" aria-label="{{ $campaign->state === 'paused' ? 'Resume campaign' : 'Publish campaign' }}">
                                            <svg viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M8 5.5v13l10-6.5-10-6.5Z"></path></svg>
                                        </button>
                                    </form>
                                @endif

                                @if($campaign->redemptions_count===0)
                                    <form method="POST" action="{{ route('admin.platform-campaigns.destroy',$campaign) }}" onsubmit="return confirm('Delete this campaign?')">@csrf @method('DELETE')
                                        <button type="submit" class="cp-action-icon cp-action-delete" title="Delete campaign" aria-label="Delete campaign">
                                            <svg viewBox="0 0 24 24" fill="none" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="m7 7 1 13h8l1-13"></path><path d="M10 11v5M14 11v5"></path></svg>
                                        </button>
                                    </form>
                                @endif

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

    function closeCampaignSelects(except=null){
        document.querySelectorAll('.cp-select-shell.is-open').forEach(shell=>{
            if(shell===except)return;
            shell.classList.remove('is-open');
            shell.querySelector('.cp-select-trigger')?.setAttribute('aria-expanded','false');
        });
    }

    function enhanceCampaignSelect(select){
        if(!select || select.multiple || select.dataset.cpCustomSelect==='1')return;
        select.dataset.cpCustomSelect='1';
        select.classList.add('cp-native-select');

        const shell=document.createElement('div');
        shell.className='cp-select-shell';

        const trigger=document.createElement('button');
        trigger.type='button';
        trigger.className='cp-select-trigger';
        trigger.setAttribute('aria-haspopup','listbox');
        trigger.setAttribute('aria-expanded','false');
        trigger.innerHTML=`<span class="cp-select-trigger-label"></span><svg class="cp-select-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="m7 10 5 5 5-5"></path></svg>`;

        const menu=document.createElement('div');
        menu.className='cp-select-menu';
        menu.setAttribute('role','listbox');

        const sync=()=>{
            const selected=select.options[select.selectedIndex] || select.options[0];
            trigger.querySelector('.cp-select-trigger-label').textContent=selected?.textContent?.trim() || 'Select';
            [...menu.querySelectorAll('.cp-select-option')].forEach(option=>{
                const isSelected=option.dataset.value===String(select.value);
                option.classList.toggle('is-selected',isSelected);
                option.setAttribute('aria-selected',isSelected?'true':'false');
            });
        };

        [...select.options].forEach(nativeOption=>{
            const option=document.createElement('button');
            option.type='button';
            option.className='cp-select-option';
            option.dataset.value=String(nativeOption.value);
            option.setAttribute('role','option');
            option.innerHTML=`<span>${nativeOption.textContent.trim()}</span><span class="cp-select-option-check" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m7 12 3 3 7-7"></path></svg></span>`;
            option.addEventListener('click',()=>{
                select.value=nativeOption.value;
                select.dispatchEvent(new Event('change',{bubbles:true}));
                sync();
                shell.classList.remove('is-open');
                trigger.setAttribute('aria-expanded','false');
                trigger.focus();
            });
            menu.appendChild(option);
        });

        select.parentNode.insertBefore(shell,select.nextSibling);
        shell.appendChild(trigger);
        shell.appendChild(menu);

        trigger.addEventListener('click',event=>{
            event.stopPropagation();
            const willOpen=!shell.classList.contains('is-open');
            closeCampaignSelects(shell);
            shell.classList.toggle('is-open',willOpen);
            trigger.setAttribute('aria-expanded',willOpen?'true':'false');
            if(willOpen){
                window.requestAnimationFrame(()=>menu.querySelector('.cp-select-option.is-selected')?.focus({preventScroll:true}));
            }
        });

        trigger.addEventListener('keydown',event=>{
            if(event.key==='ArrowDown' || event.key==='ArrowUp'){
                event.preventDefault();
                if(!shell.classList.contains('is-open'))trigger.click();
            }
        });

        menu.addEventListener('keydown',event=>{
            const options=[...menu.querySelectorAll('.cp-select-option')];
            const current=options.indexOf(document.activeElement);
            if(event.key==='ArrowDown'){
                event.preventDefault();
                options[Math.min(options.length-1,current+1)]?.focus();
            }else if(event.key==='ArrowUp'){
                event.preventDefault();
                options[Math.max(0,current-1)]?.focus();
            }else if(event.key==='Escape'){
                event.preventDefault();
                shell.classList.remove('is-open');
                trigger.setAttribute('aria-expanded','false');
                trigger.focus();
            }
        });

        select.addEventListener('change',sync);
        sync();
    }

    function initCampaignSelects(root=document){
        root.querySelectorAll('.cp-field select:not([multiple])').forEach(enhanceCampaignSelect);
    }

    function initCampaignForm(){

        initCampaignSelects(document);

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

    document.addEventListener('click',event=>{
        if(!event.target.closest('.cp-select-shell'))closeCampaignSelects();
    });

    document.addEventListener('keydown',event=>{
        if(event.key==='Escape')closeCampaignSelects();
    });

    document.addEventListener('DOMContentLoaded',()=>{initCampaignForm()},{once:true});

    document.addEventListener('livewire:navigated',()=>{initCampaignForm()});

})();

</script>

@endpush
