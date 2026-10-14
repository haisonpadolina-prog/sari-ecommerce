@extends('layouts.buyer')

@section('title', 'Order Tracking — SARI')
@section('page-title', 'Order Tracking')


@section('content')
@include('components.buyer.header')


@php
    $items = (array) $order->items;
    $primaryItem = $items[0] ?? null;
    $primaryItemName = trim((string) ($primaryItem['name'] ?? 'Order item'));
    $primaryItemImage = $primaryItem['image'] ?? null;
    $primaryItemVariant = trim((string) ($primaryItem['variant_label'] ?? 'Standard'));
    $primaryItemQty = max(1, (int) ($primaryItem['qty'] ?? 1));
    $remainingItemCount = max(0, count($items) - 1);

    $sellerSlug = 'seller-' . (int) $order->seller_account_id;

    $statusIndex = match ($order->status) {
        'new' => 1,
        'preparing' => 2,
        'ready_for_pickup' => 3,
        'courier_accepted', 'heading_pickup', 'arrived_pickup' => 4,
        'in_transit' => 5,
        'arrived_buyer' => 6,
        'delivered' => 7,
        default => 0,
    };

    $cancelled = $order->status === 'cancelled';

    $steps = [
        [
            'label' => 'Order placed',
            'description' => 'Your order was successfully submitted to the seller.',
            'time' => $order->created_at,
        ],
        [
            'label' => 'Seller preparing order',
            'description' => 'The seller is packing and preparing your items.',
            'time' => $order->status !== 'new' ? $order->updated_at : null,
        ],
        [
            'label' => 'Ready for pickup',
            'description' => 'Your parcel is packed and waiting for courier pickup.',
            'time' => $order->ready_at,
        ],
        [
            'label' => 'Courier assigned',
            'description' => $order->courier_name
                ? $order->courier_name . ' accepted your delivery.'
                : 'Waiting for a courier to accept the delivery request.',
            'time' => $order->accepted_at,
        ],
        [
            'label' => 'Parcel in transit',
            'description' => 'The courier has picked up your parcel and is heading to you.',
            'time' => $order->picked_up_at,
        ],
        [
            'label' => 'Courier arrived',
            'description' => 'The courier reached your delivery location.',
            'time' => $order->arrived_buyer_at,
        ],
        [
            'label' => 'Delivered',
            'description' => 'Your order has been delivered successfully.',
            'time' => $order->delivered_at,
        ],
    ];

    $progress = $cancelled ? 0 : match ($order->status) {
        'new' => 10,
        'preparing' => 25,
        'ready_for_pickup' => 40,
        'courier_accepted' => 52,
        'heading_pickup' => 58,
        'arrived_pickup' => 66,
        'in_transit' => 78,
        'arrived_buyer' => 92,
        'delivered' => 100,
        default => 0,
    };

    $summarySteps = [
        ['label' => 'Order placed', 'threshold' => 1],
        ['label' => 'Processing', 'threshold' => 2],
        ['label' => 'In transit', 'threshold' => 5],
        ['label' => 'Out for delivery', 'threshold' => 6],
        ['label' => 'Delivered', 'threshold' => 7],
    ];

    $activeSummaryStep = match (true) {
        $statusIndex >= 7 => 5,
        $statusIndex >= 6 => 4,
        $statusIndex >= 5 => 3,
        $statusIndex >= 2 => 2,
        $statusIndex >= 1 => 1,
        default => 0,
    };

    $deliveryHeadline = match ($order->status) {
        'new' => 'Your order has been placed.',
        'preparing' => 'The seller is preparing your order.',
        'ready_for_pickup' => 'Your parcel is ready for pickup.',
        'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'A courier is handling your parcel.',
        'in_transit' => 'Your order is on the way.',
        'arrived_buyer' => 'Your courier has arrived.',
        'delivered' => 'Your order has been delivered.',
        default => 'Track your order progress here.',
    };

    $deliverySubtext = match ($order->status) {
        'new' => 'The seller will start preparing your items shortly.',
        'preparing' => 'Your items are being packed and prepared for dispatch.',
        'ready_for_pickup' => 'Your parcel is waiting for courier pickup.',
        'courier_accepted', 'heading_pickup', 'arrived_pickup' => 'The courier is preparing to move your parcel to the next stage.',
        'in_transit' => 'The courier is currently moving your parcel toward your delivery area.',
        'arrived_buyer' => 'Please be ready to receive your order.',
        'delivered' => 'Thank you for shopping with SARI.',
        default => 'Delivery updates will appear as your order moves through fulfillment.',
    };

    $routeProgress = $cancelled ? 0 : match ($order->status) {
        'new', 'preparing' => 0,
        'ready_for_pickup' => 8,
        'courier_accepted' => 18,
        'heading_pickup' => 28,
        'arrived_pickup' => 38,
        'in_transit' => 68,
        'arrived_buyer' => 92,
        'delivered' => 100,
        default => 0,
    };

    $routeOffset = max(0, 100 - $routeProgress);
