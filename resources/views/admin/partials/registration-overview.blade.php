@php
    $registrationStats = $registrationStats ?? [
        'total' => 0,
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'buyers' => 0,
        'sellers' => 0,
        'couriers' => 0,
        'logistics' => 0,
        'riders' => 0,
    ];

    $recentRegistrationApplications = $recentRegistrationApplications ?? collect();
@endphp

<section class="mb-5 overflow-hidden rounded-[20px] border border-[#e8e1d6] bg-white shadow-[8px_8px_20px_rgba(74,58,38,.045),-5px_-5px_14px_rgba(255,255,255,.9)]">
    <div class="flex flex-col gap-4 border-b border-[#eee8df] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
        <div class="flex items-start gap-3">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl border border-[#eadfc9] bg-[#fff8e9] text-[#b77a18]">
                <svg viewBox="0 0 24 24" class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <circle cx="9" cy="8" r="3"></circle>
                    <path d="M3 20a6 6 0 0 1 12 0"></path>
                    <path d="M16 8h5"></path>
                    <path d="M18.5 5.5v5"></path>
                </svg>
            </div>

            <div>
                <p class="text-[8px] font-bold uppercase tracking-[.12em] text-[#a17732]">Live Registration Queue</p>
                <h2 class="mt-1 text-[15px] font-bold tracking-[-.02em] text-[#2b261f]">New account registrations</h2>
                <p class="mt-1 text-[8px] leading-4 text-[#8c8378]">Buyer, Seller, Courier, Logistics, and Rider submissions are read directly from the registration database.</p>
            </div>
        </div>

        <a href="{{ route('admin.registrations', ['status' => 'pending']) }}" class="inline-flex h-9 items-center justify-center gap-2 rounded-xl bg-[#c99128] px-4 text-[8px] font-bold text-white transition hover:bg-[#b47f20]">
            Review pending
            <span class="rounded-full bg-white/20 px-2 py-0.5">{{ $registrationStats['pending'] }}</span>
        </a>
    </div>

    <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-4 sm:p-5 xl:grid-cols-9">
        @foreach([
            ['Total', $registrationStats['total'], '#657f94', '#f2f6f8'],
            ['Pending', $registrationStats['pending'], '#a8731f', '#fff7e9'],
            ['Approved', $registrationStats['approved'], '#56816a', '#eef7f1'],
            ['Rejected', $registrationStats['rejected'], '#a65d5d', '#fff1f1'],
            ['Buyer', $registrationStats['buyers'], '#587ca0', '#f5f9fc'],
            ['Seller', $registrationStats['sellers'], '#a8731f', '#fff9ef'],
            ['Courier', $registrationStats['couriers'], '#6d7183', '#f5f5f8'],
            ['Logistics', $registrationStats['logistics'], '#715a86', '#f6f2f9'],
            ['Rider', $registrationStats['riders'], '#527b6f', '#f2f8f6'],
        ] as [$label, $value, $text, $background])
            <article class="rounded-[14px] border border-[#eee8df] p-3" style="background: {{ $background }};">
                <p class="text-[7px] font-semibold text-[#8d8479]">{{ $label }}</p>
                <p class="mt-1.5 text-[18px] font-bold" style="color: {{ $text }};">{{ $value }}</p>
                @if(in_array($label, ['Buyer', 'Seller', 'Courier', 'Logistics', 'Rider'], true))
                    <p class="mt-1 text-[6px] uppercase tracking-[.08em] text-[#a39a90]">pending</p>
                @endif
            </article>
        @endforeach
    </div>

    <div class="border-t border-[#eee8df]">
        <div class="flex items-center justify-between px-5 py-3 sm:px-6">
            <div>
                <p class="text-[9px] font-bold text-[#403a33]">Recent applicants</p>
                <p class="mt-0.5 text-[7px] text-[#958c80]">Latest 8 submissions, including approved and rejected records.</p>
            </div>
            <a href="{{ route('admin.registrations') }}" class="text-[8px] font-bold text-[#a8731f] hover:text-[#8e5f17]">View all →</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] border-collapse">
                <thead>
                    <tr class="border-t border-[#f0ebe4] bg-[#fcfbf8] text-left">
                        <th class="px-5 py-2.5 text-[7px] font-bold uppercase tracking-[.08em] text-[#958c80] sm:px-6">Applicant</th>
                        <th class="px-4 py-2.5 text-[7px] font-bold uppercase tracking-[.08em] text-[#958c80]">Role</th>
                        <th class="px-4 py-2.5 text-[7px] font-bold uppercase tracking-[.08em] text-[#958c80]">Status</th>
                        <th class="px-4 py-2.5 text-[7px] font-bold uppercase tracking-[.08em] text-[#958c80]">Submitted</th>
                        <th class="px-5 py-2.5 text-right text-[7px] font-bold uppercase tracking-[.08em] text-[#958c80] sm:px-6">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRegistrationApplications as $application)
                        @php
                            $roleTone = match($application->role) {
                                'buyer' => 'border-[#dae7f2] bg-[#f4f8fc] text-[#537a9f]',
                                'seller' => 'border-[#eee0c5] bg-[#fff8ec] text-[#a8731f]',
                                'courier' => 'border-[#e1e1e8] bg-[#f7f7f9] text-[#686b7a]',
                                'logistics' => 'border-[#e6ddec] bg-[#f7f2fa] text-[#715a86]',
                                'rider' => 'border-[#d9e7e3] bg-[#f2f8f6] text-[#527b6f]',
                                default => 'border-[#e3ded7] bg-[#f7f5f2] text-[#746d64]',
                            };

                            $statusTone = match($application->status) {
                                'approved' => 'border-[#cfe1d5] bg-[#f2f8f4] text-[#4f7c60]',
                                'rejected' => 'border-[#e8cccc] bg-[#fff3f3] text-[#a55a5a]',
                                default => 'border-[#eadfc9] bg-[#fffaf2] text-[#a8731f]',
                            };
                        @endphp
                        <tr class="border-t border-[#f2ede6]">
                            <td class="px-5 py-3 sm:px-6">
                                <p class="text-[8px] font-bold text-[#403a33]">{{ $application->fullName() }}</p>
                                <p class="mt-0.5 text-[7px] text-[#92897e]">{{ $application->email }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[6px] font-bold uppercase {{ $roleTone }}">{{ $application->role }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-1 text-[6px] font-bold uppercase {{ $statusTone }}">{{ $application->status }}</span>
                            </td>
                            <td class="px-4 py-3 text-[7px] text-[#81786c]">{{ $application->created_at?->format('M d, Y g:i A') }}</td>
                            <td class="px-5 py-3 text-right sm:px-6">
                                <a href="{{ route('admin.registrations', ['status' => $application->status, 'role' => $application->role]) }}" class="inline-flex h-8 items-center rounded-lg border border-[#e5ddd2] px-3 text-[7px] font-bold text-[#6f665b] hover:bg-[#faf8f4]">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-[8px] text-[#958c80]">No registration applications yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
