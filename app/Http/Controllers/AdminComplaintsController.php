<?php

namespace App\Http\Controllers;

use App\Models\PlatformComplaint;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminComplaintsController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (!$request->session()->get('is_admin')) {
            return redirect()->route('login');
        }

        $complaints = PlatformComplaint::query()->latest()->get();
        $stats = [
            'total' => $complaints->count(),
            'open' => $complaints->where('status','open')->count(),
            'resolved' => $complaints->where('status','resolved')->count(),
        ];

        return view('admin.complaints', compact('complaints','stats'));
    }

    public function resolve(Request $request, PlatformComplaint $complaint): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);
        $validated = $request->validate(['admin_note' => ['nullable','string','max:2000']]);
        $complaint->update([
            'status' => 'resolved',
            'admin_note' => $validated['admin_note'] ?? null,
            'resolved_at' => now(),
        ]);
        return back()->with('success', 'Complaint resolved.');
    }

    public function reopen(Request $request, PlatformComplaint $complaint): RedirectResponse
    {
        abort_unless($request->session()->get('is_admin'), 403);
        $complaint->update(['status' => 'open', 'resolved_at' => null]);
        return back()->with('success', 'Complaint reopened.');
    }
}
