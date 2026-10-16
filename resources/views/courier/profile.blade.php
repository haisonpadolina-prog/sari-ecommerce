@extends('layouts.courier')

@section('title', 'Rider Account')
@section('header-title', 'Account Management')
@section('header-subtitle', 'Rider Profile & Vehicle')

@section('content')

<div class="mx-auto max-w-[1200px] space-y-4">

    @if(session('success'))

        <div
            class="
                rounded-2xl
                border border-emerald-200
                bg-emerald-50
                px-5 py-4
                text-[9px]
                font-semibold
                text-emerald-700
            "
        >
            {{ session('success') }}
        </div>

    @endif


    @if($errors->any())

        <div
            class="
                rounded-2xl
                border border-rose-200
                bg-rose-50
                px-5 py-4
                text-[9px]
                font-semibold
                text-rose-700
            "
        >
            {{ $errors->first() }}
        </div>

    @endif


    {{-- =====================================================
        ACCOUNT SUMMARY
    ====================================================== --}}

    <section
        class="
            overflow-hidden
            rounded-[22px]
            border border-[#eadfc9]
            bg-[#fffdf8]
        "
    >

        <div
            class="
                flex flex-col
                justify-between
                gap-5
                p-5
                sm:p-6
                lg:flex-row
                lg:items-center
            "
        >

            <div
                class="
                    flex
                    items-center
                    gap-4
                "
            >

                <div
                    class="
                        grid h-14 w-14
                        shrink-0
                        place-items-center
                        rounded-full
                        bg-[#3C6E91]
                        text-[15px]
                        font-bold
                        text-white
                    "
                >
                    {{
                        strtoupper(
                            mb_substr(
                                trim(
                                    (string)
                                    $profile['full_name']
                                ),
                                0,
                                1
                            )
                        )
                    }}
                </div>


                <div>

                    <div
                        class="
                            flex flex-wrap
                            items-center
                            gap-2
                        "
                    >

                        <h2
                            class="
                                text-[17px]
                                font-bold
                                text-[#211d17]
                            "
                        >
                            {{ $profile['full_name'] }}
                        </h2>


                        <span
                            class="
                                rounded-full
                                border
                                px-2.5 py-1
                                text-[7px]
                                font-bold
                                uppercase

                                {{
                                    $profile['availability_status']
                                    === 'online'
                                        ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                                        : 'border-slate-200 bg-slate-50 text-slate-600'
                                }}
                            "
                        >
                            {{
                                strtoupper(
                                    $profile[
                                        'availability_status'
                                    ]
                                )
                            }}
                        </span>

                    </div>


                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        {{ $profile['vehicle_type'] }}
                        ·
                        {{ $profile['plate_number'] }}
                    </p>


                    <p
                        class="
                            mt-1
                            text-[8px]
                            text-[#918677]
                        "
                    >
                        Joined {{ $profile['joined'] }}
                    </p>

                </div>

            </div>


            <form
                method="POST"
                action="{{
                    route(
                        'courier.profile.availability'
                    )
                }}"
                class="
                    flex flex-col
                    gap-2
                    sm:flex-row
                    sm:items-center
                "
            >
                @csrf
                @method('PATCH')


                <label
                    class="
                        text-[8px]
                        font-semibold
                        text-[#6f6559]
                    "
                >
                    Availability

                    <select
                        name="availability_status"
                        class="
                            mt-2 h-10
                            min-w-[150px]
                            rounded-xl
                            border border-[#e6dccb]
                            bg-white
                            px-3
                            text-[8px]
                            font-semibold
                            text-[#51483d]
                            outline-none
                            focus:border-[#d9930a]
                        "
                    >

                        <option
                            value="online"
                            @selected(
                                $profile[
                                    'availability_status'
                                ]
                                === 'online'
                            )
                        >
                            Online
                        </option>

                        <option
                            value="offline"
                            @selected(
                                $profile[
                                    'availability_status'
                                ]
                                === 'offline'
                            )
                        >
                            Offline
                        </option>

                    </select>
                </label>


                <button
                    class="
                        mt-auto
                        h-10
                        rounded-xl
                        bg-[#3C6E91]
                        px-5
                        text-[8px]
                        font-bold
                        text-white
                    "
                >
                    Update Status
                </button>

            </form>

        </div>


        <div
            class="
                grid
                border-t border-[#eadfc9]
                sm:grid-cols-3
            "
        >

            <div
                class="
                    border-b border-[#eadfc9]
                    px-5 py-4
                    sm:border-b-0
                    sm:border-r
                "
            >

                <p
                    class="
                        text-[7px]
                        uppercase
                        tracking-[.11em]
                        text-[#9a8f80]
                    "
                >
                    Completed Deliveries
                </p>


                <p
                    class="
                        mt-2
                        text-[20px]
                        font-bold
                        text-[#211d17]
                    "
                >
                    {{
                        $profile[
                            'completed_deliveries'
                        ]
                    }}
                </p>

            </div>


            <div
                class="
                    border-b border-[#eadfc9]
                    px-5 py-4
                    sm:border-b-0
                    sm:border-r
                "
            >

                <p
                    class="
                        text-[7px]
                        uppercase
                        tracking-[.11em]
                        text-[#9a8f80]
                    "
                >
                    Rider Rating
                </p>


                <p
                    class="
                        mt-2
                        text-[20px]
                        font-bold
                        text-[#211d17]
                    "
                >
                    ★ {{ $profile['rating'] }}
                </p>

            </div>


            <div class="px-5 py-4">

                <p
                    class="
                        text-[7px]
                        uppercase
                        tracking-[.11em]
                        text-[#9a8f80]
                    "
                >
                    Assigned Area
                </p>


                <p
                    class="
                        mt-2
                        text-[10px]
                        font-semibold
                        text-[#51483d]
                    "
                >
                    {{
                        $account->barangay_name
                        ?: 'Not assigned'
                    }}
                </p>


                <p
                    class="
                        mt-1
                        text-[8px]
                        text-[#918677]
                    "
                >
                    {{
                        $account->municipality_name
                        ?: 'Municipality unavailable'
                    }}
                </p>

            </div>

        </div>

    </section>


    {{-- =====================================================
        PROFILE FORM
    ====================================================== --}}

    <section
        class="
            overflow-hidden
            rounded-[22px]
            border border-[#eadfc9]
            bg-white
        "
    >

        <div
            class="
                border-b border-[#eadfc9]
                px-5 py-4
                sm:px-6
            "
        >

            <h3
                class="
                    text-[13px]
                    font-bold
                    text-[#211d17]
                "
            >
                Rider Information
            </h3>


            <p
                class="
                    mt-1
                    text-[8px]
                    text-[#918677]
                "
            >
                Update your profile, contact details,
                vehicle information, and password.
            </p>

        </div>


        <form
            method="POST"
            action="{{
                route(
                    'courier.profile.update'
                )
            }}"
            enctype="multipart/form-data"
            class="
                grid
                gap-4
                p-5
                sm:grid-cols-2
                sm:p-6
            "
        >
            @csrf
            @method('PATCH')


            <div class="sm:col-span-2">

                <x-account.profile-photo
                    :account="$account"
                />

            </div>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                First Name

                <input
                    name="first_name"
                    value="{{
                        old(
                            'first_name',
                            $account->first_name
                        )
                    }}"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#d9930a]
                        focus:ring-4
                        focus:ring-[#d9930a]/10
                    "
                >
            </label>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                Last Name

                <input
                    name="last_name"
                    value="{{
                        old(
                            'last_name',
                            $account->last_name
                        )
                    }}"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#d9930a]
                        focus:ring-4
                        focus:ring-[#d9930a]/10
                    "
                >
            </label>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                Email

                <input
                    name="email"
                    type="email"
                    value="{{
                        old(
                            'email',
                            $account->email
                        )
                    }}"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#d9930a]
                        focus:ring-4
                        focus:ring-[#d9930a]/10
                    "
                >
            </label>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                Contact Number

                <input
                    name="contact_no"
                    value="{{
                        old(
                            'contact_no',
                            $account->contact_no
                        )
                    }}"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#d9930a]
                        focus:ring-4
                        focus:ring-[#d9930a]/10
                    "
                >
            </label>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                    sm:col-span-2
                "
            >
                Street Address

                <input
                    name="street_address"
                    value="{{
                        old(
                            'street_address',
                            $account->street_address
                        )
                    }}"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#d9930a]
                        focus:ring-4
                        focus:ring-[#d9930a]/10
                    "
                >
            </label>


            <div
                class="
                    sm:col-span-2
                    my-1
                    border-t border-[#eee4d3]
                "
            ></div>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                Vehicle Type

                <input
                    name="vehicle_type"
                    value="{{
                        old(
                            'vehicle_type',
                            $account->vehicle_type
                        )
                    }}"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#3C6E91]
                        focus:ring-4
                        focus:ring-[#3C6E91]/10
                    "
                >
            </label>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                Vehicle Model

                <input
                    name="vehicle_model"
                    value="{{
                        old(
                            'vehicle_model',
                            $account->vehicle_model
                        )
                    }}"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#3C6E91]
                        focus:ring-4
                        focus:ring-[#3C6E91]/10
                    "
                >
            </label>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                Plate Number

                <input
                    name="plate_number"
                    value="{{
                        old(
                            'plate_number',
                            $account->plate_number
                        )
                    }}"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#3C6E91]
                        focus:ring-4
                        focus:ring-[#3C6E91]/10
                    "
                >
            </label>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                License Number

                <input
                    name="license_number"
                    value="{{
                        old(
                            'license_number',
                            $account->license_number
                        )
                    }}"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#3C6E91]
                        focus:ring-4
                        focus:ring-[#3C6E91]/10
                    "
                >
            </label>


            <div
                class="
                    sm:col-span-2
                    my-1
                    border-t border-[#eee4d3]
                "
            ></div>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                Current Password

                <input
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#d9930a]
                        focus:ring-4
                        focus:ring-[#d9930a]/10
                    "
                >
            </label>


            <div class="hidden sm:block"></div>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                New Password

                <input
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#d9930a]
                        focus:ring-4
                        focus:ring-[#d9930a]/10
                    "
                >
            </label>


            <label
                class="
                    text-[8px]
                    font-semibold
                    text-[#62594d]
                "
            >
                Confirm New Password

                <input
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="
                        mt-2 h-11
                        w-full
                        rounded-xl
                        border border-[#e5dac9]
                        bg-white
                        px-3
                        text-[9px]
                        outline-none
                        focus:border-[#d9930a]
                        focus:ring-4
                        focus:ring-[#d9930a]/10
                    "
                >
            </label>


            <div
                class="
                    flex
                    justify-end
                    sm:col-span-2
                "
            >

                <button
                    class="
                        rounded-xl
                        bg-[#D99600]
                        px-6 py-3
                        text-[9px]
                        font-bold
                        text-white
                        shadow-[0_8px_20px_rgba(217,150,0,.14)]
                    "
                >
                    Save Rider Profile
                </button>

            </div>

        </form>

    </section>


    @include(
        'components.support.complaint-form'
    )

</div>

@endsection