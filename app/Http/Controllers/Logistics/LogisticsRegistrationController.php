<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;
use App\Services\Registration\RegistrationApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LogisticsRegistrationController extends Controller
{
    public function __construct(private readonly RegistrationApplicationService $registration) {}

    public function create(): View
    {
        return view('pages.register-logistics', ['defaultRole' => 'logistics', 'registrationSubmitRoute' => 'register.logistics.submit']);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        return $this->registration->store($request, 'logistics', [
            'business_name' => ['required', 'string', 'max:180', 'regex:/^[\pL\pN\s.&\'()\-]+$/u'],
            'tin' => ['required', 'regex:/^\d{3}-\d{3}-\d{3}-\d{3}$/'],
            'sec_registration_no' => ['required', 'regex:/^[A-Za-z]{2}\d{9}$/'],
            'postal_code' => ['required', 'regex:/^\d{4}$/'],
            'position' => ['required', 'string', 'max:120'],
            'service_areas' => ['required', 'array', 'min:1'],
            'service_areas.*' => ['required', 'string', 'max:120'],
            'business_permit' => ['required', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'bir_document' => ['required', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'sec_document' => ['required', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }
}
