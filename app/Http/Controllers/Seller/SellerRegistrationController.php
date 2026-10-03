<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;

use App\Services\Registration\RegistrationApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerRegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationApplicationService $registration
    ) {}

    public function create(): View
    {
        return view('pages.register-seller', [
            'defaultRole' => 'seller',
            'registrationSubmitRoute' => 'register.seller.submit',
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        return $this->registration->store($request, 'seller', [
            'business_name' => [
                'required',
                'string',
                'max:180',
                'regex:/^[\pL\pN\s.&\'()\-]+$/u',
            ],
            'business_permit' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
            'line_of_business' => ['required', 'string', 'max:120'],
        ]);
    }
}
