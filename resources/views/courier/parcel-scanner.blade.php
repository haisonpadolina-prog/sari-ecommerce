@extends('layouts.courier')

@section('title', 'Parcel QR Scanner — SARI Rider')
@section('page-title', 'Parcel QR Scanner')
@section('header-title', 'Parcel QR Scanner')
@section('header-subtitle', 'Pickup & Delivery Handoffs')

@push('styles')
    <link
        rel="stylesheet"
        href="{{ asset('css/courier/parcel-scanner.css') }}?v=20261030-1"
    >
@endpush

@section('content')
<div id="riderScanner" class="courier-scanner-page">
    @include('shared.parcel-qr-scanner', [
        'scanEndpoint' => route('courier.parcel-scanner.scan'),
        'backUrl' => route('courier.dashboard'),
        'backLabel' => 'Rider Dashboard',
        'roleLabel' => 'Rider',
        'description' => 'Scan for Seller pickup or assigned hub handoff. At the Buyer, scan only after the parcel is physically received. The final scan completes delivery and records COD as paid.',
    ])
</div>
@endsection