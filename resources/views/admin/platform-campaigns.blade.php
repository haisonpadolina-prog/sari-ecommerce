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
    $shippingEnabled = (bool) old('shipping_enabled', $formCampaign?->shipping_enabled ?? false);
    $shippingBudgetTotals = $formCampaign
        ? app(\App\Services\Promotions\PlatformShippingBudgetService::class)->totals($formCampaign)
        : ['allocated' => 0, 'reserved' => 0, 'used' => 0, 'available' => 0];
    $shippingCommittedCents = $shippingBudgetTotals['reserved'] + $shippingBudgetTotals['used'];

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

            --cp-ink: var(--sari-dark-e23e64dd1e, #111827);

            --cp-text: var(--sari-dark-e23e64dd1e, #344054);

            --cp-muted: var(--sari-dark-a63aa16d7c, #7C8592);

            --cp-subtle: #98A2B3;

            --cp-line: var(--sari-dark-d8c2716225, #E5E7EB);

            --cp-line-strong: var(--sari-dark-d8c2716225, #D1D5DB);

            --cp-divider: var(--sari-dark-d8c2716225, #ECEFF2);

            --cp-soft: #F8FAFC;

            --cp-danger: #B05757;

            --cp-success: #3E7B58;

            --cp-shadow:

                0 2px 5px var(--sari-dark-1190f58155, rgba(15,23,42,.045)),

                0 10px 24px var(--sari-dark-e07575a086, rgba(15,23,42,.075)),

                0 24px 54px var(--sari-dark-0a9e89f88f, rgba(15,23,42,.085)),

                inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255,255,255,.92));

            --cp-shadow-soft:

                0 1px 3px var(--sari-dark-7593018246, rgba(15,23,42,.035)),

                0 7px 18px var(--sari-dark-fbd3b3b9f6, rgba(15,23,42,.055)),

                inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255,255,255,.9));

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

            border: 1px solid var(--sari-dark-d8c2716225, #D9E8DD) !important;

            border-radius: 11px !important;

            background: var(--sari-dark-950baec030, #F5FAF6) !important;

            padding: 11px 13px !important;

            color: var(--sari-dark-a63aa16d7c, #477157) !important;

            font-size: 9px !important;

            line-height: 1.55 !important;

            box-shadow: none !important;

        }

        .cp-alert.cp-error {

            border-color: var(--sari-dark-d8c2716225, #F0D3D3) !important;

            background: var(--sari-dark-950baec030, #FFF7F7) !important;

            color: var(--sari-dark-a63aa16d7c, #9A5555) !important;

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

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

            box-shadow: var(--cp-shadow) !important;

        }

        .cp-panel-head {

            min-height: 66px !important;

            padding: 14px 16px !important;

            border-bottom: 1px solid var(--cp-divider) !important;

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

        }

        .cp-panel-head h2 {

            margin: 0 !important;

            color: var(--sari-dark-e23e64dd1e, #1D2939) !important;

            font-size: 12px !important;

            font-weight: 700 !important;

            line-height: 1.35 !important;

        }

        .cp-panel-head p {

            max-width: 760px;

            margin: 4px 0 0 !important;

            color: var(--sari-dark-a63aa16d7c, #8A94A3) !important;

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

            color: var(--sari-dark-e23e64dd1e, #475467) !important;

            font-size: 8.7px !important;

            font-weight: 700 !important;

            line-height: 1.35 !important;

        }

        .cp-field input,

        .cp-field select,

        .cp-field textarea {

            width: 100% !important;

            border: 1px solid var(--sari-dark-d8c2716225, #DDE1E6) !important;

            border-radius: 10px !important;

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

            color: var(--sari-dark-e23e64dd1e, #29313B) !important;

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

            color: var(--sari-dark-a63aa16d7c, #A5ADB7) !important;

        }

        .cp-field input:hover,

        .cp-field select:hover,

        .cp-field textarea:hover {

            border-color: var(--sari-dark-d8c2716225, #C9CFD6) !important;

        }

        .cp-field input:focus,

        .cp-field select:focus,

        .cp-field textarea:focus {

            border-color: var(--cp-gold) !important;

            box-shadow: 0 0 0 3px var(--sari-dark-b12a5e9552, rgba(213,150,23,.09)) !important;

        }

        .cp-field input[readonly] {

            border-color: var(--sari-dark-d8c2716225, #E5E7EB) !important;

            background: var(--sari-dark-950baec030, #F7F8FA) !important;

            color: var(--sari-dark-a63aa16d7c, #98A2B3) !important;

            cursor: not-allowed !important;

        }

        .cp-help {

            margin-top: 5px !important;

            color: var(--sari-dark-a63aa16d7c, #8A94A3) !important;

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

            border: 1px solid var(--sari-dark-d8c2716225, #E5E7EB) !important;

            border-radius: 11px !important;

            background: var(--sari-dark-950baec030, #F8F9FA) !important;

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

            border: 1px solid var(--sari-dark-d8c2716225, #CBD2D9) !important;

            border-radius: 5px !important;

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

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

            box-shadow: 0 0 0 3px var(--sari-dark-3b2338cbf0, rgba(213,150,23,.12)) !important;

        }

        .cp-immediate input[type="checkbox"] {

            margin-top: 1px !important;

        }

        .cp-immediate strong {

            display: block !important;

            color: var(--sari-dark-e23e64dd1e, #344054) !important;

            font-size: 9px !important;

            font-weight: 700 !important;

        }

        .cp-immediate span span {

            display: block !important;

            margin-top: 3px !important;

            color: var(--sari-dark-a63aa16d7c, #667085) !important;

            font-size: 7.6px !important;

            line-height: 1.55 !important;

        }

        .cp-section {

            grid-column: 1 / -1 !important;

            border: 1px solid var(--sari-dark-d8c2716225, #E7EAEE) !important;

            border-radius: 12px !important;

            background: var(--sari-dark-950baec030, #FAFBFC) !important;

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

            color: var(--sari-dark-e23e64dd1e, #344054) !important;

            font-size: 10px !important;

            font-weight: 700 !important;

        }

        .cp-toggle {

            display: inline-flex !important;

            flex: 0 0 auto !important;

            align-items: center !important;

            gap: 7px !important;

            color: var(--sari-dark-a63aa16d7c, #56606C) !important;

            font-size: 8px !important;

            font-weight: 700 !important;

            white-space: nowrap;

        }

        .cp-section .cp-grid {

            margin-top: 12px !important;

            padding-top: 12px !important;

            border-top: 1px solid var(--sari-dark-d8c2716225, #ECEFF2) !important;

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

            box-shadow: 0 4px 10px var(--sari-dark-71f44bdb16, rgba(213,150,23,.11)) !important;

        }

        .cp-btn-primary:hover,

        .cp-btn-primary:focus-visible {

            outline: none !important;

            border-color: var(--cp-gold-strong) !important;

            background: var(--cp-gold-strong) !important;

        }

        .cp-btn-secondary {

            border: 1px solid var(--sari-dark-d8c2716225, #DDE1E6) !important;

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

            color: var(--sari-dark-a63aa16d7c, #667085) !important;

        }

        .cp-btn-secondary:hover,

        .cp-btn-secondary:focus-visible {

            outline: none !important;

            border-color: var(--sari-dark-d8c2716225, #C9CFD6) !important;

            background: var(--sari-dark-950baec030, #F9FAFB) !important;

            color: var(--sari-dark-e23e64dd1e, #344054) !important;

        }

        .cp-btn-danger {

            border: 1px solid var(--sari-dark-d8c2716225, #E7CCCC) !important;

            background: var(--sari-dark-950baec030, #FFF8F8) !important;

            color: var(--sari-dark-34d4f616a6, #A55757) !important;

        }

        .cp-btn-danger:hover,

        .cp-btn-danger:focus-visible {

            outline: none !important;

            border-color: var(--sari-dark-d8c2716225, #DDB9B9) !important;

            background: var(--sari-dark-950baec030, #FFF1F1) !important;

            color: var(--sari-dark-a63aa16d7c, #934A4A) !important;

        }

        /* ---------------- CAMPAIGN SCHEDULE — CLEAN FLAT LIST ---------------- */

        .cp-panel-list .cp-panel-head {

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

        }

        .cp-list {

            display: block !important;

            padding: 0 !important;

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

        }

        .cp-item {

            position: relative !important;

            overflow: visible !important;

            border: 0 !important;

            border-bottom: 1px solid var(--sari-dark-d8c2716225, #ECEFF2) !important;

            border-radius: 0 !important;

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

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

            border-color: var(--sari-dark-d8c2716225, #ECEFF2) !important;

            background: var(--sari-dark-950baec030, #FAFBFC) !important;

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

            color: var(--sari-dark-e23e64dd1e, #172033) !important;

            font-size: 11.5px !important;

            font-weight: 750 !important;

            line-height: 1.35 !important;

            letter-spacing: -.01em !important;

        }

        .cp-meta {

            margin-top: 5px !important;

            color: var(--sari-dark-a63aa16d7c, #7C8592) !important;

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

            background: var(--sari-dark-4d21eb669b, #F4F6F8) !important;

            padding: 0 8px !important;

            color: var(--sari-dark-a63aa16d7c, #667085) !important;

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

            background: var(--sari-dark-4d21eb669b, #ECFDF3) !important;

            color: var(--sari-dark-9409b20329, #15803D) !important;

        }

        .cp-status.Scheduled {

            background: var(--sari-dark-4d21eb669b, #FFF7E6) !important;

            color: var(--sari-dark-af18afcaa4, #A56A00) !important;

        }

        .cp-status.Paused,

        .cp-status.Draft {

            background: var(--sari-dark-4d21eb669b, #F2F4F7) !important;

            color: var(--sari-dark-a63aa16d7c, #667085) !important;

        }

        .cp-benefits {

            display: flex !important;

            flex-wrap: wrap !important;

            align-items: center !important;

            gap: 0 !important;

            margin-top: 9px !important;

            color: var(--sari-dark-a63aa16d7c, #667085) !important;

        }

        .cp-chip {

            display: inline-flex !important;

            min-height: 0 !important;

            align-items: center !important;

            border: 0 !important;

            border-radius: 0 !important;

            background: transparent !important;

            padding: 0 !important;

            color: var(--sari-dark-a63aa16d7c, #667085) !important;

            font-size: 7.4px !important;

            font-weight: 600 !important;

            line-height: 1.45 !important;

        }

        .cp-chip + .cp-chip::before {

            content: "•";

            margin: 0 7px !important;

            color: var(--sari-dark-a63aa16d7c, #C0C6CE) !important;

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

            color: var(--sari-dark-e23c8a8a7f, #1677FF) !important;

        }

        .cp-action-edit:hover,

        .cp-action-edit:focus-visible {

            background: var(--sari-dark-4d21eb669b, #EAF3FF) !important;

            color: var(--sari-dark-858c62d1b5, #0068F0) !important;

        }

        .cp-action-pause {

            color: var(--sari-dark-e23e64dd1e, #111827) !important;

        }

        .cp-action-pause:hover,

        .cp-action-pause:focus-visible {

            background: var(--sari-dark-4d21eb669b, #F1F3F5) !important;

            color: var(--sari-dark-e23e64dd1e, #000000) !important;

        }

        .cp-action-publish {

            color: var(--sari-dark-e23e64dd1e, #111827) !important;

        }

        .cp-action-publish:hover,

        .cp-action-publish:focus-visible {

            background: var(--sari-dark-4d21eb669b, #F1F3F5) !important;

            color: var(--sari-dark-e23e64dd1e, #000000) !important;

        }

        .cp-action-delete {

            color: var(--sari-dark-65ed380fe4, #FF2D2D) !important;

        }

        .cp-action-delete:hover,

        .cp-action-delete:focus-visible {

            background: var(--sari-dark-950baec030, #FFF0F0) !important;

            color: var(--sari-dark-65ed380fe4, #E60000) !important;

        }

        .cp-empty {

            padding: 44px 18px !important;

            color: var(--sari-dark-a63aa16d7c, #7C8592) !important;

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

            border: 1px solid var(--sari-dark-d8c2716225, #DDE1E6) !important;

            border-radius: 10px !important;

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

            padding: 0 11px !important;

            color: var(--sari-dark-e23e64dd1e, #29313B) !important;

            font: 500 9.5px 'Poppins', sans-serif !important;

            text-align: left !important;

            cursor: pointer !important;

            box-shadow: 0 1px 2px var(--sari-dark-f6fe7319d9, rgba(15,23,42,.02)) !important;

        }

        .cp-select-trigger:hover {

            border-color: var(--sari-dark-d8c2716225, #C9CFD6) !important;

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

        }

        .cp-select-trigger:focus-visible,

        .cp-select-shell.is-open .cp-select-trigger {

            outline: none !important;

            border-color: var(--cp-gold) !important;

            box-shadow: 0 0 0 3px var(--sari-dark-b12a5e9552, rgba(213,150,23,.09)) !important;

        }

        .cp-select-trigger-label {

            min-width: 0 !important;

            overflow: hidden !important;

            color: var(--sari-dark-e23e64dd1e, #29313B) !important;

            text-overflow: ellipsis !important;

            white-space: nowrap !important;

        }

        .cp-select-chevron {

            width: 15px !important;

            height: 15px !important;

            flex: 0 0 15px !important;

            color: var(--sari-dark-a63aa16d7c, #667085) !important;

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

            border: 1px solid var(--sari-dark-d8c2716225, #E2E6EA) !important;

            border-radius: 11px !important;

            background: var(--sari-dark-950baec030, #FFFFFF) !important;

            opacity: 0 !important;

            visibility: hidden !important;

            pointer-events: none !important;

            transform: translateY(-4px) !important;

            box-shadow:

                0 12px 28px var(--sari-dark-8eadf403a5, rgba(15,23,42,.10)),

                0 3px 8px var(--sari-dark-1190f58155, rgba(15,23,42,.045)) !important;

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

            color: var(--sari-dark-e23e64dd1e, #344054) !important;

            font: 500 9px 'Poppins', sans-serif !important;

            text-align: left !important;

            cursor: pointer !important;

        }

        .cp-select-option:hover,

        .cp-select-option:focus-visible {

            outline: none !important;

            background: var(--sari-dark-950baec030, #F8FAFC) !important;

            color: var(--sari-dark-e23e64dd1e, #1D2939) !important;

        }

        .cp-select-option.is-selected {

            background: var(--sari-dark-950baec030, #F8FAFC) !important;

            color: var(--sari-dark-e23e64dd1e, #1D2939) !important;

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

    
        /* Reference parity: open header, white controls, floating main panels. */
        .campaign-page {
            --cp-gold: #c88912;
            --cp-gold-strong: #a96d08;
            --cp-ink: var(--sari-dark-e23e64dd1e, #29323b);
            --cp-text: var(--sari-dark-e23e64dd1e, #4c545e);
            --cp-muted: var(--sari-dark-a63aa16d7c, #707b86);
            --cp-shadow: 0 2px 5px var(--sari-dark-1190f58155, rgba(15,23,42,.045)),0 10px 24px var(--sari-dark-e07575a086, rgba(15,23,42,.075)),0 24px 54px var(--sari-dark-a2f72dee3d, rgba(15,23,42,.095)),inset 0 1px 0 var(--sari-dark-f30a5f9061, rgba(255,255,255,.92));
            max-width: 1640px !important;
            min-width: 0;
            padding: 2px 2px 32px !important;
            font-size: 12px;
            line-height: 1.5;
            container-type: inline-size;
        }
        .campaign-page :is(button,input,select,textarea) { font-family: inherit !important; }
        .campaign-page .cp-head { padding: 2px 0 16px !important; border-bottom: 0 !important; }
        .campaign-page .cp-eyebrow { margin: 0 !important; color: var(--sari-dark-685eb4fc40, #a87316) !important; font-size: 10.5px !important; font-weight: 800 !important; line-height: 1.2 !important; letter-spacing: .14em !important; }
        .campaign-page .cp-title { margin-top: 5px !important; font-size: clamp(29px,2.35vw,34px) !important; font-weight: 750 !important; line-height: 1.04 !important; }
        .campaign-page .cp-copy { max-width: 920px !important; margin-top: 7px !important; color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 11.5px !important; line-height: 1.6 !important; }
        .campaign-page .cp-layout { grid-template-columns: minmax(0,1.18fr) minmax(350px,.82fr) !important; gap: 18px !important; margin-top: 18px !important; }
        .campaign-page .cp-panel { min-width: 0; border: 1px solid var(--sari-dark-d8c2716225, #e5e7eb) !important; border-radius: 17px !important; box-shadow: var(--cp-shadow) !important; }
        .campaign-page .cp-panel:not(.cp-panel-list) { overflow: visible !important; }
        .campaign-page .cp-panel-head { min-height: 72px !important; padding: 16px 18px !important; border-radius: 17px 17px 0 0; }
        .campaign-page .cp-panel-head h2 { color: var(--sari-dark-e23e64dd1e, #29323b) !important; font-size: 14px !important; font-weight: 750 !important; }
        .campaign-page .cp-panel-head p { color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 10.5px !important; line-height: 1.55 !important; }
        .campaign-page .cp-body { padding: 18px !important; }
        .campaign-page .cp-grid { gap: 14px !important; }
        .campaign-page .cp-field { min-width: 0; }
        .campaign-page :is(.cp-field label,.cp-label) { color: var(--sari-dark-e23e64dd1e, #4b5560) !important; font-size: 11px !important; font-weight: 750 !important; line-height: 1.35 !important; }
        .campaign-page .cp-field :is(input,textarea,select:not(.cp-native-select)),
        .campaign-page .cp-select-trigger {
            min-width: 0;
            border: 1px solid var(--sari-dark-d8c2716225, #dfe3e8) !important;
            border-radius: 9px !important;
            background: var(--sari-dark-950baec030, #fff) !important;
            color: var(--sari-dark-e23e64dd1e, #333d47) !important;
            font: 550 12px 'Poppins',ui-sans-serif,system-ui,sans-serif !important;
            box-shadow: none !important;
        }
        .campaign-page .cp-field :is(input,select:not(.cp-native-select)),
        .campaign-page .cp-select-trigger { height: 42px !important; padding-inline: 11px !important; }
        .campaign-page .cp-field textarea { min-height: 104px !important; padding: 11px !important; line-height: 1.6 !important; }
        .campaign-page .cp-field :is(input,textarea)::placeholder { color: var(--sari-dark-a63aa16d7c, #79838e) !important; font-size: 11.5px; }
        .campaign-page .cp-field input[readonly] { background: var(--sari-dark-950baec030, #f7f8fa) !important; color: var(--sari-dark-a63aa16d7c, #707b86) !important; }
        .campaign-page .cp-help { color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 10.5px !important; line-height: 1.55 !important; }
        .campaign-page .cp-field .cp-multi { min-height: 144px !important; height: 144px !important; padding: 7px !important; }
        .campaign-page .cp-multi option { padding: 7px 9px; font-size: 11.5px; }
        .campaign-page .cp-field .cp-native-select { position: absolute !important; width: 1px !important; height: 1px !important; min-height: 0 !important; padding: 0 !important; margin: -1px !important; border: 0 !important; }
        .campaign-page .cp-select-trigger { font-weight: 650 !important; }
        .campaign-page .cp-select-trigger-label { color: var(--sari-dark-e23e64dd1e, #333d47) !important; }
        .campaign-page .cp-select-chevron { width: 14px !important; height: 14px !important; flex-basis: 14px !important; color: var(--sari-dark-a63aa16d7c, #596470) !important; }
        .campaign-page .cp-select-menu { max-height: 270px !important; border: 1px solid var(--sari-dark-d8c2716225, #e1e5e9) !important; border-radius: 12px !important; box-shadow: 0 18px 42px var(--sari-dark-3e32d50f5e, rgba(31,41,55,.13)),0 4px 12px var(--sari-dark-25b2da5e49, rgba(31,41,55,.04)) !important; }
        .campaign-page .cp-select-option { min-height: 39px !important; padding: 8px 10px !important; font-size: 11.5px !important; line-height: 1.4 !important; }
        .campaign-page .cp-select-option:hover { background: var(--sari-dark-950baec030, #f5f6f8) !important; color: var(--sari-dark-e23e64dd1e, #22282f) !important; }
        .campaign-page .cp-select-option.is-selected { background: var(--sari-dark-4d21eb669b, #f7f3eb) !important; color: var(--sari-dark-9b2ca2d764, #8e5d0e) !important; }
        .campaign-page .cp-immediate { padding: 13px !important; gap: 10px !important; }
        .campaign-page .cp-immediate strong { font-size: 11.5px !important; }
        .campaign-page .cp-immediate span span { font-size: 10.5px !important; }
        .campaign-page :is(.cp-immediate,.cp-toggle) input[type="checkbox"] { width: 17px !important; height: 17px !important; min-width: 17px !important; flex-shrink: 0; }
        .campaign-page .cp-section { padding: 14px !important; }
        .campaign-page .cp-section-head { gap: 12px !important; flex-wrap: wrap; }
        .campaign-page .cp-section-head strong { font-size: 12px !important; }
        .campaign-page .cp-toggle { font-size: 11px !important; }
        .campaign-page .cp-btn { min-height: 42px !important; height: 42px !important; font-size: 11px !important; font-weight: 650 !important; }
        .campaign-page .cp-btn-primary { box-shadow: 0 6px 16px var(--sari-dark-59ed29fe9d, rgba(201,132,8,.14)) !important; }
        .campaign-page .cp-actions { flex-wrap: wrap; }
        .campaign-page .cp-item { padding: 17px 18px !important; }
        .campaign-page .cp-item h3 { color: var(--sari-dark-e23e64dd1e, #29323b) !important; font-size: 13px !important; line-height: 1.45 !important; overflow-wrap: anywhere; }
        .campaign-page .cp-item-top { flex-wrap: wrap; gap: 10px !important; }
        .campaign-page .cp-meta { color: var(--sari-dark-a63aa16d7c, #707b86) !important; font-size: 11px !important; line-height: 1.65 !important; }
        .campaign-page .cp-status { min-height: 26px !important; padding: 3px 9px !important; font-size: 10.5px !important; }
        .campaign-page .cp-benefits { row-gap: 6px !important; }
        .campaign-page .cp-chip { font-size: 10.5px !important; }
        .campaign-page .cp-item-actions { flex-wrap: wrap !important; gap: 7px !important; margin-top: 12px !important; }
        .campaign-page .cp-action-icon { width: 34px !important; height: 34px !important; min-width: 34px !important; }
        .campaign-page .cp-action-icon svg { width: 16px !important; height: 16px !important; }
        .campaign-page .cp-action-icon:hover { transform: none !important; }
        .campaign-page :is(.cp-alert,.cp-empty) { font-size: 11.5px !important; line-height: 1.6 !important; }
        .campaign-page .cp-panel-list nav { padding-top: 12px; font-size: 11px !important; }
        .campaign-page .cp-panel-list nav p { font-size: 11px !important; color: var(--sari-dark-a63aa16d7c, #707b86); }
        .campaign-page .cp-panel-list nav a,
        .campaign-page .cp-panel-list nav [aria-disabled] > span,
        .campaign-page .cp-panel-list nav [aria-current] > span { min-height: 34px; font-size: 11px !important; }
        .campaign-page .cp-panel-list nav a { border-color: var(--sari-dark-d8c2716225, #e2e5e9); }
        .campaign-page .cp-panel-list nav [aria-current] > span { background: #c88912 !important; border-color: #c88912 !important; color: #fff !important; }
        .campaign-page .cp-panel-list nav svg { width: 16px !important; height: 16px !important; }
        .campaign-page :is(button,a,input,select,textarea):focus-visible { outline: 2px solid #9b6710 !important; outline-offset: 3px; }
        @container (max-width: 1100px) {
            .campaign-page .cp-layout { grid-template-columns: 1fr !important; }
        }
        @container (max-width: 600px) {
            .campaign-page .cp-grid { grid-template-columns: 1fr !important; }
            .campaign-page :is(.cp-span-2,.cp-section) { grid-column: 1 / -1 !important; }
            .campaign-page .cp-section-head { flex-direction: column !important; }
            .campaign-page .cp-actions { align-items: stretch !important; flex-direction: column-reverse !important; }
            .campaign-page .cp-btn { width: 100% !important; }
        }
        @media (max-width: 767px) {
            .campaign-page .cp-title { font-size: 29px !important; }
            .campaign-page .cp-eyebrow { font-size: 9.5px !important; }
            .campaign-page .cp-layout { gap: 14px !important; margin-top: 14px !important; }
            .campaign-page .cp-panel { border-radius: 14px !important; box-shadow: 0 2px 4px var(--sari-dark-7593018246, rgba(15,23,42,.035)),0 8px 18px var(--sari-dark-361b571631, rgba(15,23,42,.06)),0 18px 38px var(--sari-dark-7c935c8886, rgba(15,23,42,.07)) !important; }
            .campaign-page .cp-panel-head { border-radius: 14px 14px 0 0; }
            .campaign-page :is(.cp-body,.cp-panel-head,.cp-item) { padding: 14px !important; }
            .campaign-page .cp-field input:not([type="checkbox"]),
            .campaign-page .cp-field select:not(.cp-native-select):not([multiple]),
            .campaign-page :is(.cp-select-trigger,.cp-btn) { min-height: 44px !important; height: 44px !important; }
        }
        @media (prefers-reduced-motion: reduce) {
            .campaign-page * { transition: none !important; animation: none !important; scroll-behavior: auto !important; }
            .campaign-page .cp-action-icon:hover { transform: none !important; }
        }


/* Shipping budget: neutral surfaces, clear available balance, no icon tiles. */
.campaign-page .cp-shipping-budget{margin-top:24px;padding-top:22px;border-top:1px solid var(--sari-dark-d8c2716225, #E5E7EB);color:var(--sari-dark-e23e64dd1e, #344054)}
.campaign-page .cp-shipping-heading{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:18px}
.campaign-page .cp-shipping-eyebrow{margin:0 0 6px;font-size:10px;font-weight:700;letter-spacing:.13em;color:var(--sari-dark-685eb4fc40, #A87316)}
.campaign-page .cp-shipping-heading h2{margin:0;font-size:20px;font-weight:700;line-height:1.35;letter-spacing:-.025em;color:var(--sari-dark-e23e64dd1e, #293440)}
.campaign-page .cp-shipping-copy{margin:6px 0 0;font-size:12px;line-height:1.6;color:var(--sari-dark-a63aa16d7c, #7C8592)}
.campaign-page .cp-shipping-toggle{display:inline-flex;align-items:center;gap:8px;flex:0 0 auto;margin-top:4px;font-size:12px;font-weight:600;cursor:pointer;color:var(--sari-dark-e23e64dd1e, #475467)}
.campaign-page .cp-shipping-toggle input[type=checkbox]{width:18px;height:18px;accent-color:#C88912}
.campaign-page .cp-shipping-balance{display:grid;grid-template-columns:minmax(0,1fr);gap:20px;padding:20px;border:1px solid var(--sari-dark-d8c2716225, #E5E7EB);border-radius:14px;background:var(--sari-dark-950baec030, #FFF);box-shadow:0 3px 10px var(--sari-dark-7593018246, rgba(15,23,42,.035))}
.campaign-page .cp-shipping-available-label{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:var(--sari-dark-a63aa16d7c, #64748B)}
.campaign-page .cp-shipping-available-label svg{width:21px;height:21px;flex-shrink:0;color:var(--sari-dark-a63aa16d7c, #64748B)}
.campaign-page .cp-shipping-available>strong{display:block;margin-top:10px;font-size:30px;font-weight:700;letter-spacing:-.035em;line-height:1.2;color:var(--sari-dark-e23e64dd1e, #1F2937);overflow-wrap:anywhere}
.campaign-page .cp-shipping-available>p{margin:6px 0 0;font-size:11.5px;color:var(--sari-dark-a63aa16d7c, #8591A0)}
.campaign-page .cp-shipping-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin:0;padding-top:16px;border-top:1px solid var(--sari-dark-d8c2716225, #EDF0F3)}
.campaign-page .cp-shipping-stats>div{min-width:0}
.campaign-page .cp-shipping-stats dt{font-size:11px;color:var(--sari-dark-a63aa16d7c, #8591A0)}
.campaign-page .cp-shipping-stats dd{margin:5px 0 0;font-size:15px;font-weight:600;color:var(--sari-dark-e23e64dd1e, #344054);overflow-wrap:anywhere}
.campaign-page .cp-shipping-ledger-note{margin:10px 0 0;font-size:11px;line-height:1.6;color:var(--sari-dark-a63aa16d7c, #8591A0)}
.campaign-page .cp-shipping-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px 16px;margin-top:22px}
.campaign-page .cp-shipping-fields[hidden]{display:none!important}
.campaign-page .cp-shipping-funding{display:flex;align-items:center;gap:10px;grid-column:1/-1;padding-bottom:14px;border-bottom:1px solid var(--sari-dark-d8c2716225, #EDF0F3);font-size:12px;color:var(--sari-dark-a63aa16d7c, #8591A0)}
.campaign-page .cp-shipping-funding strong{font-size:12px;font-weight:600;color:var(--sari-dark-e23e64dd1e, #475467)}
.campaign-page .cp-shipping-fields .cp-field label{display:block;margin-bottom:8px;font-size:12px!important;font-weight:600!important}
.campaign-page .cp-shipping-fields .cp-field input{width:100%;height:46px!important;font-size:13px!important;border-radius:10px!important;box-shadow:none!important}
.campaign-page .cp-shipping-help{margin:7px 0 0;font-size:11px;line-height:1.6;color:var(--sari-dark-a63aa16d7c, #8591A0)}
.campaign-page .cp-shipping-preview{margin:8px 0 0;font-size:11.5px;line-height:1.6;color:var(--sari-dark-a63aa16d7c, #64748B)}
.campaign-page .cp-shipping-preview strong{font-weight:600;color:var(--sari-dark-e23e64dd1e, #344054)}
.campaign-page .cp-shipping-preview.is-invalid,.campaign-page .cp-shipping-preview.is-invalid strong{color:var(--sari-dark-5300031fd1, #B91C1C)}
.campaign-page .cp-shipping-policy{display:flex;align-items:flex-start;gap:10px;grid-column:1/-1;padding-top:16px;border-top:1px solid var(--sari-dark-d8c2716225, #EDF0F3);color:var(--sari-dark-a63aa16d7c, #8591A0)}
.campaign-page .cp-shipping-policy svg{width:20px;height:20px;flex:0 0 20px;margin-top:1px;color:var(--sari-dark-a63aa16d7c, #7C8592)}
.campaign-page .cp-shipping-policy p{margin:0;font-size:11px;line-height:1.7}
@media(max-width:600px){.campaign-page .cp-shipping-heading{flex-direction:column;gap:12px}.campaign-page .cp-shipping-heading h2{font-size:18px}.campaign-page .cp-shipping-fields{grid-template-columns:minmax(0,1fr)}.campaign-page .cp-shipping-balance{padding:16px}.campaign-page .cp-shipping-stats{gap:8px}.campaign-page .cp-shipping-stats dd{font-size:13px}.campaign-page .cp-shipping-available>strong{font-size:28px}}

</style>

    <div class="cp-head">

        <p class="cp-eyebrow">Marketing & Buyer Promotions</p>

        <h1 class="cp-title">Events & <span>Promotions</span></h1>

        

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

            <div class="cp-panel-head"><h2>{{ $formCampaign ? 'Edit campaign' : 'Create campaign' }}</h2></div>

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

                        <div class="cp-field cp-span-2" data-target-panel="sellers" @if($scope!=='sellers') hidden @endif><label>Eligible Sellers</label><select class="cp-multi" name="seller_ids[]" multiple>@foreach($sellers as $seller)<option value="{{ $seller->id }}" @selected(in_array((int)$seller->id,$selectedSellerIds,true))>#{{ $seller->id }} · {{ $seller->store_name ?: $seller->email }}</option>@endforeach</select><p class="cp-help">Hold Ctrl/Command to select multiple Sellers.</p></div>

                        <div class="cp-field cp-span-2" data-target-panel="products" @if($scope!=='products') hidden @endif><label>Eligible Products</label><select class="cp-multi" name="product_ids[]" multiple>@foreach($products as $product)<option value="{{ $product->id }}" @selected(in_array((int)$product->id,$selectedProductIds,true))>#{{ $product->id }} · {{ $product->name }} · {{ $product->category ?: 'General' }}</option>@endforeach</select><p class="cp-help">Only the selected product IDs receive the Admin event price.</p></div>

                        <div class="cp-field cp-span-2" data-target-panel="categories" @if($scope!=='categories') hidden @endif><label>Eligible Categories</label><select class="cp-multi" name="categories[]" multiple>@foreach($categories as $category)<option value="{{ $category }}" @selected(in_array((string)$category,$selectedCategories,true))>{{ $category }}</option>@endforeach</select></div>

                        <div class="cp-section">

                            <div class="cp-section-head"><div><strong>Automatic Buyer sale</strong></div><label class="cp-toggle"><input type="hidden" name="sale_enabled" value="0"><input type="checkbox" name="sale_enabled" value="1" data-benefit-toggle="sale" @checked($saleEnabled)> Enable sale</label></div>

                            <div class="cp-grid" data-benefit-panel="sale" @if(!$saleEnabled) hidden @endif style="margin-top:11px">

                                <div class="cp-field"><label>Discount type</label><select name="sale_discount_type"><option value="percentage" @selected(old('sale_discount_type',$formCampaign?->sale_discount_type)==='percentage')>Percentage</option><option value="fixed" @selected(old('sale_discount_type',$formCampaign?->sale_discount_type)==='fixed')>Fixed ₱ per unit</option></select></div>

                                <div class="cp-field"><label>Discount value</label><input name="sale_discount_value" type="number" min="0" step="0.01" value="{{ old('sale_discount_value',$formCampaign?->sale_discount_value ?? 10) }}"></div>

                                <div class="cp-field"><label>Maximum discount / unit</label><input name="sale_max_discount" type="number" min="0" step="0.01" value="{{ old('sale_max_discount',$formCampaign?->sale_max_discount) }}" placeholder="Optional"></div>

                            </div>

                        </div>

                        <div class="cp-section">

                            <div class="cp-section-head"><div><strong>SARI event voucher</strong></div><label class="cp-toggle"><input type="hidden" name="voucher_enabled" value="0"><input type="checkbox" name="voucher_enabled" value="1" data-benefit-toggle="voucher" @checked($voucherEnabled)> Enable voucher</label></div>

                            <div class="cp-grid" data-benefit-panel="voucher" @if(!$voucherEnabled) hidden @endif style="margin-top:11px">

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

                    <section class="cp-shipping-budget" aria-labelledby="shippingBudgetTitle" data-shipping-committed="{{ $shippingCommittedCents }}">
                        <div class="cp-shipping-heading">
                            <div><p class="cp-shipping-eyebrow">SHIPPING PROMOTIONS</p><h2 id="shippingBudgetTitle">Shipping Voucher &amp; Budget</h2><p class="cp-shipping-copy">Set how much SARI can cover for buyer shipping.</p></div>
                            <label class="cp-shipping-toggle" for="shippingEnabled"><input type="hidden" name="shipping_enabled" value="0"><input id="shippingEnabled" type="checkbox" name="shipping_enabled" value="1" data-benefit-toggle="shipping" aria-controls="shippingBenefitPanel" @checked($shippingEnabled)> Enable voucher</label>
                        </div>

                        <div class="cp-shipping-balance" aria-label="Saved campaign shipping budget">
                            <div class="cp-shipping-available">
                                <div class="cp-shipping-available-label"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M4 6h15v14H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h13v2"/><path d="M15 10h7v6h-7z"/><path d="M18 13h1"/></svg><span>Available for shipping</span></div>
                                <strong>₱{{ number_format($shippingBudgetTotals['available'] / 100, 2) }}</strong>
                                <p>{{ $formCampaign ? 'Remaining saved campaign budget' : 'No shipping budget allocated yet' }}</p>
                            </div>
                            <dl class="cp-shipping-stats">
                                @foreach(['allocated'=>'Budget limit','reserved'=>'Reserved','used'=>'Used'] as $budgetKey=>$budgetLabel)
                                    <div><dt>{{ $budgetLabel }}</dt><dd>₱{{ number_format($shippingBudgetTotals[$budgetKey] / 100, 2) }}</dd></div>
                                @endforeach
                            </dl>
                        </div>
                        <p class="cp-shipping-ledger-note">Available = budget limit − reserved − used. Campaign allocation only; this is not the Treasury cash balance.</p>

                        <div id="shippingBenefitPanel" class="cp-shipping-fields" data-benefit-panel="shipping" @if(!$shippingEnabled) hidden @endif>
                            <div class="cp-shipping-funding"><span>Funded by</span><strong>SARI marketing budget</strong></div>
                            <div class="cp-field"><label for="shippingBudget">Campaign shipping limit (₱)</label><input id="shippingBudget" name="shipping_budget" type="number" inputmode="decimal" min="{{ number_format($shippingCommittedCents / 100, 2, '.', '') }}" max="100000000" step="0.01" value="{{ old('shipping_budget', number_format($shippingBudgetTotals['allocated'] / 100, 2, '.', '')) }}" aria-describedby="shippingBudgetHelp shippingBudgetPreview"><p id="shippingBudgetHelp" class="cp-shipping-help">Maximum total shipping subsidy allowed for this campaign.</p><p id="shippingBudgetPreview" class="cp-shipping-preview" role="status" aria-live="polite">Available after saving: <strong data-shipping-proposed>₱{{ number_format(max(0, (float) old('shipping_budget', $shippingBudgetTotals['allocated'] / 100) - $shippingCommittedCents / 100), 2) }}</strong></p></div>
                            <div class="cp-field"><label for="shippingCap">Maximum subsidy per seller order (₱)</label><input id="shippingCap" name="shipping_cap" type="number" inputmode="decimal" min="0" max="1000000" step="0.01" value="{{ old('shipping_cap', number_format(($formCampaign?->shipping_cap_cents ?? 0) / 100, 2, '.', '')) }}"><p class="cp-shipping-help">Capped at the remaining shipping fee and campaign budget.</p></div>
                            <div class="cp-field"><label for="shippingCode">Shipping voucher code</label><input id="shippingCode" name="shipping_code" maxlength="64" pattern="[A-Za-z0-9_-]+" value="{{ old('shipping_code', $formCampaign?->shipping_code) }}" placeholder="SARISHIP50" autocapitalize="characters" spellcheck="false"></div>
                            <div class="cp-field"><label for="shippingMinimum">Minimum eligible spend per seller (₱)</label><input id="shippingMinimum" name="shipping_minimum" type="number" inputmode="decimal" min="0" max="100000000" step="0.01" value="{{ old('shipping_minimum', number_format(($formCampaign?->shipping_minimum_cents ?? 0) / 100, 2, '.', '')) }}"></div>
                            <div class="cp-field"><label for="shippingBuyerLimit">Checkouts per buyer</label><input id="shippingBuyerLimit" name="shipping_per_buyer_limit" type="number" inputmode="numeric" min="1" max="1000" step="1" value="{{ old('shipping_per_buyer_limit', $formCampaign?->shipping_per_buyer_limit ?? 1) }}"></div>
                            <div class="cp-field"><label for="shippingAreas">Eligible delivery areas</label><input id="shippingAreas" name="shipping_areas_text" maxlength="2000" value="{{ old('shipping_areas_text', implode(', ', $formCampaign?->shipping_areas ?? [])) }}" placeholder="All areas, or enter Laguna, Manila"><p class="cp-shipping-help">Leave blank for all areas. Otherwise enter complete city/province address parts separated by commas.</p></div>
                            <div class="cp-shipping-policy"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/></svg><p>Checkout reserves budget; delivery marks it used. Cancellation before delivery releases it. The target scope above controls eligible sellers and products; every seller order in the checkout must qualify.</p></div>
                        </div>
                    </section>

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

                            @if($campaign->shipping_enabled || $campaign->shipping_redemptions_count > 0)
                                <p class="cp-meta">Shipping: {{ $campaign->shipping_code }} · SARI-funded<br>
                                    Allocated ₱{{ number_format($campaign->shipping_budget_cents / 100, 2) }} · Reserved ₱{{ number_format(($campaign->shipping_reserved_cents ?? 0) / 100, 2) }}<br>
                                    Used ₱{{ number_format(($campaign->shipping_used_cents ?? 0) / 100, 2) }} · Available ₱{{ number_format(max(0, $campaign->shipping_budget_cents - ($campaign->shipping_reserved_cents ?? 0) - ($campaign->shipping_used_cents ?? 0)) / 100, 2) }}
                                </p>
                            @endif

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

                                @if($campaign->redemptions_count===0 && $campaign->shipping_redemptions_count===0)

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

        const shippingToggle=form.querySelector('[data-benefit-toggle="shipping"]');
        const shippingBudget=form.querySelector('#shippingBudget');
        const shippingCap=form.querySelector('#shippingCap');
        const committed=Number(form.querySelector('[data-shipping-committed]')?.dataset.shippingCommitted||0);
        const shippingPreview=form.querySelector('#shippingBudgetPreview');
        const money=new Intl.NumberFormat('en-PH',{style:'currency',currency:'PHP'});
        const syncShippingValidation=()=>{
            const enabled=Boolean(shippingToggle?.checked);
            ['shippingCode','shippingBudget','shippingCap'].forEach(id=>{
                const input=form.querySelector('#'+id);if(!input)return;
                input.required=enabled;
                if(input.type==='number')input.min=id==='shippingBudget'?String(Math.max(enabled?0.01:0,committed/100)):(enabled?'0.01':'0');
            });
            const amount=Number(shippingBudget?.value||0);
            const amountCents=Math.round(amount*100);
            const valid=Number.isFinite(amount)&&amountCents>=committed;
            shippingBudget?.setCustomValidity(valid?'':'The budget cannot be below reserved plus used subsidies.');
            if(shippingCap)shippingCap.max=String(Math.min(1000000,Math.max(0,amount)));
            if(shippingPreview){
                shippingPreview.classList.toggle('is-invalid',!valid);
                shippingPreview.replaceChildren(document.createTextNode(valid?'Available after saving: ':'Minimum committed budget: '));
                const value=document.createElement('strong');
                value.textContent=money.format(valid?Math.max(0,amountCents-committed)/100:committed/100);
                shippingPreview.append(value);
            }
        };
        shippingToggle?.addEventListener('change',syncShippingValidation);
        shippingBudget?.addEventListener('input',syncShippingValidation);
        syncShippingValidation();

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
