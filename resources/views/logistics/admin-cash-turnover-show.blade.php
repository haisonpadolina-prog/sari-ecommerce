@extends('layouts.logistics')

@section('title', 'Admin Cash Turnover Slip — SARI Logistics')
@section('page-title', 'Turnover Slip / Receipt')

@push('styles')
<style>@media print { #logisticsSidebar, #logisticsHeader, header, nav, .print\:hidden { display: none !important; } #logisticsContent { width:100%!important; margin:0!important; padding:0!important; } }</style>
@endpush

@section('content')
<div class="sari-logistics-ui mx-auto max-w-[1400px]">
    @include('finance.admin-cash-turnover-detail', ['isAdminView' => false])
</div>
@endsection
