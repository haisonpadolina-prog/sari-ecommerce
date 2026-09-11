<section class="mb-5 rounded-[22px] border border-[#ebe4da] bg-white p-5 sm:p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <span class="rounded-full border border-[#e8e1d7] bg-[#fcfaf7] px-3 py-1 text-[8px] font-bold uppercase tracking-[.12em] text-[#847a6e]">Live Operations</span>
            <h2 class="mt-3 text-[20px] font-bold text-[#211c16]">Marketplace control tower</h2>
            <p class="mt-1 text-[9px] text-[#81786c]">Real account, order, delivery, complaint and GMV data from the database.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.users') }}" class="rounded-xl border border-[#e5ddd2] px-4 py-2 text-[8px] font-semibold text-[#6f665b]">Accounts</a>
            <a href="{{ route('admin.reports') }}" class="rounded-xl bg-[#c99128] px-4 py-2 text-[8px] font-semibold text-white">Open Reports</a>
        </div>
    </div>

    <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4 xl:grid-cols-7">
        @foreach([
            ['Buyers',$adminOperations['buyers']],
            ['Sellers',$adminOperations['sellers']],
            ['Riders',$adminOperations['riders']],
            ['Logistics',$adminOperations['logistics']],
            ['Active Orders',$adminOperations['orders_active']],
            ['Delivered Today',$adminOperations['delivered_today']],
            ['Open Complaints',$adminOperations['open_complaints']],
        ] as [$label,$value])
            <div class="rounded-2xl border border-[#eee7de] bg-[#fcfbf8] p-4">
                <p class="text-[7px] uppercase tracking-[.08em] text-[#958c80]">{{ $label }}</p>
                <p class="mt-2 text-[20px] font-bold text-[#28221b]">{{ number_format($value) }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-[1fr_320px]">
        <div class="overflow-hidden rounded-2xl border border-[#eee7de]">
            <div class="border-b border-[#eee7de] px-4 py-3"><p class="text-[9px] font-bold text-[#514a42]">Recent Orders</p></div>
            <div class="divide-y divide-[#f0ebe4]">
                @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                        <div class="min-w-0">
                            <p class="truncate text-[9px] font-bold text-[#332d25]">{{ $order->order_number }} · {{ $order->seller?->store_name ?: 'SARI Seller' }}</p>
                            <p class="mt-1 truncate text-[7px] text-[#918677]">{{ $order->buyer_name }} · {{ $order->statusLabel() }}</p>
                        </div>
                        <p class="shrink-0 text-[9px] font-bold text-[#332d25]">₱{{ number_format((float)$order->total,2) }}</p>
                    </div>
                @empty
                    <p class="p-5 text-center text-[8px] text-[#918677]">No orders yet.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl border border-[#eee7de] bg-[#fcfbf8] p-4">
            <p class="text-[8px] font-bold text-[#514a42]">Delivered GMV</p>
            <p class="mt-2 text-[25px] font-bold text-[#28221b]">₱{{ number_format($adminOperations['gmv'],2) }}</p>
            <div class="mt-4 space-y-2 text-[8px] text-[#81786c]">
                <div class="flex justify-between"><span>Total orders</span><strong>{{ number_format($adminOperations['orders_total']) }}</strong></div>
                <div class="flex justify-between"><span>Ready for pickup</span><strong>{{ number_format($adminOperations['ready_pickup']) }}</strong></div>
                <div class="flex justify-between"><span>In delivery flow</span><strong>{{ number_format($adminOperations['in_transit']) }}</strong></div>
            </div>
        </div>
    </div>
</section>
