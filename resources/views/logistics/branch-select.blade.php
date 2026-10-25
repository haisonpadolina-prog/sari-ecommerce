<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Select Operating Branch · SARI Logistics</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        html { background: #e9eaec; }
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-weight: 300;
            color: #202328;
        }

        .page-backdrop {
            position: fixed;
            inset: 0;
            overflow: hidden;
            background: linear-gradient(135deg, #e7e8ea 0%, #f0f1f2 48%, #e5e6e8 100%);
        }

        .page-backdrop::before,
        .page-backdrop::after {
            content: '';
            position: absolute;
            border-radius: 999px;
            background: rgba(126, 132, 140, .18);
            filter: blur(90px);
            pointer-events: none;
        }

        .page-backdrop::before {
            width: 480px;
            height: 480px;
            left: -120px;
            top: -130px;
        }

        .page-backdrop::after {
            width: 560px;
            height: 560px;
            right: -180px;
            bottom: -180px;
            background: rgba(91, 97, 106, .12);
        }

        .page-overlay {
            position: fixed;
            inset: 0;
            background: rgba(238, 239, 241, .58);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        .selector-card {
            box-shadow: 0 26px 70px rgba(26, 31, 37, .13);
        }

        .branch-option {
            transition: border-color .16s ease, background-color .16s ease, box-shadow .16s ease, transform .16s ease;
        }

        .branch-option:hover {
            border-color: #c7cbd0;
            background: #fafafa;
        }

        .branch-radio:checked + .branch-option {
            border-color: #c98913;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(201, 137, 19, .08);
        }

        .branch-radio:checked + .branch-option .branch-check {
            border-color: #c98913;
            background: #c98913;
            box-shadow: inset 0 0 0 4px #fff;
        }

        .branch-radio:focus-visible + .branch-option {
            outline: 3px solid rgba(201, 137, 19, .18);
            outline-offset: 3px;
        }

        .primary-action {
            transition: background-color .16s ease, box-shadow .16s ease, transform .16s ease;
        }

        .primary-action:not(:disabled):hover {
            background: #b77b0e;
            box-shadow: 0 9px 22px rgba(201, 137, 19, .22);
            transform: translateY(-1px);
        }

        @media (max-width: 640px) {
            .selector-card { border-radius: 20px; }
        }

        @media (prefers-reduced-motion: reduce) {
            .branch-option,
            .primary-action {
                transition: none !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body class="min-h-screen antialiased">
    <div class="page-backdrop" aria-hidden="true"></div>
    <div class="page-overlay" aria-hidden="true"></div>

    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-5 sm:px-6 sm:py-6">
        <section class="selector-card w-full max-w-[600px] overflow-hidden rounded-[22px] border border-[#dfe2e6] bg-white/95" aria-labelledby="branchSelectTitle">

            <header class="border-b border-[#e8eaed] px-5 py-4 sm:px-6 sm:py-4.5">
                <div class="flex items-center justify-between gap-5">
                    <div class="flex min-w-0 items-center">
                        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-[12px] border border-[#e3e5e8] bg-[#f5f6f7]">
                            <img src="{{ asset('images/sari-main-logo.png') }}" alt="SARI" class="h-7 w-7 object-contain">
                        </div>

                    </div>

                    <div class="hidden items-center gap-2 rounded-full border border-[#e1e4e7] bg-[#f6f7f8] px-3 py-1.5 text-[9px] font-normal text-[#70767e] sm:flex">
                        <span class="h-1.5 w-1.5 rounded-full bg-[#5d9a70]"></span>
                        {{ $facilities->count() }} active {{ \Illuminate\Support\Str::plural('branch', $facilities->count()) }}
                    </div>
                </div>
            </header>

            <div class="px-5 py-5 sm:px-6 sm:py-6">
                <div>
                    <h1 id="branchSelectTitle" class="text-[24px] font-medium leading-tight tracking-[-.035em] text-[#181b1f] sm:text-[25px]">
                        Select operating branch
                    </h1>
                    <p class="mt-2 max-w-[500px] text-[12px] font-light leading-6 text-[#737980]">
                        Choose the sorting facility you are physically operating from for this session.
                    </p>
                </div>

                @if(session('branch_notice'))
                    <div class="mt-5 rounded-[13px] border border-[#dfe2e5] bg-[#f6f7f8] px-4 py-3.5 text-[12px] font-light leading-5 text-[#5d636a]" role="status">
                        {{ session('branch_notice') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mt-5 rounded-[13px] border border-[#e8caca] bg-[#fbf5f5] px-4 py-3.5 text-[12px] font-normal leading-5 text-[#984848]" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if($facilities->isEmpty())
                    <div class="mt-7 rounded-[17px] border border-dashed border-[#d5d9dd] bg-[#f8f9fa] px-6 py-10 text-center">
                        <div class="mx-auto grid h-10 w-10 place-items-center rounded-full border border-[#e1e4e7] bg-white text-[#68707a]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                <path d="M3 21h18M5 21V8l7-4 7 4v13M8 12h3m2 0h3M8 16h8"/>
                            </svg>
                        </div>
                        <h2 class="mt-4 text-[17px] font-medium text-[#24282d]">No active sorting facility</h2>
                        <p class="mx-auto mt-2 max-w-[440px] text-[12px] font-light leading-6 text-[#777e86]">
                            Register at least one real warehouse or sorting hub before opening branch operations.
                        </p>
                        <a href="{{ route('logistics.facilities.index') }}" class="mt-6 inline-flex h-11 items-center justify-center rounded-[11px] bg-[#30343a] px-5 text-[12px] font-medium text-white transition hover:bg-[#25282d] focus:outline-none focus:ring-4 focus:ring-[#c98913]/20">
                            Manage Logistics Network
                        </a>
                    </div>
                @else
                    <form method="POST" action="{{ route('logistics.branch.store', [], false) }}" class="mt-5" id="branchSelectionForm">
                        @csrf

                        <fieldset>
                            <legend class="mb-2.5 text-[11px] font-normal text-[#555b63]">Available branches</legend>

                            <div class="space-y-2.5">
                                @foreach($facilities as $facility)
                                    <label class="block cursor-pointer">
                                        <input
                                            type="radio"
                                            name="facility_id"
                                            value="{{ $facility->id }}"
                                            class="branch-radio sr-only"
                                            {{ (int) old('facility_id', $selectedFacility?->id) === (int) $facility->id ? 'checked' : '' }}
                                            required
                                        >

                                        <div class="branch-option rounded-[13px] border border-[#e0e3e6] bg-white px-4 py-3.5 sm:px-4 sm:py-4">
                                            <div class="flex items-start gap-3.5">
                                                <span class="branch-check mt-1 h-[17px] w-[17px] shrink-0 rounded-full border-2 border-[#bfc4ca] bg-white"></span>

                                                <div class="min-w-0 flex-1">
                                                    <div class="flex flex-wrap items-center gap-2.5">
                                                        <h2 class="text-[14px] font-medium leading-6 text-[#20242a] sm:text-[14px]">{{ $facility->name }}</h2>

                                                        @if($facility->is_main_branch)
                                                            <span class="rounded-full border border-[#d9dde1] bg-[#f2f3f4] px-2.5 py-1 text-[9px] font-medium uppercase tracking-[.06em] text-[#666d75]">Main Branch</span>
                                                        @endif

                                                        @if($facility->code)
                                                            <span class="rounded-full border border-[#e2e4e7] bg-[#f7f8f9] px-2.5 py-1 text-[9px] font-normal uppercase tracking-[.06em] text-[#7a8189]">{{ $facility->code }}</span>
                                                        @endif
                                                    </div>

                                                    <p class="mt-1.5 text-[11px] font-normal text-[#555c64]">
                                                        {{ $facility->municipality_name ?: 'Municipality not set' }}{{ $facility->province_name ? ', '.$facility->province_name : '' }}
                                                    </p>

                                                    @if($facility->address)
                                                        <p class="mt-1 line-clamp-1 text-[9px] font-light leading-5 text-[#858b92]">{{ $facility->address }}</p>
                                                    @endif
                                                </div>

                                                <div class="hidden shrink-0 items-center gap-2 pt-0.5 text-[9px] font-normal text-[#5f7f69] sm:flex">
                                                    <span class="h-2 w-2 rounded-full bg-[#5d9a70]"></span>
                                                    Active
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <div class="mt-5 border-t border-[#e9ebed] pt-4">
                            <button
                                type="submit"
                                class="primary-action inline-flex h-11 w-full items-center justify-center gap-2.5 rounded-[11px] bg-[#c98913] px-5 text-[12px] font-medium text-white focus:outline-none focus:ring-4 focus:ring-[#c98913]/20 disabled:cursor-not-allowed disabled:opacity-40"
                                id="enterBranchButton"
                            >
                                Enter selected branch
                                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path d="M5 12h14M13 6l6 6-6 6"/>
                                </svg>
                            </button>

                            <p class="mx-auto mt-3 max-w-[560px] text-center text-[9px] font-light leading-5 text-[#92979d]">
                                Branch selection scopes operational data for this session. Company finance and network management remain network-wide.
                            </p>
                        </div>
                    </form>
                @endif
            </div>
        </section>
    </main>

    @if(!$facilities->isEmpty())
        <script>
            (() => {
                const radios = Array.from(document.querySelectorAll('input[name="facility_id"]'));
                const submit = document.getElementById('enterBranchButton');

                const sync = () => {
                    if (!submit) return;
                    submit.disabled = !radios.some((radio) => radio.checked);
                };

                radios.forEach((radio) => radio.addEventListener('change', sync));
                sync();
            })();
        </script>
    @endif
</body>
</html>
