
@extends('layouts.logistics')

@section('title', 'Municipal Hubs | SARI Logistics')
@section('page-title', 'Municipal Hubs')

@php
    $editingHasOperator = (bool) $editingOperator;
    $operatorRequired = ! $editing || ! $editingHasOperator;

    $fieldClass = 'w-full h-[42px] rounded-[10px] border border-[#e5ddd1] bg-white px-3 text-[11px] text-[#403a33] outline-none transition placeholder:text-[#aaa198] focus:border-[#d3ad63] focus:ring-4 focus:ring-[#d3ad63]/10';

    $labelClass = 'mb-1.5 block text-[10px] font-semibold text-[#5f574e]';
@endphp

@push('styles')

<link
    rel="stylesheet"
    href="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.css"
>

<style>
    [hidden] {
        display: none !important;
    }

    /*
    |--------------------------------------------------------------------------
    | SELLER-STYLE MUNICIPAL HUB WORKSPACE
    |--------------------------------------------------------------------------
    */

    #logisticsFacilitiesWorkspace {
        --fh-gold: #d9950b;
        --fh-gold-dark: #c88708;
        --fh-border: #e8e2d9;
        --fh-muted: #887f75;
        --fh-shadow: 0 6px 18px rgba(33,24,14,.035);

        max-width: 1620px;
        font-family: Poppins, ui-sans-serif, sans-serif;
        color: #24211d;
    }

    /* Page heading */

    #logisticsFacilitiesWorkspace .fh-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;

        padding: 5px 2px 17px;
        border-bottom: 1px solid #e4e1dc;
    }

    #logisticsFacilitiesWorkspace .fh-eyebrow {
        color: #a76f11;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    #logisticsFacilitiesWorkspace .fh-title {
        margin-top: 5px;
        color: #24201b;
        font-size: clamp(23px,2.15vw,30px);
        font-weight: 750;
        letter-spacing: -.045em;
        line-height: 1.2;
    }

    #logisticsFacilitiesWorkspace .fh-title span {
        color: var(--fh-gold);
    }

    #logisticsFacilitiesWorkspace .fh-intro {
        margin-top: 6px;
        color: #897f75;
        font-size: 10px;
        line-height: 1.7;
    }

    #logisticsFacilitiesWorkspace .fh-heading-actions {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    #logisticsFacilitiesWorkspace .fh-network-state {
        padding: 9px 13px;
        border: 1px solid #d5e8da;
        border-radius: 11px;
        background: #f5fbf7;
        color: #387b54;
    }

    #logisticsFacilitiesWorkspace .fh-network-state.is-warning {
        border-color: #ead9b4;
        background: #fffaf0;
        color: #996a15;
    }

    #logisticsFacilitiesWorkspace .fh-network-state small {
        display: block;
        color: #857e75;
        font-size: 8px;
        font-weight: 650;
        letter-spacing: .09em;
        text-transform: uppercase;
    }

    #logisticsFacilitiesWorkspace .fh-network-state strong {
        display: block;
        margin-top: 3px;
        font-size: 10px;
        font-weight: 650;
    }

    /* Buttons */

    #logisticsFacilitiesWorkspace .fh-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 39px;
        padding: 0 17px;
        border: 1px solid var(--fh-gold);
        border-radius: 10px;
        background: var(--fh-gold);
        color: white;
        font-size: 10px;
        font-weight: 650;
        box-shadow: 0 5px 12px rgba(194,132,10,.12);

        transition:
            background .15s ease,
            border-color .15s ease,
            box-shadow .15s ease;
    }

    #logisticsFacilitiesWorkspace .fh-primary:hover {
        background: var(--fh-gold-dark);
        border-color: var(--fh-gold-dark);
        box-shadow: 0 7px 16px rgba(194,132,10,.16);
    }

    /* KPI cards */

    #logisticsFacilitiesWorkspace .fh-kpis {
        display: grid;
        grid-template-columns: repeat(4,minmax(0,1fr));
        gap: 12px;
    }

    #logisticsFacilitiesWorkspace .fh-kpi {
        display: flex;
        justify-content: space-between;
        gap: 9px;
        min-width: 0;
        min-height: 94px;
        padding: 13px;
        border: 1px solid var(--fh-border);
        border-radius: 16px;
        background: #fff;
        box-shadow: var(--fh-shadow);

        transition:
            transform .18s cubic-bezier(.22,1,.36,1),
            border-color .18s ease,
            box-shadow .18s ease;
    }

    #logisticsFacilitiesWorkspace .fh-kpi-label {
        color: #736a60;
        font-size: 10px;
        font-weight: 600;
    }

    #logisticsFacilitiesWorkspace .fh-kpi-value {
        margin-top: 7px;
        color: #211d18;
        font-size: 24px;
        font-weight: 750;
        letter-spacing: -.04em;
        line-height: 1.2;
    }

    #logisticsFacilitiesWorkspace .fh-kpi-sub {
        margin-top: 5px;
        color: #8a8177;
        font-size: 9px;
        line-height: 1.4;
    }

    #logisticsFacilitiesWorkspace .fh-kpi-icon {
        display: grid;
        place-items: center;
        flex: 0 0 39px;
        width: 39px;
        height: 39px;
        border-radius: 11px;
        background: #fff8ee;
        color: #bd831c;
    }

    #logisticsFacilitiesWorkspace
    .fh-kpi:nth-child(2) .fh-kpi-icon {
        background: #eff8f2;
        color: #45845f;
    }

    #logisticsFacilitiesWorkspace
    .fh-kpi:nth-child(3) .fh-kpi-icon {
        background: #f0f6ff;
        color: #597fac;
    }

    #logisticsFacilitiesWorkspace .fh-kpi-icon svg {
        width: 17px;
        height: 17px;
    }

    /* Management panels */

    #logisticsFacilitiesWorkspace .fh-panel,
    #logisticsFacilitiesWorkspace #hubEditor {
        overflow: hidden;
        border: 1px solid var(--fh-border) !important;
        border-radius: 17px !important;
        background: #fff !important;
        box-shadow: var(--fh-shadow) !important;
    }

    #logisticsFacilitiesWorkspace .fh-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 14px 16px;
        border-bottom: 1px solid #eee8df;
    }

    #logisticsFacilitiesWorkspace .fh-panel-title {
        color: #28231d;
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.035em;
    }

    #logisticsFacilitiesWorkspace .fh-panel-desc {
        margin-top: 4px;
        color: #948a80;
        font-size: 9px;
        line-height: 1.5;
    }

    #logisticsFacilitiesWorkspace .fh-panel-tools {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    #logisticsFacilitiesWorkspace .fh-search {
        height: 38px;
        width: 225px;
        max-width: 100%;
        padding: 0 12px;
        border: 1px solid #dae0e7;
        border-radius: 10px;
        background: white;
        font-size: 10px;
        outline: none;

        transition:
            border-color .15s ease,
            box-shadow .15s ease;
    }

    #logisticsFacilitiesWorkspace .fh-search:focus {
        border-color: var(--fh-gold);
        box-shadow: 0 0 0 3px rgba(217,149,11,.09);
    }

    #logisticsFacilitiesWorkspace .fh-counter {
        display: inline-flex;
        align-items: center;
        min-height: 35px;
        padding: 0 11px;
        border: 1px solid #ecdec1;
        border-radius: 9px;
        background: #fffaf0;
        color: #9f6c13;
        font-size: 9px;
        font-weight: 700;
    }

    /* Municipal hub cards */

    #logisticsFacilitiesWorkspace .fh-hubs {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        align-items: stretch;
        gap: 12px;
        padding: 14px;
    }

    #logisticsFacilitiesWorkspace .fh-hub {
        display: flex;
        flex-direction: column;
        min-width: 0;
        border: 1px solid #e8e2da;
        border-radius: 13px;
        background: white;
        box-shadow: 0 4px 13px rgba(35,27,18,.02);

        transition:
            transform .18s cubic-bezier(.22,1,.36,1),
            border-color .18s ease,
            box-shadow .18s ease;
    }

    #logisticsFacilitiesWorkspace .fh-hub-body {
        display: flex;
        gap: 12px;
        min-width: 0;
        padding: 15px 15px 12px;
    }

    #logisticsFacilitiesWorkspace .fh-hub-symbol {
        display: grid;
        place-items: center;
        flex: 0 0 38px;
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: #eff8f2;
        color: #44815d;
    }

    #logisticsFacilitiesWorkspace .fh-hub-symbol.is-inactive {
        background: #f4f2ef;
        color: #958d82;
    }

    #logisticsFacilitiesWorkspace .fh-hub-symbol svg {
        width: 18px;
        height: 18px;
    }

    #logisticsFacilitiesWorkspace .fh-hub-name {
        color: #322b23;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.45;
    }

    #logisticsFacilitiesWorkspace .fh-status {
        display: inline-flex;
        padding: 4px 8px;
        border-radius: 999px;
        background: #edf8f1;
        color: #367b52;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .02em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    #logisticsFacilitiesWorkspace .fh-status.is-inactive {
        background: #f2f0ed;
        color: #847d74;
    }

    #logisticsFacilitiesWorkspace .fh-hub-address {
        margin-top: 7px;
        color: #655c52;
        font-size: 10px;
        line-height: 1.5;
    }

    #logisticsFacilitiesWorkspace .fh-hub-meta {
        margin-top: 5px;
        color: #93887c;
        font-size: 9px;
        line-height: 1.55;
    }

    /* Operator card */

    #logisticsFacilitiesWorkspace .fh-operator {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin: 0 15px 13px;
        padding: 11px 12px;
        border: 1px solid #eee8e0;
        border-radius: 10px;
        background: #fbfbfc;
    }

    #logisticsFacilitiesWorkspace .fh-operator small {
        display: block;
        color: #9a8e7f;
        font-size: 8px;
        font-weight: 650;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    #logisticsFacilitiesWorkspace .fh-operator strong {
        display: block;
        margin-top: 5px;
        color: #4b4339;
        font-size: 10px;
        font-weight: 650;
    }

    #logisticsFacilitiesWorkspace .fh-operator p {
        margin-top: 3px;
        color: #8e8478;
        font-size: 9px;
        overflow-wrap: anywhere;
    }

    /* Consistent action placement */

    #logisticsFacilitiesWorkspace .fh-hub-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: auto;
        padding: 11px 15px;
        border-top: 1px solid #f0ebe5;
    }

    #logisticsFacilitiesWorkspace .fh-code {
        color: #8e8274;
        font-family: ui-monospace, monospace;
        font-size: 9px;
        font-weight: 650;
    }

    #logisticsFacilitiesWorkspace .fh-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 6px;
    }

    #logisticsFacilitiesWorkspace .fh-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 31px;
        padding: 0 11px;
        border: 1px solid #e2d8c9;
        border-radius: 8px;
        background: white;
        color: #65594c;
        font-size: 9px;
        font-weight: 600;

        transition:
            background .15s ease,
            border-color .15s ease,
            color .15s ease;
    }

    #logisticsFacilitiesWorkspace .fh-action:hover {
        background: #fffaf1;
        border-color: #d9bf93;
        color: #94630b;
    }

    #logisticsFacilitiesWorkspace .fh-action.is-warning {
        border-color: #e9d8b5;
        background: #fff9ee;
        color: #986a19;
    }

    #logisticsFacilitiesWorkspace .fh-action.is-danger {
        border-color: #e8cccc;
        background: #fff7f7;
        color: #ac5555;
    }

    #logisticsFacilitiesWorkspace .fh-action.is-danger:hover {
        background: #ffefef;
        border-color: #deaaaa;
    }

    #logisticsFacilitiesWorkspace .fh-empty {
        grid-column: 1 / -1;
        padding: 38px 15px;
        border: 1px dashed #e5dbcd;
        border-radius: 12px;
        color: #8f8374;
        font-size: 10px;
        text-align: center;
    }

    /* Hub editor */

    #logisticsFacilitiesWorkspace #hubEditor > div:first-child {
        padding: 15px 17px !important;
        border-color: #eee8df !important;
    }

    #logisticsFacilitiesWorkspace #hubEditor > div:first-child h3 {
        font-size: 16px !important;
        letter-spacing: -.025em;
    }

    #logisticsFacilitiesWorkspace #sariFacilityForm {
        padding: 17px !important;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm > div.grid {
        gap: 20px !important;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm > div.grid > div:last-child > section {
        background: #fbfbfc !important;
        border-color: #e9e3dc !important;
        border-radius: 13px !important;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm input:not([type="hidden"]):not([type="checkbox"]),

    #logisticsFacilitiesWorkspace #sariFacilityForm select,

    #logisticsFacilitiesWorkspace #sariFacilityForm textarea {
        font-family: inherit;
        font-size: 10px !important;
        background: #fff;
        border-color: #dde1e6;
        border-radius: 10px !important;

        transition:
            border-color .15s ease,
            box-shadow .15s ease;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm input:not([type="hidden"]):not([type="checkbox"]):focus,

    #logisticsFacilitiesWorkspace #sariFacilityForm select:focus,

    #logisticsFacilitiesWorkspace #sariFacilityForm textarea:focus {
        outline: none;
        border-color: #d9950b !important;
        box-shadow: 0 0 0 3px rgba(217,149,11,.09) !important;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm .hub-input-valid {
        border-color: #68aa80 !important;
        background: #f8fdf9 !important;
    }

    #logisticsFacilitiesWorkspace
    #sariFacilityForm .hub-input-invalid {
        border-color: #dc7777 !important;
        background: #fff9f9 !important;
    }

    #logisticsFacilitiesWorkspace #hubSubmitButton {
        background: #d9950b;
        box-shadow: 0 5px 12px rgba(194,132,10,.13);

        transition:
            background .15s ease,
            box-shadow .15s ease;
    }

    #logisticsFacilitiesWorkspace #hubSubmitButton:hover {
        background: #c88708;
        box-shadow: 0 7px 16px rgba(194,132,10,.16);
    }

    /* Address directory */

    .hub-address-wrap {
        position: relative;
    }

    .hub-address-results {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        right: 0;
        z-index: 50;
        max-height: 215px;
        overflow-y: auto;
        padding: 5px;
        border: 1px solid #e6dccc;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 12px 28px rgba(42,31,18,.12);
    }

    .hub-address-results button {
        display: block;
        width: 100%;
        padding: 10px 11px;
        border-radius: 7px;
        color: #51483e;
        font-size: 11px;
        text-align: left;
    }

    .hub-address-results button:hover,
    .hub-address-results button:focus {
        outline: none;
        background: #fff5df;
        color: #99650b;
    }

    /* Map */

    #sariFacilityMap {
        width: 100%;
        height: 340px;
        overflow: hidden;
        border: 1px solid #e5e2dd;
        border-radius: 12px;
        background: #eef1ed;
    }

    #sariFacilityMap .maplibregl-canvas {
        outline: none;
    }

    #sariFacilityMap .maplibregl-ctrl-group {
        overflow: hidden;
        border: 1px solid #e7ddcf;
        border-radius: 9px;
        box-shadow: 0 7px 20px rgba(64,50,34,.08);
    }

    .sari-facility-pin {
        display: grid;
        place-items: center;
        width: 29px;
        height: 29px;
        border: 3px solid white;
        border-radius: 50%;
        background: #d9930a;
        color: white;
        font-size: 9px;
        font-weight: 800;
        box-shadow: 0 7px 18px rgba(67,49,22,.24);
    }

    /* Password fields */

    .hub-password-wrap {
        position: relative;
        margin-top: 6px;
    }

    .hub-password-wrap input {
        padding-right: 46px !important;
    }

    .hub-password-eye {
        position: absolute;
        top: 50%;
        right: 6px;
        transform: translateY(-50%);
        display: grid;
        place-items: center;
        width: 35px;
        height: 35px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #82776a;
        cursor: pointer;
    }

    .hub-password-eye:hover {
        background: #fff3dc;
        color: #b97805;
    }

    .hub-password-eye svg {
        width: 17px;
        height: 17px;
        pointer-events: none;
    }

    .hub-password-rules {
        display: grid;
        grid-template-columns: repeat(2,minmax(0,1fr));
        gap: 9px;
        margin-top: 12px;
        padding: 13px;
        border: 1px solid #eadfcf;
        border-radius: 11px;
        background: white;
    }

    .hub-password-rule {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #93897d;
        font-size: 10px;
    }

    .hub-password-rule::before {
        content: "○";
        font-size: 13px;
    }

    .hub-password-rule.valid {
        color: #328354;
    }

    .hub-password-rule.valid::before {
        content: "✓";
        font-weight: 800;
    }

    .hub-password-rule.invalid {
        color: #c04f4f;
    }

    .hub-password-rule.invalid::before {
        content: "✕";
        font-weight: 800;
    }

    .hub-feedback {
        display: block;
        margin-top: 7px;
        color: #93897d;
        font-size: 10px;
        line-height: 16px;
    }

    .hub-feedback.valid {
        color: #328354;
        font-weight: 600;
    }

    .hub-feedback.invalid {
        color: #c04f4f;
        font-weight: 600;
    }

    .hub-input-valid {
        border-color: #68aa80 !important;
        background: #f8fdf9 !important;
    }

    .hub-input-invalid {
        border-color: #dc7777 !important;
        background: #fff9f9 !important;
    }

    /* Hover */

    @media (hover:hover) and (pointer:fine) {
        #logisticsFacilitiesWorkspace .fh-kpi:hover,
        #logisticsFacilitiesWorkspace .fh-hub:hover {
            transform: translateY(-2px);
            border-color: #d8c9b2;
            box-shadow: 0 13px 28px rgba(43,33,21,.055);
        }
    }

    /* Responsive */

    @media (max-width:1199px) {
        #logisticsFacilitiesWorkspace .fh-kpis {
            grid-template-columns: repeat(2,minmax(0,1fr));
        }
    }

    @media (max-width:767px) {
        #logisticsFacilitiesWorkspace .fh-hubs {
            grid-template-columns: 1fr;
        }

        #logisticsFacilitiesWorkspace .fh-heading {
            align-items: flex-start;
        }

        #logisticsFacilitiesWorkspace .fh-panel-tools {
            width: 100%;
        }

        #logisticsFacilitiesWorkspace .fh-panel-tools input {
            flex: 1;
            min-width: 0;
        }
    }

    @media (max-width:640px) {
        #sariFacilityMap {
            height: 270px;
        }

        .hub-password-rules {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width:480px) {
        #logisticsFacilitiesWorkspace .fh-heading-actions {
            width: 100%;
        }

        #logisticsFacilitiesWorkspace .fh-kpis {
            gap: 9px;
        }

        #logisticsFacilitiesWorkspace .fh-kpi {
            min-height: 91px;
            padding: 11px;
        }

        #logisticsFacilitiesWorkspace .fh-kpi-icon {
            display: none;
        }

        #logisticsFacilitiesWorkspace .fh-title {
            font-size: 23px;
        }
    }

    @media (prefers-reduced-motion:reduce) {
        #logisticsFacilitiesWorkspace *,
        #logisticsFacilitiesWorkspace *::before,
        #logisticsFacilitiesWorkspace *::after {
            transition-duration: .01ms !important;
            animation-duration: .01ms !important;
        }

        #logisticsFacilitiesWorkspace .fh-kpi:hover,
        #logisticsFacilitiesWorkspace .fh-hub:hover {
            transform: none !important;
        }
    }
