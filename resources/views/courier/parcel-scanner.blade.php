@extends('layouts.courier')

@section('title', 'Parcel QR Scanner — SARI Rider')
@section('page-title', 'Parcel QR Scanner')

@section('content')
    @include('shared.parcel-qr-scanner', [
        'scanEndpoint' => route('courier.parcel-scanner.scan'),
        'backUrl' => route('courier.dashboard'),
        'backLabel' => 'Rider Dashboard',
        'roleLabel' => 'Rider',
        'description' => 'Use the same parcel QR for Seller pickup, destination-hub Rider handoff and final Buyer delivery. At the hub, scan assigned parcels to take custody and start the route. At the Buyer, scan the parcel again only after the Buyer physically receives it; that final scan completes delivery and records COD as paid.',
    ])
@endsection
