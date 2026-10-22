@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-xl rounded-[24px] border border-[#e8dfd2] bg-white p-6 shadow-sm">
    <h1 class="text-xl font-bold text-[#28231d]">Activate Rider Account</h1>
    <p class="mt-2 text-sm text-[#756c61]">{{ $rider->first_name }}, create your password to activate {{ $rider->email }}.</p>
    <form method="POST" action="{{ route('rider.activation.activate', ['token' => $token]) }}" class="mt-6 space-y-4">
        @csrf
        <div><label class="sari-field-label">Password</label><input type="password" name="password" required autocomplete="new-password" class="sari-control w-full border border-[#e3ddd4]"></div>
        <div><label class="sari-field-label">Confirm Password</label><input type="password" name="password_confirmation" required autocomplete="new-password" class="sari-control w-full border border-[#e3ddd4]"></div>
        @error('password')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
        <button class="sari-register-action sari-register-primary w-full" type="submit">Activate Account</button>
    </form>
</div>
@endsection
