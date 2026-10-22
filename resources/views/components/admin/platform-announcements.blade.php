@php
    $platformAnnouncementService = app(\App\Services\Platform\PlatformAnnouncementService::class);
    $platformAnnouncementContext = $platformAnnouncementService->currentIdentity(request());
    $platformAnnouncements = $platformAnnouncementService->activeForCurrentSession(request(), 5);
@endphp

@if($platformAnnouncementContext && $platformAnnouncements->isNotEmpty())
    <section
        class="sari-platform-announcements mb-4 grid gap-2"
        data-platform-announcements
        data-announcement-audience="{{ $platformAnnouncementContext['audience_role'] }}"
        aria-label="SARI announcements"
    >
        @foreach($platformAnnouncements as $announcement)
            @php
                $severity = in_array($announcement->severity, ['info','warning','critical'], true)
                    ? $announcement->severity
                    : 'info';
                $isUnread = !$announcement->viewer_read_at;
                $border = match($severity) {
                    'critical' => '#e4b6b6',
                    'warning' => '#ead7a8',
                    default => '#d8e1e8',
                };
                $background = match($severity) {
                    'critical' => '#fff6f6',
                    'warning' => '#fffaf0',
                    default => '#f8fafc',
                };
                $accent = match($severity) {
                    'critical' => '#a74646',
                    'warning' => '#8d681f',
                    default => '#586b7d',
                };
            @endphp
            <article
                data-platform-announcement="{{ $announcement->id }}"
                class="rounded-[10px] border px-3.5 py-3 shadow-[0_4px_16px_rgba(15,23,42,.035)]"
                style="border-color:{{ $border }};background:{{ $background }};"
            >
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="rounded-full border px-2 py-1 text-[7px] font-extrabold uppercase tracking-[.08em]"
                                style="border-color:{{ $border }};color:{{ $accent }};"
                            >
                                {{ $severity === 'critical' ? 'Important' : ($severity === 'warning' ? 'Notice' : 'SARI Update') }}
                            </span>
                            @if($isUnread)
                                <span class="text-[7px] font-extrabold text-[#B97805]">NEW</span>
                            @endif
                        </div>
                        <h2 class="mt-2 text-[11px] font-bold text-[#2e3741]">
                            {{ $announcement->title }}
                        </h2>
                        <p class="mt-1 text-[8.5px] leading-5 text-[#6f7a85]">
                            {{ $announcement->message }}
                        </p>
                        <div class="mt-2 flex flex-wrap items-center gap-2 text-[7px] text-[#8a949e]">
                            @if($announcement->ends_at)
                                <span>
                                    Until {{ $announcement->ends_at->copy()->timezone('Asia/Manila')->format('M j, Y h:i A') }} PHT
                                </span>
                            @endif
                            @if($announcement->action_url && $announcement->action_label)
                                <a
                                    href="{{ $announcement->action_url }}"
                                    class="font-bold text-[#B97805] hover:underline"
                                >
                                    {{ $announcement->action_label }}
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($isUnread)
                        <button
                            type="button"
                            data-announcement-dismiss
                            data-read-url="{{ route('platform-announcements.read', $announcement) }}"
                            class="shrink-0 rounded-[7px] border border-[#d8dde2] bg-white px-2.5 py-1.5 text-[7px] font-bold text-[#65717c] transition hover:border-[#d6b66f] hover:text-[#94620a]"
                        >
                            Dismiss
                        </button>
                    @endif
                </div>
            </article>
        @endforeach
    </section>

    @once
        <script>
            document.addEventListener('click', async function (event) {
                const button = event.target.closest('[data-announcement-dismiss]');
                if (!button) return;

                const card = button.closest('[data-platform-announcement]');
                const url = button.dataset.readUrl;
                if (!url) return;

                button.disabled = true;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        throw new Error('Unable to dismiss announcement');
                    }

                    card?.remove();
                    const wrapper = document.querySelector('[data-platform-announcements]');
                    if (wrapper && !wrapper.querySelector('[data-platform-announcement]')) {
                        wrapper.remove();
                    }
                } catch (error) {
                    button.disabled = false;
                }
            });
        </script>
    @endonce
@endif
