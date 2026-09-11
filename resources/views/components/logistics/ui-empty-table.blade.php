@props([
    'columns' => [],
    'title' => 'No data connected yet',
    'description' => 'This screen is UI-only for now. We will connect the real function when its workflow is defined.',
])

<div class="overflow-hidden rounded-[20px] border border-[#eee4d3] bg-white shadow-[0_12px_35px_rgba(76,58,34,0.06)]">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-[#fffaf1]">
                <tr>
                    @foreach ($columns as $column)
                        <th class="whitespace-nowrap px-5 py-3.5 text-left text-[10px] font-bold uppercase tracking-[0.07em] text-[#8b8174]">{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="{{ max(count($columns), 1) }}" class="px-6 py-16 text-center">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl bg-[#fff4dd] text-[#b97805]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 6h16"></path><path d="M4 12h16"></path><path d="M4 18h10"></path></svg>
                        </div>
                        <p class="mt-4 text-[13px] font-bold text-[#342d24]">{{ $title }}</p>
                        <p class="mx-auto mt-2 max-w-[520px] text-[11px] leading-5 text-[#8c8275]">{{ $description }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
