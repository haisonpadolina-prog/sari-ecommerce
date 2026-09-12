<?php

namespace App\Http\Controllers;

use App\Models\LogisticsParcel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogisticsParcelSortingController extends Controller
{
    public function index(): View
    {
        $parcels = LogisticsParcel::query()
            ->with(['order.seller'])
            ->whereIn('status', ['received','sorted'])
            ->latest('received_at')
            ->get();

        return view('logistics.parcel-sorting', compact('parcels'));
    }

    public function sort(Request $request, LogisticsParcel $parcel): RedirectResponse
    {
        abort_unless($parcel->status === 'received', 422, 'Only received parcels can be sorted.');

        $validated = $request->validate([
            'sorting_zone' => ['required','string','max:80'],
            'notes' => ['nullable','string','max:1000'],
        ]);

        $parcel->update([
            'status' => 'sorted',
            'sorting_zone' => $validated['sorting_zone'],
            'notes' => $validated['notes'] ?? $parcel->notes,
            'sorted_at' => now(),
        ]);

        return back()->with('success', 'Parcel sorted and cleared for final delivery.');
    }
}
