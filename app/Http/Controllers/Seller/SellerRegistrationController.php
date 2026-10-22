<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Registration\RegistrationApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SellerRegistrationController extends Controller
{
    public function __construct(private readonly RegistrationApplicationService $registration) {}

    public function create(): View
    {
        return view('pages.register-seller', ['defaultRole' => 'seller', 'registrationSubmitRoute' => 'register.seller.submit']);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        return $this->registration->store($request, 'seller', [
            'business_name' => ['required', 'string', 'min:3', 'max:30', 'regex:/^[\pL\pN\s.&\'()\-]+$/u'],
            'line_of_business' => ['required', 'string', 'max:120'],
            'seller_type' => ['required', Rule::in(['individual', 'sole_proprietorship', 'corporation'])],
            'tin' => ['required', 'regex:/^\d{3}-\d{3}-\d{3}-\d{3}$/'],
            'postal_code' => ['required', 'regex:/^\d{4}$/'],
            'sec_registration_no' => ['required_if:seller_type,corporation', 'nullable', 'regex:/^[A-Za-z]{2}\d{9}$/'],
            'business_permit' => ['required_if:seller_type,sole_proprietorship', 'nullable', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'bir_document' => ['required_unless:seller_type,individual', 'nullable', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'sec_document' => ['required_if:seller_type,corporation', 'nullable', 'file', 'mimetypes:image/jpeg,image/png,application/pdf', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);
    }
}