@endphp

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

    .sari-track-page {
        --sari-gold: #c98913;
        --sari-gold-dark: #9d6910;
        --sari-gold-soft: #fff7e8;
        --sari-ink: #25211d;
        --sari-text: #514a43;
        --sari-muted: #8d857c;
        --sari-border: #e8e3dc;
        --sari-surface: #ffffff;
        --sari-subtle: #f8f7f5;
        --sari-success: #37805a;
        font-family: 'Poppins', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        color: var(--sari-ink);
    }

    .sari-track-page * {
        box-sizing: border-box;
    }

    .sari-track-shell {
        width: min(100%, 1380px);
        margin: 0 auto;
        padding: 24px 22px 42px;
    }

    .sari-track-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 18px;
    }

    .sari-track-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #716961;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: color .16s ease;
    }

    .sari-track-back:hover {
        color: var(--sari-gold-dark);
    }

    .sari-track-back svg {
        width: 16px;
        height: 16px;
    }

    .sari-track-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 8px;
    }

    .sari-track-action {
        display: inline-flex;
        min-height: 38px;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 1px solid var(--sari-border);
        border-radius: 10px;
        background: #fff;
        padding: 0 13px;
        color: #625b54;
        font-size: 10.5px;
        font-weight: 650;
        text-decoration: none;
        transition: border-color .16s ease, background .16s ease, color .16s ease;
    }

    .sari-track-action:hover {
        border-color: #d9bb80;
        background: #fffbf3;
        color: var(--sari-gold-dark);
    }

    .sari-track-action--primary {
        border-color: #ead4a7;
        background: var(--sari-gold-soft);
        color: var(--sari-gold-dark);
    }

    .sari-track-action svg {
        width: 15px;
        height: 15px;
    }

    .sari-track-hero {
        overflow: hidden;
        border: 1px solid var(--sari-border);
        border-radius: 18px;
        background: var(--sari-surface);
    }

    .sari-track-hero-main {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 28px;
        align-items: center;
        padding: 24px 26px 22px;
    }

    .sari-track-eyebrow {
        margin: 0 0 7px;
        color: var(--sari-gold-dark);
        font-size: 9px;
        line-height: 1;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
    }

    .sari-track-title-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .sari-track-title {
        margin: 0;
        color: var(--sari-ink);
        font-size: clamp(22px, 3vw, 31px);
        line-height: 1.16;
        font-weight: 700;
        letter-spacing: -.035em;
    }

    .sari-track-status {
        display: inline-flex;
        min-height: 26px;
        align-items: center;
        border-radius: 999px;
        background: var(--sari-gold-soft);
        padding: 0 10px;
        color: var(--sari-gold-dark);
        font-size: 8.5px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .sari-track-meta {
        margin: 8px 0 0;
        color: var(--sari-muted);
        font-size: 10.5px;
        line-height: 1.6;
    }

    .sari-track-meta strong {
        color: #5a524a;
        font-weight: 600;
    }

    .sari-track-hero-side {
        display: grid;
        grid-template-columns: repeat(2, minmax(120px, 1fr));
        gap: 0;
        min-width: 300px;
        border: 1px solid var(--sari-border);
        border-radius: 13px;
        overflow: hidden;
        background: #fbfaf8;
    }

    .sari-track-stat {
        padding: 14px 16px;
    }

    .sari-track-stat + .sari-track-stat {
        border-left: 1px solid var(--sari-border);
    }

    .sari-track-stat-label {
        display: block;
        color: var(--sari-muted);
        font-size: 8px;
        font-weight: 650;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sari-track-stat-value {
        display: block;
        margin-top: 5px;
        color: #37322d;
        font-size: 12px;
        font-weight: 700;
    }

    .sari-track-stat-value--gold {
        color: var(--sari-gold-dark);
        font-size: 17px;
    }

    .sari-track-progress-wrap {
        border-top: 1px solid #eee9e3;
        padding: 21px 26px 22px;
    }

    .sari-track-progress {
        position: relative;
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px;
    }

    .sari-track-progress::before,
    .sari-track-progress::after {
        content: '';
        position: absolute;
        top: 13px;
        left: 9%;
        right: 9%;
        height: 2px;
        border-radius: 999px;
    }

    .sari-track-progress::before {
        background: #e8e3dc;
    }

    .sari-track-progress::after {
        right: auto;
        width: calc(max(0, min(100, {{ $progress }})) * .82%);
        max-width: 82%;
        background: var(--sari-gold);
        transition: width .3s ease;
    }

    .sari-track-progress-step {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .sari-track-progress-dot {
        display: grid;
        width: 28px;
        height: 28px;
        margin: 0 auto;
        place-items: center;
        border: 2px solid #ded8d1;
        border-radius: 50%;
        background: #fff;
        color: #a9a098;
        font-size: 9px;
        font-weight: 700;
    }

    .sari-track-progress-step.is-complete .sari-track-progress-dot,
    .sari-track-progress-step.is-current .sari-track-progress-dot {
        border-color: var(--sari-gold);
        background: var(--sari-gold-soft);
        color: var(--sari-gold-dark);
    }

    .sari-track-progress-step.is-current .sari-track-progress-dot {
        box-shadow: 0 0 0 5px rgba(201, 137, 19, .09);
    }

    .sari-track-progress-label {
        margin: 8px 0 0;
        color: #918980;
        font-size: 9px;
        line-height: 1.35;
        font-weight: 600;
    }

    .sari-track-progress-step.is-complete .sari-track-progress-label,
    .sari-track-progress-step.is-current .sari-track-progress-label {
        color: #443d37;
        font-weight: 700;
    }

    .sari-track-current {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        margin-top: 14px;
        border: 1px solid #eee7dc;
        border-radius: 14px;
        background: #fffdf9;
        padding: 14px 16px;
    }

    .sari-track-current-copy {
        min-width: 0;
    }

    .sari-track-current-title {
        margin: 0;
        color: #38322d;
        font-size: 12.5px;
        font-weight: 700;
    }

    .sari-track-current-sub {
        margin: 4px 0 0;
        color: var(--sari-muted);
        font-size: 9.5px;
        line-height: 1.5;
    }

    .sari-track-current-badge {
        flex: 0 0 auto;
        border-radius: 9px;
        background: #f7f3eb;
        padding: 8px 10px;
        color: var(--sari-gold-dark);
        font-size: 9px;
        font-weight: 700;
        white-space: nowrap;
    }

    .sari-track-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(310px, .72fr);
        gap: 16px;
        margin-top: 16px;
    }

    .sari-track-panel {
        border: 1px solid var(--sari-border);
        border-radius: 17px;
        background: #fff;
    }

    .sari-track-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-bottom: 1px solid #eee9e3;
        padding: 17px 19px;
    }

    .sari-track-panel-eyebrow {
        margin: 0;
        color: var(--sari-gold-dark);
        font-size: 8px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .sari-track-panel-title {
        margin: 3px 0 0;
        color: #332e29;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.015em;
    }

    .sari-track-panel-note {
        color: #9a9188;
        font-size: 8.5px;
        font-weight: 500;
    }

    .sari-track-journey {
        display: grid;
        grid-template-columns: minmax(240px, 300px) minmax(0, 1fr);
    }

    .sari-track-timeline {
        border-right: 1px solid #eee9e3;
        padding: 18px 18px 18px 19px;
    }

    .sari-track-event {
        position: relative;
        display: grid;
        grid-template-columns: 22px minmax(0, 1fr);
        gap: 10px;
        padding-bottom: 17px;
    }

    .sari-track-event:last-child {
        padding-bottom: 0;
    }

    .sari-track-event::before {
        content: '';
        position: absolute;
        top: 22px;
        bottom: -1px;
        left: 10px;
        width: 1px;
        background: #e7e2dc;
    }

    .sari-track-event:last-child::before {
        display: none;
    }

    .sari-track-event.is-complete::before {
        background: #d9b56f;
    }

    .sari-track-event-dot {
        position: relative;
        z-index: 2;
        display: grid;
        width: 22px;
        height: 22px;
        place-items: center;
        border: 1px solid #ddd7d0;
        border-radius: 50%;
        background: #fff;
        color: #aaa29a;
        font-size: 7px;
        font-weight: 700;
    }

    .sari-track-event.is-complete .sari-track-event-dot {
        border-color: #dab468;
        background: #fff8ea;
        color: var(--sari-gold-dark);
    }

    .sari-track-event.is-current .sari-track-event-dot {
        box-shadow: 0 0 0 4px rgba(201, 137, 19, .09);
    }

    .sari-track-event-title {
        margin: 1px 0 0;
        color: #3d3732;
        font-size: 10.5px;
        line-height: 1.4;
        font-weight: 700;
    }

    .sari-track-event:not(.is-complete) .sari-track-event-title {
        color: #9c948c;
    }

    .sari-track-event-time {
        margin: 3px 0 0;
        color: #9b9289;
        font-size: 7.5px;
        font-weight: 500;
    }

    .sari-track-event-copy {
        margin: 4px 0 0;
        color: #887f76;
        font-size: 8.5px;
        line-height: 1.5;
    }

    .sari-track-map-wrap {
        padding: 15px;
    }

    .sari-track-map {
        position: relative;
        min-height: 385px;
        overflow: hidden;
        border: 1px solid #e7e2dc;
        border-radius: 14px;
        background: #f6f5f2;
    }

    .sari-track-map-bar {
        position: absolute;
        z-index: 3;
        top: 13px;
        left: 13px;
        right: 13px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border: 1px solid rgba(255,255,255,.9);
        border-radius: 11px;
        background: rgba(255,255,255,.94);
        padding: 10px 11px;
        box-shadow: 0 8px 20px rgba(45,39,33,.05);
        backdrop-filter: blur(6px);
    }

    .sari-track-map-title {
        margin: 0;
        color: #3a3530;
        font-size: 9.5px;
        font-weight: 700;
    }

    .sari-track-map-sub {
        margin: 2px 0 0;
        color: #948b82;
        font-size: 7.5px;
    }

    .sari-track-map-progress {
        flex: 0 0 auto;
        border-radius: 8px;
        background: var(--sari-gold-soft);
        padding: 6px 8px;
        color: var(--sari-gold-dark);
        font-size: 8px;
        font-weight: 700;
    }

    .sari-track-map svg {
        display: block;
        width: 100%;
        height: 385px;
    }

    .sari-track-route-progress {
        stroke-dasharray: 100;
        stroke-dashoffset: var(--route-offset, 100);
        transition: stroke-dashoffset .6s ease;
    }

    .sari-track-map-footer {
        position: absolute;
        z-index: 3;
        left: 13px;
        right: 13px;
        bottom: 13px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        border: 1px solid rgba(255,255,255,.9);
        border-radius: 11px;
        background: rgba(255,255,255,.95);
        padding: 10px 11px;
        box-shadow: 0 8px 20px rgba(45,39,33,.05);
        backdrop-filter: blur(6px);
    }

    .sari-track-courier-name {
        margin: 0;
        color: #3f3933;
        font-size: 9.5px;
        font-weight: 700;
    }

    .sari-track-courier-note {
        margin: 2px 0 0;
        color: #958b82;
        font-size: 7.5px;
        line-height: 1.4;
    }

    .sari-track-side {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: 16px;
    }

    .sari-track-summary {
        padding: 18px;
    }

    .sari-track-summary-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 15px;
    }

    .sari-track-summary-title {
        margin: 0;
        color: #342f2a;
        font-size: 14px;
        font-weight: 700;
    }

    .sari-track-chip {
        display: inline-flex;
        min-height: 23px;
        align-items: center;
        border-radius: 999px;
        background: #f7f5f1;
        padding: 0 8px;
        color: #81786f;
        font-size: 7.5px;
        font-weight: 650;
    }

    .sari-track-summary-list {
        margin: 0;
    }

    .sari-track-summary-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        padding: 8px 0;
    }

    .sari-track-summary-row dt {
        color: #91887f;
        font-size: 8.5px;
        line-height: 1.45;
    }

    .sari-track-summary-row dd {
        margin: 0;
        color: #48413a;
        font-size: 8.5px;
        line-height: 1.45;
        font-weight: 650;
        text-align: right;
    }

    .sari-track-summary-address {
        margin-top: 11px;
        border-top: 1px solid #eee9e3;
        padding-top: 13px;
    }

    .sari-track-summary-address-label {
        color: #91887f;
        font-size: 8.5px;
    }

    .sari-track-summary-address-name {
        margin: 5px 0 0;
        color: #433d37;
        font-size: 9.5px;
        font-weight: 700;
    }

    .sari-track-summary-address-copy {
        margin: 3px 0 0;
        color: #837a72;
        font-size: 8px;
        line-height: 1.55;
    }

    .sari-track-totals {
        margin-top: 13px;
        border-top: 1px solid #eee9e3;
        padding-top: 11px;
    }

    .sari-track-total-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 4px 0;
    }

    .sari-track-total-row span:first-child {
        color: #8f867d;
        font-size: 8.5px;
    }

    .sari-track-total-row span:last-child {
        color: #45403a;
        font-size: 9px;
        font-weight: 650;
    }

    .sari-track-total-row--grand {
        margin-top: 8px;
        border-top: 1px solid #eee9e3;
        padding-top: 11px;
    }

    .sari-track-total-row--grand span:first-child {
        color: #38332e;
        font-size: 10px;
        font-weight: 700;
    }

    .sari-track-total-row--grand span:last-child {
        color: var(--sari-gold-dark);
        font-size: 17px;
        font-weight: 700;
        letter-spacing: -.02em;
    }

    .sari-track-courier {
        padding: 17px 18px;
    }

    .sari-track-courier-head {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .sari-track-courier-icon {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        place-items: center;
        border-radius: 9px;
        background: var(--sari-gold-soft);
        color: var(--sari-gold-dark);
    }

    .sari-track-courier-icon svg {
        width: 16px;
        height: 16px;
    }

    .sari-track-courier-label {
        margin: 0;
        color: #91887f;
        font-size: 7.5px;
        font-weight: 650;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sari-track-courier-value {
        margin: 3px 0 0;
        color: #39342f;
        font-size: 10.5px;
        font-weight: 700;
    }

    .sari-track-courier-contact {
        margin-top: 12px;
        border-top: 1px solid #eee9e3;
        padding-top: 11px;
        color: #7f776f;
        font-size: 8px;
        line-height: 1.5;
    }

    .sari-track-items {
        margin-top: 16px;
    }

    .sari-track-items-body {
        padding: 0 18px;
    }

    .sari-track-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 13px 0;
    }

    .sari-track-item + .sari-track-item {
        border-top: 1px solid #eee9e3;
    }

    .sari-track-item-main {
        display: flex;
        min-width: 0;
        align-items: center;
        gap: 11px;
    }

    .sari-track-item-image {
        display: grid;
        width: 46px;
        height: 46px;
        flex: 0 0 46px;
        overflow: hidden;
        place-items: center;
        border: 1px solid #e9e4de;
        border-radius: 10px;
        background: #f8f7f5;
        color: #aaa29a;
    }

    .sari-track-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .sari-track-item-image svg {
        width: 18px;
        height: 18px;
    }

    .sari-track-item-name {
        margin: 0;
        overflow: hidden;
        color: #413b35;
        font-size: 10px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-track-item-meta {
        margin: 4px 0 0;
        color: #948b83;
        font-size: 8px;
    }

    .sari-track-item-price {
        flex: 0 0 auto;
        color: #37322d;
        font-size: 10.5px;
        font-weight: 700;
    }

    .sari-track-reminder {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 16px;
        border: 1px solid #eadfc9;
        border-radius: 14px;
        background: #fffaf1;
        padding: 13px 14px;
    }

    .sari-track-reminder svg {
        width: 16px;
        height: 16px;
        flex: 0 0 16px;
        margin-top: 1px;
        color: var(--sari-gold-dark);
    }

    .sari-track-reminder strong {
        display: block;
        color: #61513a;
        font-size: 9.5px;
    }

    .sari-track-reminder p {
        margin: 3px 0 0;
        color: #8a7657;
        font-size: 8.5px;
        line-height: 1.5;
    }

    .sari-track-cancelled {
        border: 1px solid #efd1d1;
        border-radius: 17px;
        background: #fffafa;
        padding: 18px;
    }

    .sari-track-cancelled-title {
        margin: 0;
        color: #944848;
        font-size: 14px;
        font-weight: 700;
    }

    .sari-track-cancelled-copy {
        margin: 5px 0 0;
        color: #946f6f;
        font-size: 9.5px;
        line-height: 1.55;
    }

    .sari-track-cancelled-time {
        margin: 8px 0 0;
        color: #a98c8c;
        font-size: 8px;
    }

    @media (max-width: 1100px) {
        .sari-track-grid {
            grid-template-columns: 1fr;
        }

        .sari-track-side {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, .8fr);
        }
    }

    @media (max-width: 840px) {
        .sari-track-shell {
            padding: 18px 14px 32px;
        }

        .sari-track-topbar {
            align-items: flex-start;
            flex-direction: column;
        }

        .sari-track-actions {
            width: 100%;
            justify-content: flex-start;
        }

        .sari-track-hero-main {
            grid-template-columns: 1fr;
            gap: 18px;
            padding: 20px;
        }

        .sari-track-hero-side {
            min-width: 0;
            width: 100%;
        }

        .sari-track-progress-wrap {
            padding: 18px 20px 19px;
        }

        .sari-track-progress {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .sari-track-progress::before,
        .sari-track-progress::after {
            display: none;
        }

        .sari-track-progress-step {
            display: grid;
            grid-template-columns: 28px minmax(0, 1fr);
            gap: 10px;
            align-items: center;
            padding: 6px 0;
            text-align: left;
        }

        .sari-track-progress-dot {
            margin: 0;
        }

        .sari-track-progress-label {
            margin: 0;
            font-size: 9.5px;
        }

        .sari-track-journey {
            grid-template-columns: 1fr;
        }

        .sari-track-timeline {
            border-right: 0;
            border-bottom: 1px solid #eee9e3;
        }

        .sari-track-side {
            display: flex;
        }
    }

    @media (max-width: 560px) {
        .sari-track-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .sari-track-action {
            width: 100%;
            padding-inline: 10px;
        }

        .sari-track-title {
            font-size: 24px;
        }

        .sari-track-hero-side {
            grid-template-columns: 1fr;
        }

        .sari-track-stat + .sari-track-stat {
            border-top: 1px solid var(--sari-border);
            border-left: 0;
        }

        .sari-track-current {
            align-items: flex-start;
            flex-direction: column;
        }

        .sari-track-map {
            min-height: 330px;
        }

        .sari-track-map svg {
            height: 330px;
        }

        .sari-track-map-bar,
        .sari-track-map-footer {
            left: 9px;
            right: 9px;
        }

        .sari-track-item {
            align-items: flex-start;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sari-track-route-progress,
        .sari-track-progress::after {
            transition: none !important;
        }
    }

    .sari-track-product-hero {
        display: grid;
        grid-template-columns: 92px minmax(0, 1fr) minmax(220px, .58fr);
        gap: 18px;
        align-items: center;
        border: 1px solid var(--sari-border);
        border-radius: 18px;
        background: #fff;
        padding: 16px 18px;
    }

    .sari-track-product-media {
        display: grid;
        width: 92px;
        height: 92px;
        place-items: center;
        overflow: hidden;
        border: 1px solid #e8e3dc;
        border-radius: 14px;
        background: #f8f7f5;
        color: #aaa29a;
    }

    .sari-track-product-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .sari-track-product-media svg {
        width: 25px;
        height: 25px;
    }

    .sari-track-product-copy {
        min-width: 0;
    }

    .sari-track-product-topline {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .sari-track-product-label {
        color: #9a9188;
        font-size: 8.5px;
        font-weight: 650;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sari-track-product-name {
        margin: 8px 0 0;
        overflow: hidden;
        color: #2f2a26;
        font-size: clamp(20px, 2.3vw, 27px);
        line-height: 1.2;
        font-weight: 700;
        letter-spacing: -.03em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sari-track-product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin: 7px 0 0;
        color: #837b73;
        font-size: 9.5px;
        line-height: 1.5;
    }

    .sari-track-product-order-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 7px;
        margin-top: 9px;
        color: #9a9188;
        font-size: 8.5px;
        font-weight: 500;
    }

    .sari-track-product-order-dot {
        color: #c9c2ba;
    }

    .sari-track-product-side {
        min-width: 0;
        border-left: 1px solid #eee9e3;
        padding-left: 18px;
    }

    .sari-track-product-side-label {
        display: block;
        color: #978e85;
        font-size: 7.5px;
        font-weight: 650;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .sari-track-product-side strong {
        display: block;
        margin-top: 6px;
        color: #3c3631;
        font-size: 11.5px;
        line-height: 1.45;
        font-weight: 700;
    }

    .sari-track-product-side small {
        display: block;
        margin-top: 4px;
        color: #8c837a;
        font-size: 8.5px;
        line-height: 1.5;
    }

    @media (max-width: 820px) {
        .sari-track-product-hero {
            grid-template-columns: 76px minmax(0, 1fr);
            gap: 14px;
            padding: 14px;
        }

        .sari-track-product-media {
            width: 76px;
            height: 76px;
            border-radius: 12px;
        }

        .sari-track-product-side {
            grid-column: 1 / -1;
            border-top: 1px solid #eee9e3;
            border-left: 0;
            padding-top: 12px;
            padding-left: 0;
        }
    }

    @media (max-width: 520px) {
        .sari-track-product-hero {
            grid-template-columns: 64px minmax(0, 1fr);
        }

        .sari-track-product-media {
            width: 64px;
            height: 64px;
        }

        .sari-track-product-name {
            font-size: 18px;
        }
    }



    /* ============================================================
       DELIVERY JOURNEY — SARI GOLD PROGRESS PALETTE
    ============================================================ */
    .sari-track-timeline {
        background: #ffffff;
    }

    .sari-track-event::before {
        background: #eadfc9 !important;
    }

    .sari-track-event.is-complete::before {
        background: #c98913 !important;
    }

    .sari-track-event-dot {
        border-color: #e2dbd1 !important;
        background: #ffffff !important;
        color: #a69c91 !important;
        box-shadow: none !important;
    }

    .sari-track-event.is-complete .sari-track-event-dot {
        border-color: #c98913 !important;
        background: #c98913 !important;
        color: #ffffff !important;
    }

    .sari-track-event.is-current .sari-track-event-dot {
        border-color: #b87905 !important;
        background: #b87905 !important;
        color: #ffffff !important;
        box-shadow: 0 0 0 5px rgba(201, 135, 17, .10) !important;
    }

    .sari-track-event.is-complete .sari-track-event-title {
        color: #453821 !important;
    }

    .sari-track-event.is-current .sari-track-event-title {
        color: #8d5d08 !important;
    }

    .sari-track-event:not(.is-complete) .sari-track-event-title {
        color: #9c948c !important;
    }

    .sari-track-event.is-complete .sari-track-event-time {
        color: #b17a18 !important;
        font-weight: 600 !important;
    }

    .sari-track-event.is-current .sari-track-event-time {
        color: #9b6509 !important;
        font-weight: 700 !important;
    }

    .sari-track-event.is-complete .sari-track-event-copy {
        color: #76664d !important;
    }

    .sari-track-event.is-current .sari-track-event-copy {
        color: #72582d !important;
    }

    .sari-track-event.is-current {
        margin: 2px -8px 14px;
        padding: 10px 8px 18px;
        border: 1px solid #eadfcd;
        border-radius: 12px;
        background: #ffffff;
    }

    .sari-track-event.is-current::before {
        left: 18px !important;
        top: 32px !important;
    }

    @media (max-width: 840px) {
        .sari-track-event.is-current {
            margin-inline: 0;
        }
    }


    /* Real interactive delivery map */
    .sari-track-map {
        position: relative;
        min-height: 430px;
        overflow: hidden;
        background: #eef1ed;
    }

    #sariBuyerDeliveryMap {
        width: 100%;
        height: 430px;
    }

    /* ============================================================
       MAPLIBRE VECTOR MAP — avoids raster <img> tile conflicts
       ============================================================ */
    #sariBuyerDeliveryMap {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        background: #eef1ed;
    }

    #sariBuyerDeliveryMap .maplibregl-canvas {
        outline: none;
    }

    #sariBuyerDeliveryMap .maplibregl-ctrl-top-right {
        top: 58px;
        right: 10px;
    }

    #sariBuyerDeliveryMap .maplibregl-ctrl-group {
        overflow: hidden;
        border: 1px solid #e2dbd2;
        border-radius: 10px;
        background: rgba(255,255,255,.96);
        box-shadow: 0 6px 18px rgba(48,40,31,.08);
    }

    #sariBuyerDeliveryMap .maplibregl-ctrl-group button {
        width: 31px;
        height: 31px;
    }

    #sariBuyerDeliveryMap .maplibregl-ctrl-attrib {
        border-radius: 7px 0 0 0;
        background: rgba(255,255,255,.84);
        color: #7e766e;
        font-size: 7px;
    }

    #sariBuyerDeliveryMap .maplibregl-ctrl-attrib a {
        color: #996813;
    }

    .sari-buyer-map-marker {
        display: grid;
        width: 31px;
        height: 31px;
        place-items: center;
        border: 3px solid #fff;
        border-radius: 999px;
        background: #c98913;
        color: #fff;
        font-size: 8px;
        font-weight: 800;
        box-shadow:
            0 0 0 1px rgba(130,88,15,.18),
            0 6px 16px rgba(45,35,22,.22);
    }

    .sari-buyer-map-marker.is-buyer {
        background: #2f2b27;
    }

    .sari-buyer-map-marker.is-future {
        background: #aaa39b;
    }

    .sari-buyer-map-marker.is-current {
        box-shadow:
            0 0 0 6px rgba(201,137,19,.14),
            0 6px 16px rgba(45,35,22,.22);
    }

    .sari-map-route-notice {
        z-index: 12;
    }

    .sari-track-map-bar,
    .sari-track-map-footer {
        z-index: 14;
    }



    .sari-map-loading {
        position: absolute;
        z-index: 13;
        right: 14px;
        bottom: 68px;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: 1px solid rgba(230,223,214,.96);
        border-radius: 999px;
        background: rgba(255,255,255,.94);
        padding: 7px 10px;
        color: #837970;
        font-size: 7.5px;
        font-weight: 650;
        box-shadow: 0 6px 18px rgba(44,37,30,.07);
        backdrop-filter: blur(6px);
        pointer-events: none;
        transition: opacity .2s ease, visibility .2s ease;
    }

    .sari-map-loading::before {
        content: '';
        width: 8px;
        height: 8px;
        border: 2px solid #ead7b1;
        border-top-color: #c98913;
        border-radius: 50%;
        animation: sariMapSpin .75s linear infinite;
    }

    .sari-map-loading.is-hidden {
        opacity: 0;
        visibility: hidden;
    }

    @keyframes sariMapSpin {
        to { transform: rotate(360deg); }
    }

