@extends('layouts.admin')

@section('title', 'Cash Turnover Verification — SARI Admin')
@section('page-title', 'Cash Turnover Verification')

@push('styles')
<style>@media print { aside, header, nav, .print\:hidden { display:none!important; } main { width:100%!important; margin:0!important; padding:0!important; } }</style>
@endpush

@section('content')
<div class="mx-auto max-w-[1400px]">
    @include('finance.admin-cash-turnover-detail', ['isAdminView' => true])
</div>
@endsection
