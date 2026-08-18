@extends('layouts.app')

@section('title', 'SARI — Elevated Everyday')

@section('content')

<div class="sari-page" id="sariPage">
    @include('components.landing.navbar')
    @include('components.landing.mobile-menu')

    <main>
        @include('components.landing.hero')
        @include('components.landing.intro')
        @include('components.landing.about')
        @include('components.landing.how-it-works')
        @include('components.landing.categories')
        @include('components.landing.platform')
        @include('components.landing.trust')
        @include('components.landing.cta')
    </main>

    @include('components.landing.footer')
    @include('components.landing.search-overlay')
</div>

@endsection