</style>

<div class="sari-track-page">
    <div class="sari-track-shell">

        <div class="sari-track-topbar">
            <a href="{{ route('buyer.orders') }}" class="sari-track-back">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" aria-hidden="true">
                    <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
                Back to My Orders
            </a>

            <div class="sari-track-actions">
                <a
                    href="{{ route('buyer.messages', ['seller' => $order->seller_account_id]) }}"
                    class="sari-track-action sari-track-action--primary"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M4 6h16v11H8l-4 3V6Z" stroke-linejoin="round"></path>
                    </svg>
                    Contact seller
                </a>

                <a href="{{ route('buyer.shop', ['shop' => $sellerSlug]) }}" class="sari-track-action">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M4 10h16v10H4z"></path>
                        <path d="M3 10 5 4h14l2 6"></path>
                    </svg>
                    View store
                </a>

                <a href="{{ route('buyer.shipping') }}" class="sari-track-action">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M3 7h11v9H3z"></path>
                        <path d="M14 10h4l3 3v3h-7z"></path>
                    </svg>
                    Shipping center
                </a>
            </div>
        </div>

        @if ($cancelled)
            <section class="sari-track-cancelled">
                <p class="sari-track-eyebrow">Order update</p>
                <h1 class="sari-track-cancelled-title">Order cancelled</h1>
                <p class="sari-track-cancelled-copy">
                    {{ $order->cancellation_reason ?: 'This order was cancelled.' }}
                </p>
                @if ($order->cancelled_at)
                    <p class="sari-track-cancelled-time">
                        {{ $order->cancelled_at->format('M d, Y · h:i A') }}
                    </p>
                @endif
            </section>
        @else
            <section class="sari-track-product-hero">
                <div class="sari-track-product-media">
                    @if ($primaryItemImage)
                        <img
                            src="{{ $primaryItemImage }}"
                            alt="{{ $primaryItemName }}"
                            loading="eager"
                            decoding="async"
                        >
                    @else
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                            <path d="M4 7.5L12 3l8 4.5v9L12 21l-8-4.5v-9z" stroke-linejoin="round"></path>
                            <path d="M4.5 7.7 12 12l7.5-4.3M12 12v9" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    @endif
                </div>

                <div class="sari-track-product-copy">
                    <div class="sari-track-product-topline">
                        <span class="sari-track-product-label">Tracking this order</span>
                        <span class="sari-track-status">{{ $order->statusLabel() }}</span>
                    </div>

                    <h1 class="sari-track-product-name">{{ $primaryItemName }}</h1>

                    <p class="sari-track-product-meta">
                        {{ $primaryItemVariant }}
                        <span>·</span>
                        Qty {{ $primaryItemQty }}
                        @if ($remainingItemCount > 0)
                            <span>·</span>
                            +{{ $remainingItemCount }} more {{ $remainingItemCount === 1 ? 'item' : 'items' }}
                        @endif
                    </p>

                    <div class="sari-track-product-order-meta">
                        <span>{{ $order->seller?->store_name ?: 'SARI Seller' }}</span>
                        <span class="sari-track-product-order-dot">•</span>
                        <span>{{ $order->order_number }}</span>
                    </div>
                </div>

                <div class="sari-track-product-side">
                    <span class="sari-track-product-side-label">Current status</span>
                    <strong>{{ $deliveryHeadline }}</strong>
                    <small>{{ $deliverySubtext }}</small>
                </div>
            </section>

            <div class="sari-track-grid">
                <section class="sari-track-panel">
                    <div class="sari-track-panel-head">
                        <div>
                            <p class="sari-track-panel-eyebrow">Shipment progress</p>
                            <h2 class="sari-track-panel-title">Delivery journey</h2>
                        </div>
                        <span class="sari-track-panel-note">Status-based preview</span>
                    </div>

                    <div class="sari-track-journey">
                        <div class="sari-track-timeline">
                            @foreach ($steps as $index => $step)
                                @php
                                    $stepNumber = $index + 1;
                                    $complete = $statusIndex >= $stepNumber;
                                    $current = $statusIndex === $stepNumber && $order->status !== 'delivered';
                                @endphp

                                <div class="sari-track-event {{ $complete ? 'is-complete' : '' }} {{ $current ? 'is-current' : '' }}">
                                    <span class="sari-track-event-dot">
                                        @if ($complete && !$current)
                                            <svg viewBox="0 0 24 24" width="11" height="11" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                                                <path d="m6 12 4 4 8-9" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        @else
                                            {{ $stepNumber }}
                                        @endif
                                    </span>

                                    <div>
                                        <p class="sari-track-event-title">{{ $step['label'] }}</p>

                                        @if ($step['time'])
                                            <p class="sari-track-event-time">
                                                {{ $step['time']->format('M d, Y · h:i A') }}
                                            </p>
                                        @endif

                                        <p class="sari-track-event-copy">{{ $step['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="sari-track-map-wrap">
                            <div
                                class="sari-track-map"
                                data-sari-delivery-route="{{ json_encode($trackingRoute, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
                            >
                                <div class="sari-track-map-bar">
                                    <div>
                                        <p class="sari-track-map-title">Delivery route</p>
                                        <p class="sari-track-map-sub">
                                            Seller → Origin facility → Destination facility → Buyer
                                        </p>
                                    </div>
                                    <span class="sari-track-map-progress">
                                        {{ (int) ($trackingRoute['progress'] ?? 0) }}%
                                    </span>
                                </div>

                                <div
                                    id="sariBuyerDeliveryMap"
                                    data-sari-maplibre-map
                                    aria-label="Interactive delivery route map"
                                ></div>
                                <span class="sari-map-loading" data-sari-map-loading>
                                    Loading street map
                                </span>

                                @if (empty($trackingRoute['ready']))
                                    <div class="sari-map-route-notice" data-sari-route-notice>
                                        <span class="sari-map-route-notice-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M12 21s7-6.2 7-12A7 7 0 1 0 5 9c0 5.8 7 12 7 12Z"></path>
                                                <circle cx="12" cy="9" r="2.4"></circle>
                                            </svg>
                                        </span>

                                        <span>
                                            <strong>
                                                {{ $order->status === 'delivered'
                                                    ? 'Route history is not available for this order'
                                                    : 'Delivery route is being prepared' }}
                                            </strong>
                                            <small>
                                                {{ $order->status === 'delivered'
                                                    ? 'This order was completed before its five map coordinates were saved. The real map remains available, and any saved route points will appear automatically.'
                                                    : 'SARI Logistics is resolving the Seller, nearest origin facility, nearest destination facility, and Buyer coordinates. Saved stops will appear here automatically.' }}
                                            </small>
                                        </span>
                                    </div>
                                @endif

                                <div class="sari-track-map-footer">
                                    <div>
                                        <p class="sari-track-courier-name">
                                            {{ $order->courier_name ?: 'Courier not yet assigned' }}
                                        </p>
                                        <p class="sari-track-courier-note">
                                            Real route stops · progress follows Logistics updates
                                        </p>
                                    </div>
                                    <span class="sari-track-status">{{ $order->statusLabel() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <aside class="sari-track-side">
                    <section class="sari-track-panel sari-track-summary">
                        <div class="sari-track-summary-head">
                            <h2 class="sari-track-summary-title">Order summary</h2>
                            <span class="sari-track-chip">
                                {{ count($items) }} {{ count($items) === 1 ? 'item' : 'items' }}
                            </span>
                        </div>

                        <dl class="sari-track-summary-list">
                            <div class="sari-track-summary-row">
                                <dt>Order ID</dt>
                                <dd>{{ $order->order_number }}</dd>
                            </div>

                            <div class="sari-track-summary-row">
                                <dt>Payment method</dt>
                                <dd>{{ $order->payment_method }}</dd>
                            </div>

                            <div class="sari-track-summary-row">
                                <dt>Payment status</dt>
                                <dd>{{ ucfirst($order->payment_status) }}</dd>
                            </div>
                        </dl>

                        <div class="sari-track-summary-address">
                            <span class="sari-track-summary-address-label">Shipping address</span>
                            <p class="sari-track-summary-address-name">{{ $order->buyer_name }}</p>
                            <p class="sari-track-summary-address-copy">{{ $order->buyer_address }}</p>
                            @if ($order->buyer_phone)
                                <p class="sari-track-summary-address-copy">{{ $order->buyer_phone }}</p>
                            @endif
                        </div>

                        <div class="sari-track-totals">
                            <div class="sari-track-total-row">
                                <span>Subtotal</span>
                                <span>₱{{ number_format((float) $order->subtotal, 2) }}</span>
                            </div>

                            <div class="sari-track-total-row">
                                <span>Delivery fee</span>
                                <span>₱{{ number_format((float) $order->delivery_fee, 2) }}</span>
                            </div>

                            <div class="sari-track-total-row sari-track-total-row--grand">
                                <span>Total amount</span>
                                <span>₱{{ number_format((float) $order->total, 2) }}</span>
                            </div>
                        </div>
                    </section>

                    <section class="sari-track-panel sari-track-courier">
                        <div class="sari-track-courier-head">
                            <span class="sari-track-courier-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                    <path d="M3 7h11v9H3z"></path>
                                    <path d="M14 10h4l3 3v3h-7z"></path>
                                    <circle cx="7" cy="18" r="1.5"></circle>
                                    <circle cx="18" cy="18" r="1.5"></circle>
                                </svg>
                            </span>

                            <div>
                                <p class="sari-track-courier-label">Courier details</p>
                                <p class="sari-track-courier-value">
                                    {{ $order->courier_name ?: 'Waiting for assignment' }}
                                </p>
                            </div>
                        </div>

                        @if ($order->courier_email)
                            <div class="sari-track-courier-contact">
                                {{ $order->courier_email }}
                            </div>
                        @endif
                    </section>
                </aside>
            </div>
        @endif

            </div>
</div>
@endsection

@push('scripts')
    @vite('resources/js/buyer-orders.js')

    <link
        rel="stylesheet"
        href="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.css"
    >

    <script src="https://unpkg.com/maplibre-gl@5.7.1/dist/maplibre-gl.js"></script>

    <script>
    (() => {
        const MAP_STYLE = {
            version: 8,
            name: 'SARI Street Map',
            sources: {
                'osm-streets': {
                    type: 'raster',
                    tiles: [
                        'https://tile.openstreetmap.org/{z}/{x}/{y}.png'
                    ],
                    tileSize: 256,
                    minzoom: 0,
                    maxzoom: 19,
                    attribution: '© OpenStreetMap contributors',
                },
            },
            layers: [
                {
                    id: 'sari-osm-background',
                    type: 'raster',
                    source: 'osm-streets',
                    minzoom: 0,
                    maxzoom: 19,
                    paint: {
                        'raster-opacity': 1,
                        'raster-saturation': -0.12,
                        'raster-contrast': 0.04,
                    },
                },
            ],
        };

        const PHILIPPINES_CENTER = [121.7740, 12.8797];

        let animationTimer = null;
        let activeMap = null;

        const parseRoute = (wrapper) => {
            try {
                return JSON.parse(wrapper?.dataset?.sariDeliveryRoute || '{}');
            } catch (_) {
                return {};
            }
        };

        const validStops = (route) => {
            return Array.isArray(route?.stops)
                ? route.stops.filter((stop) => {
                    const lat = Number(stop?.lat);
                    const lng = Number(stop?.lng);

                    return Number.isFinite(lat)
                        && Number.isFinite(lng)
                        && lat >= 4.0
                        && lat <= 22.5
                        && lng >= 116.0
                        && lng <= 127.5;
                })
                : [];
        };

        const stopLabel = (stop) => {
            if (stop.type === 'seller') return 'S';
            if (stop.type === 'buyer') return 'B';
            if (stop.key === 'origin_facility') return 'O';
            if (stop.key === 'destination_facility') return 'D';

            return String(stop.sequence || '');
        };

        const markerElement = (stop, route) => {
            const el = document.createElement('div');
            el.className = 'sari-buyer-map-marker';
            el.textContent = stopLabel(stop);

            if (stop.type === 'buyer') {
                el.classList.add('is-buyer');
            }

            if (
                !stop.reached
                && Number(stop.sequence) > Number(route?.current_sequence || 1)
            ) {
                el.classList.add('is-future');
            }

            if (Number(stop.sequence) === Number(route?.current_sequence || 0)) {
                el.classList.add('is-current');
            }

            return el;
        };

        const lineFeature = (coordinates) => ({
            type: 'Feature',
            properties: {},
            geometry: {
                type: 'LineString',
                coordinates,
            },
        });

        const addLine = (
            map,
            id,
            coordinates,
            {
                color,
                width,
                opacity = 1,
                dasharray = null,
            }
        ) => {
            if (!Array.isArray(coordinates) || coordinates.length < 2) {
                return false;
            }

            map.addSource(id, {
                type: 'geojson',
                data: lineFeature(coordinates),
            });

            const paint = {
                'line-color': color,
                'line-width': width,
                'line-opacity': opacity,
            };

            if (dasharray) {
                paint['line-dasharray'] = dasharray;
            }

            map.addLayer({
                id,
                type: 'line',
                source: id,
                layout: {
                    'line-cap': 'round',
                    'line-join': 'round',
                },
                paint,
            });

            return true;
        };

        const startCurrentLegAnimation = (map, layerId) => {
            if (!map.getLayer(layerId)) return;

            const patterns = [
                [0.5, 2.1],
                [0.8, 1.8],
                [1.1, 1.5],
                [1.4, 1.2],
                [1.1, 1.5],
                [0.8, 1.8],
            ];

            let index = 0;

            if (animationTimer) {
                window.clearInterval(animationTimer);
            }

            animationTimer = window.setInterval(() => {
                if (!map.getLayer(layerId)) return;

                index = (index + 1) % patterns.length;
                map.setPaintProperty(
                    layerId,
                    'line-dasharray',
                    patterns[index]
                );
            }, 160);
        };

        const fitRoute = (map, stops) => {
            if (stops.length === 0) {
                map.setCenter(PHILIPPINES_CENTER);
                map.setZoom(5.2);
                return;
            }

            if (stops.length === 1) {
                map.setCenter([
                    Number(stops[0].lng),
                    Number(stops[0].lat),
                ]);
                map.setZoom(13);
                return;
            }

            const bounds = new maplibregl.LngLatBounds();

            stops.forEach((stop) => {
                bounds.extend([
                    Number(stop.lng),
                    Number(stop.lat),
                ]);
            });

            map.fitBounds(bounds, {
                padding: {
                    top: 90,
                    right: 70,
                    bottom: 90,
                    left: 70,
                },
                maxZoom: 14,
                duration: 0,
            });
        };

        const buildRouteLayers = (map, route, stops) => {
            const coordinates = stops.map((stop) => [
                Number(stop.lng),
                Number(stop.lat),
            ]);

            if (coordinates.length < 2) return;

            addLine(map, 'sari-route-full', coordinates, {
                color: '#d5d0c9',
                width: 5,
                opacity: .92,
            });

            const lastReached = Math.max(
                0,
                Math.min(
                    stops.length,
                    Number(route?.last_reached_sequence || 0)
                )
            );

            const completedStops = stops.filter(
                (stop) => Number(stop.sequence) <= lastReached
            );

            const completedCoordinates = completedStops.map((stop) => [
                Number(stop.lng),
                Number(stop.lat),
            ]);

            addLine(map, 'sari-route-completed', completedCoordinates, {
                color: '#c98913',
                width: 5.5,
                opacity: 1,
            });

            const currentStop = stops.find(
                (stop) => Number(stop.sequence) === lastReached
            );

            const nextStop = stops.find(
                (stop) => Number(stop.sequence) === lastReached + 1
            );

            const currentLegStart = currentStop || stops.find(
                (stop) => Number(stop.sequence) === 1
            );

            const currentLegEnd = nextStop || (
                lastReached === 0
                    ? stops.find((stop) => Number(stop.sequence) === 2)
                    : null
            );

            if (currentLegStart && currentLegEnd) {
                const added = addLine(
                    map,
                    'sari-route-current',
                    [
                        [
                            Number(currentLegStart.lng),
                            Number(currentLegStart.lat),
                        ],
                        [
                            Number(currentLegEnd.lng),
                            Number(currentLegEnd.lat),
                        ],
                    ],
                    {
                        color: '#d89513',
                        width: 5.5,
                        opacity: 1,
                        dasharray: [0.8, 1.8],
                    }
                );

                if (added) {
                    startCurrentLegAnimation(
                        map,
                        'sari-route-current'
                    );
                }
            }
        };

        const addMarkers = (map, route, stops) => {
            stops.forEach((stop) => {
                const popup = new maplibregl.Popup({
                    offset: 18,
                    closeButton: false,
                    maxWidth: '250px',
                }).setHTML(`
                    <div style="font-family:Poppins,system-ui,sans-serif;padding:2px 1px;">
                        <strong style="display:block;font-size:11px;color:#3c3631;">
                            ${String(stop.name || 'Route stop')}
                        </strong>
                        ${
                            stop.address
                                ? `<span style="display:block;margin-top:3px;font-size:9px;line-height:1.4;color:#81776d;">
                                    ${String(stop.address)}
                                   </span>`
                                : ''
                        }
                    </div>
                `);

                new maplibregl.Marker({
                    element: markerElement(stop, route),
                    anchor: 'center',
                })
                    .setLngLat([
                        Number(stop.lng),
                        Number(stop.lat),
                    ])
                    .setPopup(popup)
                    .addTo(map);
            });
        };

        const updateNotice = (wrapper, title, copy) => {
            const notice = wrapper?.querySelector(
                '[data-sari-route-notice]'
            );

            if (!notice) return;

            const titleNode = notice.querySelector('strong');
            const copyNode = notice.querySelector('small');

            if (titleNode && title) titleNode.textContent = title;
            if (copyNode && copy) copyNode.textContent = copy;
        };

        const destroyMap = () => {
            if (animationTimer) {
                window.clearInterval(animationTimer);
                animationTimer = null;
            }

            if (activeMap) {
                try {
                    activeMap.remove();
                } catch (_) {}
                activeMap = null;
            }

            const mapNode = document.querySelector(
                '[data-sari-maplibre-map]'
            );

            if (mapNode) {
                delete mapNode.dataset.ready;
            }
        };

        const boot = () => {
            const wrapper = document.querySelector(
                '[data-sari-delivery-route]'
            );

            const mapNode = document.querySelector(
                '[data-sari-maplibre-map]'
            );

            if (!wrapper || !mapNode || mapNode.dataset.ready === '1') {
                return;
            }

            if (!window.maplibregl) {
                updateNotice(
                    wrapper,
                    'Map library failed to load',
                    'Refresh the page and check that unpkg.com is allowed by the browser or server network policy.'
                );
                return;
            }

            mapNode.dataset.ready = '1';

            const route = parseRoute(wrapper);
            const stops = validStops(route);

            const map = new maplibregl.Map({
                container: mapNode,
                style: MAP_STYLE,
                center: PHILIPPINES_CENTER,
                zoom: 5.2,
                attributionControl: true,
                interactive: true,
                dragRotate: false,
                pitchWithRotate: false,
            });

            activeMap = map;

            map.addControl(
                new maplibregl.NavigationControl({
                    showCompass: false,
                    visualizePitch: false,
                }),
                'top-right'
            );

            map.on('load', () => {
                wrapper.querySelector('[data-sari-map-loading]')?.classList.add('is-hidden');

                fitRoute(map, stops);
                addMarkers(map, route, stops);
                buildRouteLayers(map, route, stops);

                if (stops.length >= 2 && route?.ready) {
                    wrapper
                        .querySelector('[data-sari-route-notice]')
                        ?.remove();
                }
            });

            map.on('error', (event) => {
                wrapper.querySelector('[data-sari-map-loading]')?.classList.add('is-hidden');

                const message = String(
                    event?.error?.message || ''
                ).toLowerCase();

                if (
                    message.includes('style')
                    || message.includes('source')
                    || message.includes('tile')
                    || message.includes('network')
                    || message.includes('fetch')
                ) {
                    updateNotice(
                        wrapper,
                        'Map background could not load',
                        'The tracking data is safe. Refresh the page or verify that tile.openstreetmap.org is allowed by your browser/server network policy.'
                    );
                }
            });

            window.setTimeout(() => {
                map.resize();
                fitRoute(map, stops);
            }, 180);
        };

        if (document.readyState === 'loading') {
            document.addEventListener(
                'DOMContentLoaded',
                boot,
                { once: true }
            );
        } else {
            boot();
        }

        document.addEventListener(
            'livewire:navigated',
            () => {
                destroyMap();
                boot();
            }
        );
    })();
    </script>
@endpush
