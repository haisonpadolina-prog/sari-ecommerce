{{-- ARCHIVE CONFIRMATION --}}
<div id="productsActionModal" class="fixed inset-0 z-[190] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-[3px]">
    <div class="w-full max-w-[430px] rounded-[22px] border border-[#e7dfd4] bg-white p-5 shadow-[0_30px_90px_rgba(37,29,19,.24)] sm:p-6">
        <div class="flex items-start gap-3.5">
            <span id="productsActionIcon" class="grid h-11 w-11 shrink-0 place-items-center rounded-[13px] bg-[#fff5e7] text-[#b97918]">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16"></path><path d="M6 7v12h12V7"></path><path d="M9 11h6"></path></svg>
            </span>
            <div>
                <h3 id="productsActionTitle" class="text-[17px] font-bold tracking-[-0.025em] text-[#302a24]">Archive Product?</h3>
                <p id="productsActionText" class="mt-1.5 text-[9.5px] leading-5 text-[#81786d]"></p>
            </div>
        </div>

        <form id="productsActionForm" method="POST" class="mt-5 flex justify-end gap-2.5">
            @csrf
            <button id="productsActionCancel" type="button" class="h-10 rounded-xl border border-[#e3dcd2] bg-white px-4 text-[9px] font-semibold text-[#746b61] hover:bg-[#faf8f5]">Cancel</button>
            <button id="productsActionSubmit" type="submit" class="h-10 rounded-xl bg-[#c99128] px-5 text-[9px] font-semibold text-white hover:bg-[#b47e1e]">Archive Product</button>
        </form>
    </div>
</div>