</style>
@endpush

@section('content')

<div
    id="logisticsFacilitiesWorkspace"
    class="mx-auto w-full space-y-4"
>

    {{-- FLASH MESSAGES --}}

    @if(session('success'))
        <div
            class="rounded-xl border border-[#bfe0cd] bg-[#f1faf5] px-4 py-3 text-[10px] font-medium text-[#327a53]"
            role="status"
        >
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div
            id="facilityServerErrors"
            class="rounded-xl border border-[#efc6c6] bg-[#fff4f4] px-4 py-3 text-[10px] text-[#b53e3e]"
            role="alert"
        >
            <strong class="mb-2 block">
                Please review the following:
            </strong>

            <ul class="list-inside list-disc space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- PAGE HEADING --}}

    <header class="fh-heading">

        <div class="min-w-0">

            <p class="fh-eyebrow">
                Logistics Network
            </p>

            <h2 class="fh-title">
                Municipal <span>Hubs</span>
            </h2>

            <p class="fh-intro">
                Manage municipal sorting facilities and their assigned
                operators across {{ $logistics->displayName() }}.
            </p>

        </div>

        <div class="fh-heading-actions">

            <div
                class="fh-network-state {{ $stats['routing_ready'] ? '' : 'is-warning' }}"
                role="status"
            >
                <small>Network Status</small>

                <strong>
                    {{
                        $stats['routing_ready']
                            ? '✓ Routing Ready'
                            : 'Inter-hub setup incomplete'
                    }}
                </strong>
            </div>

            <a href="#hubEditor" class="fh-primary">
                {{ $editing ? 'Edit Hub' : '+ New Hub' }}
            </a>

        </div>

    </header>

    {{-- KPI CARDS --}}

    @php
        $facilityMetrics = [
            [
                'label' => 'Municipal Hubs',
                'value' => $stats['total'],
                'description' => 'Registered hub locations',
                'icon' => 'hub',
            ],
            [
                'label' => 'Active Hubs',
                'value' => $stats['active'],
                'description' => 'Eligible for new routes',
                'icon' => 'active',
            ],
            [
                'label' => 'Route History',
                'value' => $stats['used'],
                'description' => 'Hubs referenced by routes',
                'icon' => 'route',
            ],
            [
                'label' => 'Hub Operators',
                'value' => $stats['operator_accounts'],
                'description' => 'Operator accounts configured',
                'icon' => 'people',
            ],
        ];
    @endphp

    <section
        class="fh-kpis"
        aria-label="Municipal Hub overview"
    >

        @foreach($facilityMetrics as $metric)

            <article class="fh-kpi">

                <div class="min-w-0">

                    <p class="fh-kpi-label">
                        {{ $metric['label'] }}
                    </p>

                    <p class="fh-kpi-value">
                        {{ number_format($metric['value']) }}
                    </p>

                    <p class="fh-kpi-sub">
                        {{ $metric['description'] }}
                    </p>

                </div>

                <span class="fh-kpi-icon" aria-hidden="true">

                    @switch($metric['icon'])

                        @case('active')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="12" cy="12" r="9"/>
                                <path d="m8 12 3 3 5-6"/>
                            </svg>
                            @break

                        @case('route')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="5" cy="6" r="2"/>
                                <circle cx="19" cy="18" r="2"/>
                                <path d="M7 6h7a4 4 0 0 1 0 8h-3a4 4 0 0 0 0 8h6"/>
                            </svg>
                            @break

                        @case('people')
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <circle cx="8" cy="8" r="3"/>
                                <circle cx="17" cy="9" r="2.5"/>
                                <path d="M2.5 20c.5-4 2.5-6 5.5-6M13 15c3.2 0 5.2 1.7 5.7 5"/>
                            </svg>
                            @break

                        @default
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M3 21h18M5 21V8l7-4 7 4v13M8 12h3M13 12h3M8 16h8"/>
                            </svg>

                    @endswitch

                </span>

            </article>

        @endforeach

    </section>

    @unless($stats['routing_ready'])

        <div
            class="rounded-xl border border-[#ead7ae] bg-[#fff9ec] px-4 py-3 text-[10px] leading-5 text-[#936719]"
            role="status"
        >
            Inter-hub routing requires at least two active municipal hubs.
            A single active hub can still serve valid local deliveries.
        </div>

    @endunless

    {{-- MUNICIPAL HUB NETWORK --}}

    <section
        class="fh-panel"
        aria-labelledby="fhNetworkHeading"
    >

        <header class="fh-panel-head">

            <div>

                <p class="fh-eyebrow">
                    Municipal Hub Network
                </p>

                <h3
                    id="fhNetworkHeading"
                    class="fh-panel-title mt-1"
                >
                    Registered operational hubs
                </h3>

                <p class="fh-panel-desc">
                    Review the location, assigned operator, and routing
                    usage of each hub.
                </p>

            </div>

            <div class="fh-panel-tools">

                <label
                    class="sr-only"
                    for="facilitySearchInput"
                >
                    Search hub or operator
                </label>

                <input
                    id="facilitySearchInput"
                    type="search"
                    placeholder="Search hub or operator..."
                    class="fh-search"
                    autocomplete="off"
                >

                <span class="fh-counter">
                    {{ $facilities->count() }} HUBS
                </span>

            </div>

        </header>

        <div id="facilityNetworkList" class="fh-hubs">

            @forelse($facilities as $facility)

                @php
                    $hubOperator = $hubAccounts->get($facility->id);

                    $facilitySearch = strtolower(implode(' ', [
                        $facility->name,
                        $facility->code,
                        $facility->address,
                        $facility->province_name,
                        $facility->municipality_name,
                        $facility->barangay_name,
                        $hubOperator?->name,
                        $hubOperator?->email,
                    ]));
                @endphp

                <article
                    class="fh-hub"
                    data-facility-card
                    data-search="{{ $facilitySearch }}"
                >

                    <div class="fh-hub-body">

                        <span
                            class="fh-hub-symbol {{ $facility->is_active ? '' : 'is-inactive' }}"
                            aria-hidden="true"
                        >
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path d="M3 21h18M5 21V8l7-4 7 4v13M8 12h3M13 12h3"/>
                            </svg>
                        </span>

                        <div class="min-w-0 flex-1">

                            <div class="flex flex-wrap items-center gap-2">

                                <h4 class="fh-hub-name">
                                    {{ $facility->name }}
                                </h4>

                                <span
                                    class="fh-status {{ $facility->is_active ? '' : 'is-inactive' }}"
                                >
                                    {{ $facility->is_active ? 'Active' : 'Inactive' }}
                                </span>

                            </div>

                            <p class="fh-hub-address">
                                {{ $facility->address }}
                            </p>

                            <p class="fh-hub-meta">
                                {{ $facility->municipality_name }},
                                {{ $facility->province_name }}
                                ·
                                {{ $facility->route_stops_count }} route uses
                            </p>

                            <p class="fh-hub-meta">
                                {{ number_format((float) $facility->latitude, 5) }},
                                {{ number_format((float) $facility->longitude, 5) }}
                            </p>

                        </div>

                    </div>

                    {{-- HUB OPERATOR --}}

                    <div class="fh-operator">

                        <div class="min-w-0">

                            <small>Hub Operator</small>

                            @if($hubOperator)

                                <strong>
                                    {{ $hubOperator->name }}
                                </strong>

                                <p>
                                    {{ $hubOperator->email }}
                                </p>

                            @else

                                <strong class="!text-[#a96e05]">
                                    No operator account assigned
                                </strong>

                            @endif

                        </div>

                        <span
                            class="fh-status {{
                                $hubOperator && $hubOperator->account_status === 'active'
                                    ? ''
                                    : 'is-inactive'
                            }}"
                        >
                            {{
                                $hubOperator
                                    ? str_replace('_', ' ', $hubOperator->account_status)
                                    : 'Setup Needed'
                            }}
                        </span>

                    </div>

                    {{-- ALIGNED CARD ACTIONS --}}

                    <footer class="fh-hub-footer">

                        <span class="fh-code">
                            {{ $facility->code }}
                        </span>

                        <div class="fh-actions">

                            <a
                                class="fh-action"
                                href="{{ route('logistics.facilities.edit', $facility) }}#hubEditor"
                            >
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="{{ route('logistics.facilities.toggle', $facility) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="fh-action is-warning"
                                >
                                    {{ $facility->is_active ? 'Deactivate' : 'Activate' }}
                                </button>

                            </form>

                            @if((int) $facility->route_stops_count === 0)

                                <form
                                    method="POST"
                                    action="{{ route('logistics.facilities.destroy', $facility) }}"
                                    onsubmit="return confirm('Delete this unused municipal hub and its operator?');"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="fh-action is-danger"
                                    >
                                        Delete
                                    </button>

                                </form>

                            @endif

                        </div>

                    </footer>

                </article>

            @empty

                <div class="fh-empty">

                    <strong class="block text-[11px] text-[#51483d]">
                        No municipal hubs yet
                    </strong>

                    <p class="mt-2">
                        Create your first hub and its operator using the form below.
                    </p>

                </div>

            @endforelse

            <div
                id="facilitySearchEmpty"
                class="fh-empty hidden"
            >

                <strong class="block text-[11px] text-[#51483d]">
                    No matching hub
                </strong>

                <p class="mt-2">
                    Try a different search keyword.
                </p>

            </div>

        </div>

    </section>

    {{-- HUB EDITOR --}}

    <section
        id="hubEditor"
        class="scroll-mt-24 overflow-hidden rounded-[20px] border border-[#e8dfd1] bg-white shadow-sm"
    >

        <div
            class="flex flex-wrap items-center justify-between gap-3 border-b border-[#eee5d8] px-5 py-4"
        >

            <div>

                <p class="text-[9px] font-bold uppercase tracking-wider text-[#b97805]">
                    {{ $editing ? 'Edit Municipal Hub' : 'Create Municipal Hub' }}
                </p>

                <h3 class="mt-1 text-[16px] font-bold text-[#302920]">
                    {{ $editing ? $editing->name : 'Hub + Operator Setup' }}
                </h3>

                <p class="mt-1 text-[10px] text-[#918779]">
                    Configure the physical location and the Hub Operator
                    assigned to this municipality.
                </p>

            </div>

            @if($editing)

                <a
                    href="{{ route('logistics.facilities.index') }}#hubEditor"
                    class="rounded-[9px] border border-[#e4dacd] px-4 py-2 text-[10px] font-semibold text-[#81786c] hover:bg-[#faf7f1]"
                >
                    Cancel Edit
                </a>

            @endif

        </div>

        <form
            id="sariFacilityForm"
            method="POST"
            action="{{ $editing
                ? route('logistics.facilities.update', $editing)
                : route('logistics.facilities.store') }}"
            class="p-5"
        >

            @csrf

            @if($editing)
                @method('PATCH')
            @endif

            <div
                class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(360px,1fr)]"
            >

                {{-- LEFT: HUB DETAILS --}}

                <div class="min-w-0 space-y-5">

                    <div class="border-b border-[#eee5d8] pb-3">

                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#9e6b10]">
                            01 · Hub Details
                        </p>

                        <p class="mt-1 text-[9px] text-[#998f82]">
                            Physical facility used for parcel intake, sorting,
                            transfer and destination handling.
                        </p>

                    </div>

                    {{-- HUB NAME --}}

                    <div>

                        <label for="facilityName" class="{{ $labelClass }}">
                            Hub Name *
                        </label>

                        <input
                            id="facilityName"
                            name="name"
                            value="{{ old('name', $editing?->name) }}"
                            maxlength="140"
                            required
                            placeholder="e.g. SARI Calamba Hub"
                            class="{{ $fieldClass }}"
                        >

                    </div>

                    {{-- FULL ADDRESS --}}

                    <div>

                        <label for="facilityAddress" class="{{ $labelClass }}">
                            Full Address / Landmark *
                        </label>

                        <textarea
                            id="facilityAddress"
                            name="address"
                            rows="3"
                            maxlength="1200"
                            required
                            placeholder="Building, street, subdivision, landmark..."
                            class="w-full resize-none rounded-[10px] border border-[#e5ddd1] bg-white px-3 py-3 text-[11px] outline-none focus:border-[#d3ad63]"
                        >{{ old('address', $editing?->address) }}</textarea>

                    </div>

                    {{-- ADDRESS API STATUS --}}

                    <div
                        id="facilityAddressStatus"
                        role="status"
                        class="rounded-[10px] border border-[#eadfc9] bg-[#fffaf2] px-3 py-3 text-[10px] text-[#98753b]"
                    >
                        Loading Philippine address directory...
                    </div>

                    {{-- PROVINCE + MUNICIPALITY --}}

                    <div class="grid gap-4 sm:grid-cols-2">

                        <div class="hub-address-wrap">

                            <label
                                for="facility-province-search"
                                class="{{ $labelClass }}"
                            >
                                Province / Area *
                            </label>

                            <input
                                id="facility-province-search"
                                type="text"
                                autocomplete="off"
                                placeholder="Search province..."
                                value="{{ old('province_name', $editing?->province_name) }}"
                                required
                                class="{{ $fieldClass }}"
                            >

                            <input
                                id="facility-province-name"
                                type="hidden"
                                name="province_name"
                                value="{{ old('province_name', $editing?->province_name) }}"
                            >

                            <input
                                id="facility-province-code"
                                type="hidden"
                                name="province_code"
                            >

                            <div
                                id="facility-province-results"
                                class="hub-address-results"
                                hidden
                            ></div>

                        </div>

                        <div class="hub-address-wrap">

                            <label
                                for="facility-municipality-search"
                                class="{{ $labelClass }}"
                            >
                                City / Municipality *
                            </label>

                            <input
                                id="facility-municipality-search"
                                type="text"
                                autocomplete="off"
                                placeholder="Choose province first"
                                value="{{ old('municipality_name', $editing?->municipality_name) }}"
                                disabled
                                required
                                class="{{ $fieldClass }}"
                            >

                            <input
                                id="facility-municipality-name"
                                type="hidden"
                                name="municipality_name"
                                value="{{ old('municipality_name', $editing?->municipality_name) }}"
                            >

                            <input
                                id="facility-municipality-code"
                                type="hidden"
                                name="municipality_code"
                            >

                            <div
                                id="facility-municipality-results"
                                class="hub-address-results"
                                hidden
                            ></div>

                        </div>

                    </div>

                    {{-- BARANGAY --}}

                    <div class="hub-address-wrap">

                        <label
                            for="facility-barangay-search"
                            class="{{ $labelClass }}"
                        >
                            Barangay
                            <span class="font-normal text-[#aaa198]">
                                (optional)
                            </span>
                        </label>

                        <input
                            id="facility-barangay-search"
                            type="text"
                            autocomplete="off"
                            placeholder="Choose municipality first"
                            value="{{ old('barangay_name', $editing?->barangay_name) }}"
                            disabled
                            class="{{ $fieldClass }}"
                        >

                        <input
                            id="facility-barangay-name"
                            type="hidden"
                            name="barangay_name"
                            value="{{ old('barangay_name', $editing?->barangay_name) }}"
                        >

                        <input
                            id="facility-barangay-code"
                            type="hidden"
                            name="barangay_code"
                        >

                        <div
                            id="facility-barangay-results"
                            class="hub-address-results"
                            hidden
                        ></div>

                        <p class="mt-2 text-[9px] text-[#998f82]">
                            Select a result from the address directory.
                            Barangay is optional for hub details.
                        </p>

                    </div>

                    {{-- MAP --}}

                    <div class="space-y-3">

                        <div
                            class="flex items-center justify-between gap-3"
                        >

                            <div>

                                <p class="text-[10px] font-semibold text-[#5f574e]">
                                    Exact Map Location *
                                </p>

                                <p class="mt-1 text-[9px] text-[#9a9084]">
                                    Click the map at the physical hub location.
                                </p>

                            </div>

                            <span
                                id="sariFacilityMapStatus"
                                class="rounded-full px-3 py-1 text-[8px] font-bold
                                {{ old('latitude', $editing?->latitude) && old('longitude', $editing?->longitude)
                                    ? 'bg-[#edf8f1] text-[#327a53]'
                                    : 'bg-[#fff6e5] text-[#a96e05]' }}"
                            >
                                {{ old('latitude', $editing?->latitude) && old('longitude', $editing?->longitude)
                                    ? 'PIN SET'
                                    : 'SELECT PIN' }}
                            </span>

                        </div>

                        <div id="sariFacilityMap"></div>

                        <div class="grid grid-cols-2 gap-3">

                            <div>

                                <label
                                    for="sariFacilityLatitude"
                                    class="{{ $labelClass }}"
                                >
                                    Latitude
                                </label>

                                <input
                                    id="sariFacilityLatitude"
                                    name="latitude"
                                    type="number"
                                    step="0.0000001"
                                    min="4"
                                    max="22.5"
                                    value="{{ old('latitude', $editing?->latitude) }}"
                                    readonly
                                    required
                                    class="{{ $fieldClass }} bg-[#faf8f4]"
                                >

                            </div>

                            <div>

                                <label
                                    for="sariFacilityLongitude"
                                    class="{{ $labelClass }}"
                                >
                                    Longitude
                                </label>

                                <input
                                    id="sariFacilityLongitude"
                                    name="longitude"
                                    type="number"
                                    step="0.0000001"
                                    min="116"
                                    max="127.5"
                                    value="{{ old('longitude', $editing?->longitude) }}"
                                    readonly
                                    required
                                    class="{{ $fieldClass }} bg-[#faf8f4]"
                                >

                            </div>

                        </div>

                    </div>

                    {{-- ACTIVE STATUS --}}

                    <label
                        class="flex cursor-pointer items-start gap-3 rounded-[11px] border border-[#eee4d3] bg-[#fffdf8] p-4"
                    >

                        <input
                            name="is_active"
                            type="checkbox"
                            value="1"
                            @checked(old('is_active', $editing?->is_active ?? true))
                            class="mt-0.5 h-4 w-4 rounded border-[#d9cdbb] text-[#c98913]"
                        >

                        <span>

                            <strong class="block text-[10px] text-[#51493f]">
                                Active for new routes
                            </strong>

                            <small class="mt-1 block text-[9px] leading-5 text-[#93897e]">
                                Inactive hubs remain in historical route records
                                but cannot be selected for new routing.
                            </small>

                        </span>

                    </label>

                </div>

                {{-- RIGHT: HUB OPERATOR --}}

                <div class="min-w-0">

                    <section
                        class="rounded-[15px] border border-[#eadfcf] bg-[#fffdf9] p-4 sm:p-5"
                    >

                        <div
                            class="mb-5 flex items-start justify-between gap-3 border-b border-[#eee5d8] pb-3"
                        >

                            <div>

                                <p class="text-[10px] font-bold uppercase tracking-wider text-[#9e6b10]">
                                    02 · Hub Operator
                                </p>

                                <p class="mt-1 text-[9px] leading-5 text-[#998f82]">
                                    {{ $editingHasOperator
                                        ? 'Update the login identity assigned to this hub.'
                                        : 'Create an operator account scoped to this hub.' }}
                                </p>

                            </div>

                            @if($editingHasOperator)

                                <span
                                    class="shrink-0 rounded-full bg-[#edf8f1] px-3 py-1 text-[8px] font-bold text-[#327a53]"
                                >
                                    Configured
                                </span>

                            @endif

                        </div>

                        <div class="space-y-5">

                            {{-- OPERATOR NAME --}}

                            <div>

                                <label
                                    for="hubOperatorName"
                                    class="{{ $labelClass }}"
                                >
                                    Operator Name {{ $operatorRequired ? '*' : '' }}
                                </label>

                                <input
                                    id="hubOperatorName"
                                    name="operator_name"
                                    type="text"
                                    value="{{ old('operator_name', $editingOperator?->name) }}"
                                    minlength="2"
                                    maxlength="50"
                                    autocomplete="name"
                                    placeholder="e.g. Juan Dela Cruz"
                                    @if($operatorRequired) required @endif
                                    aria-describedby="hubNameFeedback"
                                    class="{{ $fieldClass }}"
                                >

                                <small
                                    id="hubNameFeedback"
                                    class="hub-feedback"
                                    role="status"
                                >
                                    2–50 characters. Letters including ñ,
                                    spaces, hyphens, apostrophes and periods only.
                                </small>

                            </div>

                            {{-- OPERATOR EMAIL --}}

                            <div>

                                <label
                                    for="hubOperatorEmail"
                                    class="{{ $labelClass }}"
                                >
                                    Operator Email {{ $operatorRequired ? '*' : '' }}
                                </label>

                                <input
                                    id="hubOperatorEmail"
                                    name="operator_email"
                                    type="email"
                                    value="{{ old('operator_email', $editingOperator?->email) }}"
                                    maxlength="255"
                                    autocomplete="off"
                                    placeholder="operator@example.com"
                                    @if($operatorRequired) required @endif
                                    aria-describedby="hubEmailFeedback"
                                    class="{{ $fieldClass }}"
                                >

                                <small
                                    id="hubEmailFeedback"
                                    class="hub-feedback"
                                    role="status"
                                >
                                    Enter a valid email. Uniqueness is checked
                                    when saved.
                                </small>

                            </div>

                            {{-- ACCOUNT SECURITY --}}

                            <div class="border-t border-[#eee5d8] pt-4">

                                <p class="text-[10px] font-bold text-[#51493f]">
                                    Account Security
                                </p>

                                <p class="mt-1 text-[9px] text-[#93897e]">
                                    Create a secure password for the Hub Operator.
                                </p>

                            </div>

                            {{-- PASSWORD --}}

                            <div>

                                <label
                                    for="hubOperatorPassword"
                                    class="{{ $labelClass }}"
                                >
                                    {{ $editingHasOperator ? 'New Password' : 'Temporary Password' }}
                                    {{ $operatorRequired ? '*' : '' }}
                                </label>

                                <div class="hub-password-wrap">

                                    <input
                                        id="hubOperatorPassword"
                                        name="operator_password"
                                        type="password"
                                        minlength="8"
                                        maxlength="255"
                                        autocomplete="new-password"
                                        placeholder="Enter secure password"
                                        @if($operatorRequired) required @endif
                                        aria-describedby="hubPasswordRules"
                                        class="{{ $fieldClass }}"
                                    >

                                    <button
                                        id="hubTogglePassword"
                                        type="button"
                                        class="hub-password-eye"
                                        aria-label="Show password"
                                        aria-pressed="false"
                                        title="Show password"
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>

                                </div>

                                {{-- LIVE PASSWORD REQUIREMENTS --}}

                                <div
                                    id="hubPasswordRules"
                                    class="hub-password-rules"
                                    aria-live="polite"
                                >

                                    <span
                                        class="hub-password-rule"
                                        data-password-rule="length"
                                    >
                                        At least 8 characters
                                    </span>

                                    <span
                                        class="hub-password-rule"
                                        data-password-rule="uppercase"
                                    >
                                        Uppercase letter
                                    </span>

                                    <span
                                        class="hub-password-rule"
                                        data-password-rule="lowercase"
                                    >
                                        Lowercase letter
                                    </span>

                                    <span
                                        class="hub-password-rule"
                                        data-password-rule="number"
                                    >
                                        At least one number
                                    </span>

                                    <span
                                        class="hub-password-rule"
                                        data-password-rule="special"
                                    >
                                        Special character
                                    </span>

                                </div>

                                <small
                                    id="hubPasswordFeedback"
                                    class="hub-feedback"
                                    role="status"
                                >
                                    Complete all five password requirements.
                                </small>

                            </div>

                            {{-- CONFIRM PASSWORD --}}

                            <div>

                                <label
                                    for="hubOperatorConfirmPassword"
                                    class="{{ $labelClass }}"
                                >
                                    Confirm Password {{ $operatorRequired ? '*' : '' }}
                                </label>

                                <div class="hub-password-wrap">

                                    <input
                                        id="hubOperatorConfirmPassword"
                                        name="operator_password_confirmation"
                                        type="password"
                                        minlength="8"
                                        maxlength="255"
                                        autocomplete="new-password"
                                        placeholder="Repeat password"
                                        @if($operatorRequired) required @endif
                                        aria-describedby="hubConfirmFeedback"
                                        class="{{ $fieldClass }}"
                                    >

                                    <button
                                        id="hubToggleConfirmation"
                                        type="button"
                                        class="hub-password-eye"
                                        aria-label="Show password confirmation"
                                        aria-pressed="false"
                                        title="Show confirmation"
                                    >
                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </button>

                                </div>

                                <small
                                    id="hubConfirmFeedback"
                                    class="hub-feedback"
                                    role="status"
                                    aria-live="polite"
                                >
                                    Re-enter your password to confirm.
                                </small>

                            </div>

                            @if($editingHasOperator)

                                <p
                                    class="rounded-[9px] bg-[#f7f3ec] px-3 py-3 text-[10px] leading-5 text-[#81766a]"
                                >
                                    Leave both password fields blank to retain
                                    the current Hub Operator password.
                                </p>

                            @endif

                        </div>

                    </section>

                    {{-- ARCHITECTURE NOTE --}}

                    <div
                        class="mt-4 rounded-[12px] border border-[#eee5d8] bg-[#fcfaf7] p-4"
                    >

                        <p class="text-[9px] font-bold text-[#696056]">
                            Hub architecture
                        </p>

                        <p class="mt-1 text-[9px] leading-5 text-[#948a7e]">
                            The hub stays under {{ $logistics->displayName() }}.
                            Its Hub Operator is a facility-scoped login and
                            does not become a separate Logistics provider.
                        </p>

                    </div>

                </div>

            </div>

            {{-- SAVE BUTTON --}}

            <div
                class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-[#eee5d8] pt-5"
            >

                @if($editing)

                    <a
                        href="{{ route('logistics.facilities.index') }}#hubEditor"
                        class="rounded-[10px] border border-[#e5ddd1] px-5 py-3 text-[11px] font-semibold text-[#63594f]"
                    >
                        Cancel
                    </a>

                @endif

                <button
                    id="hubSubmitButton"
                    type="submit"
                    class="min-w-[210px] rounded-[10px] bg-[#d9930a] px-6 py-3.5 text-[11px] font-bold text-white"
                >
                    {{ $editing ? 'Save Hub Changes' : 'Create Hub + Operator' }}
                </button>

            </div>

        </form>

    </section>

</div>

@endsection

@push('scripts')

<script src="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | FORM REFERENCES
    |--------------------------------------------------------------------------
    */

    const facilityForm = document.getElementById('sariFacilityForm');

    if (!facilityForm) {
        return;
    }

    const passwordOptional = @json(
        (bool) ($editing && $editingHasOperator)
    );

    /*
    |--------------------------------------------------------------------------
    | HUB SEARCH
    |--------------------------------------------------------------------------
    */

    const searchInput = document.getElementById('facilitySearchInput');

    const facilityCards = Array.from(
        document.querySelectorAll('[data-facility-card]')
    );

    const searchEmpty = document.getElementById('facilitySearchEmpty');

    function filterFacilities() {

        const keyword = String(searchInput?.value || '')
            .trim()
            .toLowerCase();

        let visible = 0;

        facilityCards.forEach(function (card) {

            const searchable = String(card.dataset.search || '').toLowerCase();

            const show = !keyword || searchable.includes(keyword);

            card.classList.toggle('hidden', !show);

            if (show) {
                visible++;
            }

        });

        if (searchEmpty && facilityCards.length) {
            searchEmpty.classList.toggle('hidden', visible !== 0);
        }

    }

    searchInput?.addEventListener('input', filterFacilities);

    /*
    |--------------------------------------------------------------------------
    | OPERATOR NAME + EMAIL
    |--------------------------------------------------------------------------
    */

    const operatorName = document.getElementById('hubOperatorName');
    const operatorEmail = document.getElementById('hubOperatorEmail');

    const nameFeedback = document.getElementById('hubNameFeedback');
    const emailFeedback = document.getElementById('hubEmailFeedback');

    const namePattern = /^[\p{L}\p{M} .'\u2019-]+$/u;

    function updateFeedback(input, node, valid, message, touched) {

        input.classList.toggle('hub-input-valid', touched && valid);

        input.classList.toggle('hub-input-invalid', touched && !valid);

        input.setAttribute('aria-invalid', String(touched && !valid));

        node.classList.toggle('valid', touched && valid);
        node.classList.toggle('invalid', touched && !valid);

        node.textContent = message;

        return valid;
    }

    function validateName(touched = true) {

        const value = operatorName.value.trim().replace(/\s+/gu, ' ');

        const optionalBlank = passwordOptional && value.length === 0;

        const valid =
            optionalBlank ||
            (
                [...value].length >= 2 &&
                [...value].length <= 50 &&
                namePattern.test(value) &&
                /\p{L}/u.test(value)
            );

        let message =
            '2–50 characters. Letters, spaces, hyphens, apostrophes and periods only.';

        if (touched) {

            message = valid
                ? '✓ Valid operator name.'
                : '✕ Name must be 2–50 characters and contain only allowed characters.';

        }

        if (optionalBlank) {
            message = 'Existing operator name will be retained.';
        }

        return updateFeedback(
            operatorName,
            nameFeedback,
            valid,
            message,
            touched && !optionalBlank
        );
    }

    function validateEmail(touched = true) {

        const value = operatorEmail.value.trim();

        const optionalBlank = passwordOptional && value.length === 0;

        const valid =
            optionalBlank ||
            (
                value.length <= 255 &&
                /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value) &&
                operatorEmail.validity.valid
            );

        let message =
            'Enter a valid email. Availability is checked when saved.';

        if (touched) {

            message = valid
                ? '✓ Valid email format. Uniqueness will be checked when saved.'
                : '✕ Please enter a valid email address.';

        }

        if (optionalBlank) {
            message = 'Existing operator email will be retained.';
        }

        return updateFeedback(
            operatorEmail,
            emailFeedback,
            valid,
            message,
            touched && !optionalBlank
        );
    }

    operatorName.addEventListener('input', function () {
        validateName();
    });

    operatorName.addEventListener('blur', function () {

        operatorName.value = operatorName.value
            .trim()
            .replace(/\s+/gu, ' ');

        validateName();
    });

    operatorEmail.addEventListener('input', function () {
        validateEmail();
    });

    operatorEmail.addEventListener('blur', function () {

        operatorEmail.value = operatorEmail.value
            .trim()
            .toLowerCase();

        validateEmail();
    });

    /*
    |--------------------------------------------------------------------------
    | PASSWORD VISIBILITY
    |--------------------------------------------------------------------------
    */

    const password = document.getElementById('hubOperatorPassword');

    const confirmation = document.getElementById(
        'hubOperatorConfirmPassword'
    );

    const passwordToggle = document.getElementById('hubTogglePassword');

    const confirmationToggle = document.getElementById(
        'hubToggleConfirmation'
    );

    const passwordFeedback = document.getElementById(
        'hubPasswordFeedback'
    );

    const confirmFeedback = document.getElementById(
        'hubConfirmFeedback'
    );

    function bindVisibility(input, button) {

        if (!input || !button) {
            return;
        }

        button.addEventListener('click', function () {

            const isVisible = input.type === 'text';

            input.type = isVisible ? 'password' : 'text';

            button.setAttribute(
                'aria-pressed',
                String(!isVisible)
            );

            button.setAttribute(
                'aria-label',
                isVisible ? 'Show password' : 'Hide password'
            );

            button.setAttribute(
                'title',
                isVisible ? 'Show password' : 'Hide password'
            );

            button.style.color = isVisible ? '#82776a' : '#b97805';

        });
    }

    bindVisibility(password, passwordToggle);
    bindVisibility(confirmation, confirmationToggle);

    /*
    |--------------------------------------------------------------------------
    | LIVE PASSWORD REQUIREMENTS
    |--------------------------------------------------------------------------
    */

    const passwordChecks = {

        length(value) {
            return value.length >= 8 && value.length <= 255;
        },

        uppercase(value) {
            return /[A-Z]/.test(value);
        },

        lowercase(value) {
            return /[a-z]/.test(value);
        },

        number(value) {
            return /[0-9]/.test(value);
        },

        special(value) {
            return /[^A-Za-z0-9\s]/.test(value);
        }

    };

    function validatePassword(touched = true) {

        const value = password.value;

        const optionalBlank =
            passwordOptional &&
            value === '' &&
            confirmation.value === '';

        let allValid = true;

        Object.entries(passwordChecks).forEach(function ([rule, check]) {

            const passed = check(value);

            const element = document.querySelector(
                '[data-password-rule="' + rule + '"]'
            );

            if (!passed) {
                allValid = false;
            }

            if (element) {

                element.classList.toggle('valid', passed);

                element.classList.toggle(
                    'invalid',
                    touched && value.length > 0 && !passed
                );

            }

        });

        const valid = optionalBlank || allValid;

        password.classList.toggle(
            'hub-input-valid',
            touched && !optionalBlank && allValid
        );

        password.classList.toggle(
            'hub-input-invalid',
            touched && !optionalBlank && value.length > 0 && !allValid
        );

        passwordFeedback.classList.toggle(
            'valid',
            touched && !optionalBlank && allValid
        );

        passwordFeedback.classList.toggle(
            'invalid',
            touched && !optionalBlank && value.length > 0 && !allValid
        );

        if (optionalBlank) {

            passwordFeedback.textContent =
                'Leave both fields blank to retain the current password.';

        } else if (!value.length) {

            passwordFeedback.textContent =
                'Complete all five password requirements.';

        } else if (allValid) {

            passwordFeedback.textContent =
                '✓ All password requirements met.';

        } else {

            passwordFeedback.textContent =
                'Complete the remaining password requirements.';

        }

        password.setCustomValidity(
            valid
                ? ''
                : 'Password must meet all five security requirements.'
        );

        return valid;
    }

    /*
    |--------------------------------------------------------------------------
    | LIVE CONFIRM PASSWORD MATCHING
    |--------------------------------------------------------------------------
    */

    function validateConfirmation(touched = true) {

        const original = password.value;
        const repeated = confirmation.value;

        const optionalBlank =
            passwordOptional &&
            original === '' &&
            repeated === '';

        let valid = false;
        let message = '';

        if (optionalBlank) {

            valid = true;
            message = 'Leave both fields blank to retain the current password.';

        } else if (!repeated.length) {

            message = 'Please confirm your password.';

        } else if (original === repeated) {

            valid = true;
            message = '✓ Passwords match.';

        } else {

            message = '✕ Passwords do not match yet.';

        }

        confirmFeedback.textContent = message;

        confirmFeedback.classList.toggle(
            'valid',
            touched && valid && !optionalBlank
        );

        confirmFeedback.classList.toggle(
            'invalid',
            touched && !valid && repeated.length > 0
        );

        confirmation.classList.toggle(
            'hub-input-valid',
            touched && valid && !optionalBlank
        );

        confirmation.classList.toggle(
            'hub-input-invalid',
            touched && !valid && repeated.length > 0
        );

        confirmation.setCustomValidity(
            valid ? '' : 'Passwords do not match.'
        );

        return valid;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATE DURING TYPING
    |--------------------------------------------------------------------------
    */

    password.addEventListener('input', function () {
        validatePassword();
        validateConfirmation();
    });

    confirmation.addEventListener('input', function () {
        validatePassword();
        validateConfirmation();
    });

    validateName(false);
    validateEmail(false);
    validatePassword(false);
    validateConfirmation(false);

    /*
    |--------------------------------------------------------------------------
    | PHILIPPINE ADDRESS DIRECTORY
    |--------------------------------------------------------------------------
    */

    const addressKeys = [
        'province',
        'municipality',
        'barangay'
    ];

    const fields = Object.fromEntries(
        addressKeys.map(function (key) {

            return [
                key,
                {
                    search: document.getElementById('facility-' + key + '-search'),
                    results: document.getElementById('facility-' + key + '-results'),
                    name: document.getElementById('facility-' + key + '-name'),
                    code: document.getElementById('facility-' + key + '-code')
                }
            ];

        })
    );

    const addressStatus = document.getElementById('facilityAddressStatus');

    let provinces = [];
    let municipalities = [];
    let barangays = [];

    let manualMode = false;

    let cityRequestId = 0;
    let barangayRequestId = 0;

    const savedAddress = {
        province: fields.province.name.value.trim(),
        municipality: fields.municipality.name.value.trim(),
        barangay: fields.barangay.name.value.trim()
    };

    function normalizeItems(payload) {

        if (Array.isArray(payload)) {
            return payload;
        }

        if (Array.isArray(payload?.data)) {
            return payload.data;
        }

        if (Array.isArray(payload?.data?.data)) {
            return payload.data.data;
        }

        if (Array.isArray(payload?.results)) {
            return payload.results;
        }

        if (Array.isArray(payload?.items)) {
            return payload.items;
        }

        return [];
    }

    function locationCode(item) {

        return String(
            item.code ??
            item.psgc_code ??
            item.psgcCode ??
            item.id ??
            ''
        );
    }

    function locationName(item) {

        return String(
            item.name ??
            item.area_name ??
            item.areaName ??
            item.label ??
            ''
        );
    }

    async function fetchDirectory(url) {

        const response = await fetch(url, {

            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },

            cache: 'no-store'

        });

        if (!response.ok) {
            throw new Error('Address lookup failed: ' + response.status);
        }

        return normalizeItems(await response.json());
    }

    function setAddressStatus(message, error = false) {

        addressStatus.textContent = message;

        addressStatus.style.color = error
            ? '#b53e3e'
            : '#98753b';
    }

    function enableManualAddress(message) {

        manualMode = true;

        setAddressStatus(
            message + ' Manual address entry has been enabled.',
            true
        );

        addressKeys.forEach(function (key) {

            const field = fields[key];

            field.search.disabled = false;

            field.search.placeholder = 'Enter ' + key;

            field.results.hidden = true;

            field.name.value = field.search.value.trim();

        });
    }

    function clearField(key) {

        const field = fields[key];

        field.search.value = '';
        field.name.value = '';
        field.code.value = '';

        field.results.hidden = true;
    }

    function resetBelow(key) {

        if (key === 'province') {

            cityRequestId++;
            municipalities = [];

            clearField('municipality');

            fields.municipality.search.disabled = true;

        }

        if (key === 'province' || key === 'municipality') {

            barangayRequestId++;
            barangays = [];

            clearField('barangay');

            fields.barangay.search.disabled = true;

        }
    }

    function showOptions(key, items) {

        const field = fields[key];

        const query = field.search.value.trim().toLowerCase();

        const matches = items.filter(function (item) {

            return locationName(item).toLowerCase().includes(query);

        }).slice(0, 40);

        field.results.innerHTML = '';

        if (!matches.length) {

            const empty = document.createElement('p');

            empty.className = 'px-3 py-3 text-[10px] text-[#918779]';

            empty.textContent = 'No matching location found.';

            field.results.appendChild(empty);

        } else {

            matches.forEach(function (item) {

                const button = document.createElement('button');

                button.type = 'button';

                button.textContent = locationName(item);

                button.addEventListener('mousedown', function (event) {

                    event.preventDefault();

                    selectLocation(key, item);

                });

                field.results.appendChild(button);

            });

        }

        field.results.hidden = false;
    }

    function selectLocation(key, item, restore = '') {

        const field = fields[key];

        field.search.value = locationName(item);

        field.name.value = locationName(item);

        field.code.value = locationCode(item);

        field.results.hidden = true;

        if (key === 'province') {

            resetBelow('province');

            loadMunicipalities(locationCode(item), restore);

        } else if (key === 'municipality') {

            resetBelow('municipality');

            loadBarangays(locationCode(item), restore);

        }
    }

    async function loadMunicipalities(provinceCode, restore = '') {

        const requestId = ++cityRequestId;

        setAddressStatus('Loading cities and municipalities...');

        try {

            const url =
                @json(url('/address/provinces')) +
                '/' +
                encodeURIComponent(provinceCode) +
                '/municipalities';

            const items = await fetchDirectory(url);

            if (requestId !== cityRequestId) {
                return;
            }

            municipalities = items;

            fields.municipality.search.disabled = false;

            fields.municipality.search.placeholder =
                'Search city / municipality...';

            setAddressStatus('Address directory ready.');

            if (restore) {

                const match = items.find(function (item) {

                    return locationName(item).toLowerCase() ===
                        restore.toLowerCase();

                });

                if (match) {

                    selectLocation(
                        'municipality',
                        match,
                        savedAddress.barangay
                    );

                } else {

                    fields.municipality.search.value = restore;

                    setAddressStatus(
                        'Previously saved municipality not found. Select a current result.',
                        true
                    );

                }

            }

        } catch (error) {

            if (requestId !== cityRequestId) {
                return;
            }

            console.error(error);

            enableManualAddress('Municipality directory unavailable.');

        }
    }

    async function loadBarangays(municipalityCode, restore = '') {

        const requestId = ++barangayRequestId;

        setAddressStatus('Loading barangays...');

        try {

            const url =
                @json(url('/address/municipalities')) +
                '/' +
                encodeURIComponent(municipalityCode) +
                '/barangays';

            const items = await fetchDirectory(url);

            if (requestId !== barangayRequestId) {
                return;
            }

            barangays = items;

            fields.barangay.search.disabled = false;

            fields.barangay.search.placeholder =
                'Search barangay (optional)...';

            setAddressStatus('Address directory ready.');

            if (restore) {

                const match = items.find(function (item) {

                    return locationName(item).toLowerCase() ===
                        restore.toLowerCase();

                });

                if (match) {

                    selectLocation('barangay', match);

                } else {

                    fields.barangay.search.value = restore;

                    setAddressStatus(
                        'Previously saved barangay not found. Select a current result.',
                        true
                    );

                }

            }

        } catch (error) {

            if (requestId !== barangayRequestId) {
                return;
            }

            console.error(error);

            enableManualAddress('Barangay directory unavailable.');

        }
    }

    /*
    |--------------------------------------------------------------------------
    | ADDRESS SEARCH INTERACTIONS
    |--------------------------------------------------------------------------
    */

    addressKeys.forEach(function (key) {

        const field = fields[key];

        function currentItems() {

            if (key === 'province') {
                return provinces;
            }

            if (key === 'municipality') {
                return municipalities;
            }

            return barangays;
        }

        field.search.addEventListener('focus', function () {

            if (!manualMode && !field.search.disabled) {

                showOptions(key, currentItems());

            }
        });

        field.search.addEventListener('input', function () {

            if (manualMode) {

                field.name.value = field.search.value.trim();

                return;
            }

            if (field.search.value !== field.name.value) {

                field.name.value = '';

                field.code.value = '';

                if (key !== 'barangay') {
                    resetBelow(key);
                }

            }

            showOptions(key, currentItems());
        });

        field.search.addEventListener('keydown', function (event) {

            if (event.key === 'Escape') {

                field.results.hidden = true;

            }

            if (event.key === 'Enter' && !field.results.hidden) {

                const first = field.results.querySelector('button');

                if (first) {

                    event.preventDefault();

                    first.dispatchEvent(new MouseEvent('mousedown', {
                        bubbles: true
                    }));

                }

            }
        });

        document.addEventListener('click', function (event) {

            if (
                !field.search.contains(event.target) &&
                !field.results.contains(event.target)
            ) {
                field.results.hidden = true;
            }

        });

    });

    /*
    |--------------------------------------------------------------------------
    | INITIALIZE ADDRESS DIRECTORY
    |--------------------------------------------------------------------------
    */

    async function initializeAddressDirectory() {

        try {

            provinces = await fetchDirectory(
                @json(route('address.provinces'))
            );

            if (!provinces.length) {
                throw new Error('No provinces returned.');
            }

            setAddressStatus('Address directory ready.');

            if (savedAddress.province) {

                const match = provinces.find(function (item) {

                    return locationName(item).toLowerCase() ===
                        savedAddress.province.toLowerCase();

                });

                if (match) {

                    selectLocation(
                        'province',
                        match,
                        savedAddress.municipality
                    );

                } else {

                    setAddressStatus(
                        'Previously saved province not found. Select a current result.',
                        true
                    );

                }

            }

        } catch (error) {

            console.error(error);

            enableManualAddress('Philippine address directory unavailable.');

        }
    }

    initializeAddressDirectory();

    /*
    |--------------------------------------------------------------------------
    | MAPLIBRE MAP
    |--------------------------------------------------------------------------
    */

    const mapNode = document.getElementById('sariFacilityMap');

    const latitudeInput = document.getElementById(
        'sariFacilityLatitude'
    );

    const longitudeInput = document.getElementById(
        'sariFacilityLongitude'
    );

    const mapStatus = document.getElementById(
        'sariFacilityMapStatus'
    );

    if (
        mapNode &&
        latitudeInput &&
        longitudeInput &&
        window.maplibregl
    ) {

        const PH_BOUNDS = [
            [116.0, 4.0],
            [127.5, 22.5]
        ];

        const initialLat = Number(latitudeInput.value);
        const initialLng = Number(longitudeInput.value);

        const hasInitial =
            latitudeInput.value !== '' &&
            longitudeInput.value !== '' &&
            Number.isFinite(initialLat) &&
            Number.isFinite(initialLng) &&
            initialLat >= 4 &&
            initialLat <= 22.5 &&
            initialLng >= 116 &&
            initialLng <= 127.5;

        const mapStyle = {

            version: 8,

            sources: {

                'osm-streets': {

                    type: 'raster',

                    tiles: [
                        'https://tile.openstreetmap.org/{z}/{x}/{y}.png'
                    ],

                    tileSize: 256,

                    minzoom: 0,

                    maxzoom: 19,

                    attribution: '© OpenStreetMap contributors'

                }

            },

            layers: [
                {
                    id: 'osm',
                    type: 'raster',
                    source: 'osm-streets'
                }
            ]

        };

        const map = new maplibregl.Map({

            container: mapNode,

            style: mapStyle,

            center: hasInitial
                ? [initialLng, initialLat]
                : [121.0, 12.4],

            zoom: hasInitial ? 13 : 5.3,

            maxBounds: PH_BOUNDS,

            dragRotate: false,

            pitchWithRotate: false,

            attributionControl: true

        });

        map.addControl(
            new maplibregl.NavigationControl({
                showCompass: false
            }),
            'top-right'
        );

        let marker = null;

        function setPin(lng, lat) {

            if (
                !Number.isFinite(lat) ||
                !Number.isFinite(lng) ||
                lat < 4 ||
                lat > 22.5 ||
                lng < 116 ||
                lng > 127.5
            ) {
                return;
            }

            latitudeInput.value = Number(lat).toFixed(7);

            longitudeInput.value = Number(lng).toFixed(7);

            mapStatus.textContent = 'PIN SET';

            mapStatus.classList.remove(
                'bg-[#fff6e5]',
                'text-[#a96e05]'
            );

            mapStatus.classList.add(
                'bg-[#edf8f1]',
                'text-[#327a53]'
            );

            if (!marker) {

                const element = document.createElement('div');

                element.className = 'sari-facility-pin';

                element.textContent = 'H';

                marker = new maplibregl.Marker({
                    element,
                    anchor: 'center'
                })
                    .setLngLat([lng, lat])
                    .addTo(map);

            } else {

                marker.setLngLat([lng, lat]);

            }
        }

        if (hasInitial) {
            setPin(initialLng, initialLat);
        }

        map.on('click', function (event) {

            setPin(
                event.lngLat.lng,
                event.lngLat.lat
            );

        });

        window.setTimeout(function () {
            map.resize();
        }, 150);

    }

    /*
    |--------------------------------------------------------------------------
    | FINAL FORM VALIDATION
    |--------------------------------------------------------------------------
    */

    facilityForm.addEventListener('submit', function (event) {

        operatorName.value = operatorName.value
            .trim()
            .replace(/\s+/gu, ' ');

        operatorEmail.value = operatorEmail.value
            .trim()
            .toLowerCase();

        /*
         * Validate address selection.
         */

        if (manualMode) {

            addressKeys.forEach(function (key) {

                fields[key].name.value =
                    fields[key].search.value.trim();

            });

        }

        for (const key of addressKeys) {

            const field = fields[key];

            const required =
                key !== 'barangay' ||
                field.search.value.trim().length > 0;

            if (
                required &&
                (
                    !field.name.value.trim() ||
                    (!manualMode && !field.code.value)
                )
            ) {

                event.preventDefault();

                setAddressStatus(
                    'Please select a valid ' +
                    key +
                    ' from the address suggestions.',
                    true
                );

                field.search.focus();

                return;
            }
        }

        /*
         * Validate operator.
         */

        const results = [
            [validateName(), operatorName],
            [validateEmail(), operatorEmail],
            [validatePassword(), password],
            [validateConfirmation(), confirmation]
        ];

        const failed = results.find(function (result) {
            return !result[0];
        });

        if (failed) {

            event.preventDefault();

            failed[1].focus();

            failed[1].reportValidity();

            return;
        }

    });

});
</script>

@endpush
