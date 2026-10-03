<?php

namespace App\Http\Controllers\Logistics;

use App\Http\Controllers\Controller;

use App\Services\Registration\RegistrationApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsRegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationApplicationService $registration
    ) {}

    public function create(): View
    {
        return view('pages.register-logistics', [
            'defaultRole' => 'logistics',
            'registrationSubmitRoute' => 'register.logistics.submit',
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        return $this->registration->store($request, 'logistics', [
            'business_name' => [
                'required',
                'string',
                'max:180',
                'regex:/^[\pL\pN\s.&\'()\-]+$/u',
            ],
            'business_permit' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
            'profile_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }
}
