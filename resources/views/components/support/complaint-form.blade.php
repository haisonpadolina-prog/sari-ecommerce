<section class="mt-5 rounded-[18px] border border-[#eadfc9] bg-white p-4 sm:p-5">
    <h3 class="text-[11px] font-bold text-[#302a23]">Report an Issue</h3>
    <p class="mt-1 text-[8px] text-[#918677]">Send a platform complaint directly to the SARI Administrator.</p>
    <form method="POST" action="{{ route('platform.complaints.store') }}" class="mt-4 grid gap-3">
        @csrf
        <input name="subject" required maxlength="150" placeholder="Issue subject" class="h-10 rounded-xl border border-[#e6dccb] px-3 text-[8px]">
        <textarea name="description" required minlength="10" maxlength="3000" rows="4" placeholder="Describe what happened..." class="rounded-xl border border-[#e6dccb] px-3 py-3 text-[8px]"></textarea>
        <button class="w-fit rounded-xl bg-[#8f5f55] px-4 py-2.5 text-[8px] font-bold text-white">Submit Complaint</button>
    </form>
</section>
