<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;

use App\Services\Registration\RegistrationApplicationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BuyerRegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationApplicationService $registration
    ) {}

    public function create(): View
    {
        return view('pages.register-buyer', [
            'defaultRole' => 'buyer',
            'registrationSubmitRoute' => 'register.buyer.submit',
        ]);
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        return $this->registration->store($request, 'buyer', []);
    }
}
