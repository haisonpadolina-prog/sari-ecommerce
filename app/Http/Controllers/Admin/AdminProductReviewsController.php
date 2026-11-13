<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminProductReviewQueueService;
use Illuminate\Http\Request;

class AdminProductReviewsController extends Controller
{
    public function index(Request $request, AdminProductReviewQueueService $queue)
    {
        abort_unless($request->session()->get('is_admin'), 403, 'Administrator session required.');

        return view('admin.product-reviews', $queue->load());
    }
}
