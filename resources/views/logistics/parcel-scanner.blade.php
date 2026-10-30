@extends('layouts.logistics')

@section('title', 'Parcel QR Scanner — SARI Logistics')
@section('page-title', 'Parcel QR Scanner')

@section('content')
<div class="sari-logistics-ui sari-logistics-scanner">
    @include('shared.parcel-qr-scanner', [
        'scanEndpoint' => route('logistics.parcel-scanner.scan'),
        'backUrl' => route('logistics.dashboard'),
        'backLabel' => 'Logistics Dashboard',
        'roleLabel' => 'Logistics',
        'description' => 'Use the same printed parcel QR throughout the Logistics chain. The scanner automatically detects whether the parcel is arriving at the origin hub, being loaded for an inter-hub transfer, arriving at the destination hub, or being cleared as Ready for Delivery after local Rider assignment. Every accepted checkpoint is authorized server-side and notifies Buyer, Seller and Logistics.',
    ])
</div>
@endsection
