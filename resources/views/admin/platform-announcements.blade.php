@extends('layouts.admin')



@section('title', 'Platform Announcements — SARI Admin')

@section('page-title', 'Platform Announcements')



@section('content')

@php

    $editing = $editAnnouncement;

    $audienceOld = old('audiences', $editing?->audiences ?? ['buyer']);

    $toPhtInput = static fn ($value) => $value

        ? $value->copy()->timezone('Asia/Manila')->format('Y-m-d\TH:i')

        : '';

@endphp



<div class="ann-page mx-auto w-full max-w-[1500px] pb-8">

    <style>
    .ann-page{
        --gold:#D59617;
        --gold-strong:#B97805;
        --ink:#111827;
        --text:#344054;
        --muted:#7C8592;
        --line:#E5E7EB;
        --divider:#EEF0F3;
        --soft:#F8FAFC;
        --blue:#1677FF;
        --red:#FF334B;
        --shadow:0 2px 5px rgba(15,23,42,.04),0 10px 24px rgba(15,23,42,.07),0 24px 54px rgba(15,23,42,.08);
        --shadow-soft:0 1px 3px rgba(15,23,42,.03),0 7px 18px rgba(15,23,42,.05);
        width:100%;
        max-width:1540px;
        margin:0 auto;
        padding-bottom:30px;
        color:var(--ink);
        font-family:'Poppins',ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
    }
    .ann-page *, .ann-page *::before, .ann-page *::after{box-sizing:border-box}

    /* Header */
    .ann-head{padding:2px 2px 17px;border-bottom:1px solid #E7E9EC}
    .ann-eyebrow{margin:0;color:#9A6F23;font-size:8px;font-weight:800;line-height:1.15;letter-spacing:.16em;text-transform:uppercase}
    .ann-title{margin:5px 0 0;font-size:clamp(30px,2.2vw,37px);font-weight:700;line-height:1;letter-spacing:-.04em;color:var(--ink)}
    .ann-title span{color:var(--gold)}
    .ann-copy{max-width:900px;margin:8px 0 0;color:var(--muted);font-size:10.5px;line-height:1.65}
    .ann-clock{display:inline-flex;align-items:center;gap:6px;margin-top:10px;border:1px solid var(--line);border-radius:999px;background:#F8F9FA;padding:6px 10px;color:#667085;font-size:7.5px;font-weight:700;box-shadow:none}
    .ann-clock::before{content:"";width:6px;height:6px;border-radius:999px;background:var(--gold);box-shadow:0 0 0 3px rgba(213,150,23,.10)}

    /* Grid / surfaces */
    .ann-grid{display:grid;grid-template-columns:minmax(0,1.08fr) minmax(420px,.92fr);gap:16px;margin-top:16px;align-items:start}
    .ann-panel{overflow:visible;border:1px solid var(--line);border-radius:14px;background:#fff;box-shadow:var(--shadow)}
    .ann-panel-head{display:flex;min-height:66px;align-items:center;justify-content:space-between;gap:16px;padding:14px 16px;border-bottom:1px solid var(--divider);background:#fff;border-radius:14px 14px 0 0}
    .ann-panel-head h2{margin:0;color:#1D2939;font-size:12px;font-weight:700;line-height:1.3}
    .ann-panel-head p{max-width:760px;margin:4px 0 0;color:#8A94A3;font-size:8.5px;line-height:1.55}
    .ann-body{padding:16px}
    .ann-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}
    .ann-field{display:block;min-width:0}
    .ann-field.full{grid-column:1/-1}
    .ann-label{display:block;margin-bottom:6px;color:#475467;font-size:8.8px;font-weight:700;line-height:1.35}

    /* Inputs */
    .ann-input,.ann-select,.ann-textarea,.ann-select-trigger{
        width:100%;border:1px solid #DDE2E7;border-radius:10px;background:#fff;color:#29313B;
        font:500 9.6px 'Poppins',sans-serif;outline:none;box-shadow:none;
        transition:border-color .15s ease,box-shadow .15s ease,background-color .15s ease;
    }
    .ann-input,.ann-select,.ann-select-trigger{height:42px}
    .ann-input,.ann-select{padding:0 11px}
    .ann-textarea{min-height:118px;padding:11px 12px;resize:vertical;line-height:1.55}
    .ann-input::placeholder,.ann-textarea::placeholder{color:#A0A8B2}
    .ann-input:hover,.ann-textarea:hover,.ann-select-trigger:hover{border-color:#CBD2D9}
    .ann-input:focus,.ann-textarea:focus,.ann-select-trigger:focus-visible,.ann-select-wrap.is-open .ann-select-trigger{border-color:var(--gold);box-shadow:0 0 0 3px rgba(213,150,23,.09)}
    .ann-input[type="datetime-local"]{color:#475467}
    .ann-input[type="file"]{height:auto;min-height:42px;padding:5px 6px;color:#667085;background:#FAFBFC}
    .ann-input[type="file"]::file-selector-button{height:30px;margin-right:8px;border:1px solid #DDE2E7;border-radius:8px;background:#fff;padding:0 10px;color:#475467;font:700 7.8px 'Poppins',sans-serif;cursor:pointer}
    .ann-input[type="file"]::file-selector-button:hover{border-color:#C9D0D7;background:#F8FAFC}

    /* Custom dropdown */
    .ann-select-wrap{position:relative;min-width:0;z-index:20}
    .ann-select-wrap.is-open{z-index:80}
    .ann-select.ann-native-select{position:absolute!important;width:1px!important;height:1px!important;overflow:hidden!important;clip:rect(0 0 0 0)!important;clip-path:inset(50%)!important;white-space:nowrap!important;opacity:0!important;pointer-events:none!important}
    .ann-select-trigger{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:0 11px;text-align:left;cursor:pointer}
    .ann-select-trigger-label{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#344054}
    .ann-select-chevron{width:14px;height:14px;flex:0 0 14px;color:#667085;transition:transform .15s ease}
    .ann-select-wrap.is-open .ann-select-chevron{transform:rotate(180deg)}
    .ann-select-menu{position:absolute;z-index:120;top:calc(100% + 6px);right:0;left:0;max-height:220px;overflow:auto;padding:5px;border:1px solid #E1E5EA;border-radius:11px;background:#fff;box-shadow:0 14px 34px rgba(15,23,42,.13),0 4px 12px rgba(15,23,42,.05);opacity:0;visibility:hidden;pointer-events:none;transform:translateY(-4px);transition:opacity .14s ease,transform .14s ease,visibility .14s ease}
    .ann-select-wrap.is-open .ann-select-menu{opacity:1;visibility:visible;pointer-events:auto;transform:translateY(0)}
    .ann-select-option{display:flex;width:100%;min-height:35px;align-items:center;justify-content:space-between;gap:10px;border:0;border-radius:8px;background:transparent;padding:0 9px;color:#475467;font:600 8.4px 'Poppins',sans-serif;text-align:left;cursor:pointer}
    .ann-select-option:hover,.ann-select-option:focus-visible{outline:none;background:#F7F8FA;color:#1D2939}
    .ann-select-check{display:grid;width:17px;height:17px;flex:0 0 17px;place-items:center;border-radius:5px;background:transparent;color:transparent}
    .ann-select-option.is-selected{background:#FFF9EC;color:#8A5C07}
    .ann-select-option.is-selected .ann-select-check{background:var(--gold);color:#fff}
    .ann-select-check svg{width:11px;height:11px}

    /* Audience */
    .ann-audiences{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px}
    .ann-audience{display:flex;min-height:42px;align-items:center;gap:8px;border:1px solid #E2E6EA;border-radius:10px;background:#FAFBFC;padding:0 10px;color:#475467;font-size:8.2px;font-weight:700;cursor:pointer;transition:border-color .15s ease,background-color .15s ease}
    .ann-audience:hover{border-color:#D4DAE0;background:#F8F9FA}
    .ann-audience:has(input:checked){border-color:#E6C875;background:#FFF9EC;color:#7F570C}
    .ann-audience input,.ann-remove input{appearance:none;-webkit-appearance:none;position:relative;width:16px;height:16px;flex:0 0 16px;margin:0;border:1px solid #C9D0D7;border-radius:5px;background:#fff;cursor:pointer;outline:none}
    .ann-audience input:checked,.ann-remove input:checked{border-color:var(--gold);background:var(--gold)}
    .ann-audience input:checked::after,.ann-remove input:checked::after{content:"";position:absolute;left:4px;top:1.5px;width:5px;height:9px;border:solid #fff;border-width:0 2px 2px 0;transform:rotate(45deg)}
    .ann-audience input:focus-visible,.ann-remove input:focus-visible{box-shadow:0 0 0 3px rgba(213,150,23,.12)}

    /* Image area */
    .ann-media{display:grid;grid-template-columns:132px minmax(0,1fr);gap:13px;align-items:start}
    .ann-preview{position:relative;display:flex;width:132px;height:96px;align-items:center;justify-content:center;overflow:hidden;border:1px dashed #CCD3DB;border-radius:12px;background:#F8F9FA;color:#8A94A3;padding:8px;font-size:7.3px;font-weight:700;line-height:1.45;text-align:center}
    .ann-preview img{display:block;width:100%;height:100%;object-fit:cover}
    .ann-upload-copy{margin:6px 0 0;color:#8A94A3;font-size:7.1px;line-height:1.55}
    .ann-remove{display:inline-flex;align-items:center;gap:7px;margin-top:8px;color:#667085;font-size:7.5px;font-weight:700;cursor:pointer}

    /* Buttons */
    .ann-actions{display:flex;flex-wrap:wrap;align-items:center;gap:9px;margin-top:16px;padding-top:14px;border-top:1px solid var(--divider)}
    .ann-btn{display:inline-flex;min-height:40px;align-items:center;justify-content:center;border-radius:9px;padding:0 13px;font-size:8.5px;font-weight:750;text-decoration:none;cursor:pointer;transition:border-color .15s ease,background-color .15s ease,color .15s ease}
    .ann-btn.primary{border:1px solid var(--gold);background:var(--gold);color:#fff;box-shadow:0 5px 13px rgba(194,132,10,.12)}
    .ann-btn.primary:hover,.ann-btn.primary:focus-visible{outline:none;border-color:var(--gold-strong);background:var(--gold-strong)}
    .ann-btn.secondary{border:1px solid #DDE2E7;background:#fff;color:#667085;box-shadow:none}
    .ann-btn.secondary:hover,.ann-btn.secondary:focus-visible{outline:none;border-color:#CBD2D9;background:#F8FAFC;color:#344054}

    /* Schedule list */
    .ann-panel > .ann-row{background:#fff}
    .ann-row{position:relative;padding:15px 16px;border-bottom:1px solid var(--divider);transition:background-color .15s ease}
    .ann-row:last-of-type{border-bottom:0}
    .ann-row:hover{background:#FCFCFD}
    .ann-row-top{display:flex;align-items:flex-start;justify-content:space-between;gap:14px}
    .ann-name{color:#172033;font-size:10.8px;font-weight:750;line-height:1.35}
    .ann-sub{max-width:620px;margin-top:4px;color:#7C8796;font-size:7.7px;line-height:1.55}
    .ann-badges{display:flex;flex-wrap:wrap;align-items:center;gap:6px;margin-top:9px}
    .ann-badge{display:inline-flex;min-height:22px;align-items:center;border:1px solid #E3E7EB;border-radius:999px;background:#F8F9FA;padding:0 8px;color:#667085;font-size:6.8px;font-weight:750;white-space:nowrap}
    .ann-badge.live{border-color:#D7E8DC;background:#F3F9F5;color:#3F7A54}
    .ann-badge.scheduled{border-color:#EBD9A9;background:#FFF8E6;color:#8A6414}
    .ann-badge.paused,.ann-badge.draft{border-color:#E1E5E9;background:#F7F8F9;color:#667085}
    .ann-badge.ended{border-color:#F0D4D4;background:#FFF7F7;color:#A34E4E}
    .ann-panel-head > .ann-badge{background:#F8F9FA;color:#667085}

    .ann-row-actions{display:flex;align-items:center;gap:5px;margin-top:11px}
    .ann-row-actions form{display:inline-flex;margin:0}
    .ann-icon-action{display:inline-grid;width:30px;height:30px;place-items:center;border:0;border-radius:8px;background:transparent;padding:0;text-decoration:none;cursor:pointer;transition:background-color .15s ease,color .15s ease}
    .ann-icon-action svg{width:16px;height:16px;stroke:currentColor}
    .ann-icon-action.edit{color:var(--blue)}
    .ann-icon-action.edit:hover,.ann-icon-action.edit:focus-visible{outline:none;background:#EEF6FF;color:#006BEE}
    .ann-icon-action.toggle{color:#111827}
    .ann-icon-action.toggle:hover,.ann-icon-action.toggle:focus-visible{outline:none;background:#F1F3F5;color:#000}
    .ann-icon-action.delete{color:var(--red)}
    .ann-icon-action.delete:hover,.ann-icon-action.delete:focus-visible{outline:none;background:#FFF0F2;color:#F5002E}

    .ann-flash{margin-top:13px;border-radius:10px;padding:10px 12px;font-size:8.5px;font-weight:650}
    .ann-flash.ok{border:1px solid #D7E7DC;background:#F5FAF6;color:#51715D}
    .ann-flash.err{border:1px solid #EAD2CC;background:#FFF6F3;color:#9A5A49}
    .ann-empty{padding:38px 18px;color:#7D8792;font-size:8.5px;text-align:center}

    @media(max-width:1100px){
        .ann-grid{grid-template-columns:1fr}
        .ann-audiences{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media(max-width:640px){
        .ann-page{padding-bottom:20px}
        .ann-title{font-size:clamp(28px,8vw,34px)}
        .ann-fields{grid-template-columns:1fr}
        .ann-field.full{grid-column:auto}
        .ann-audiences{grid-template-columns:1fr 1fr}
        .ann-media{grid-template-columns:1fr}
        .ann-preview{width:100%;height:160px}
        .ann-panel-head{align-items:flex-start;flex-direction:column}
        .ann-actions{align-items:stretch;flex-direction:column}
        .ann-btn{width:100%}
        .ann-actions > span{width:100%}
    }
</style>




    <div class="ann-head">

        <p class="ann-eyebrow">Targeted Marketplace Communication</p>

        <h1 class="ann-title">Platform <span>Announcements</span></h1>

        <p class="ann-copy">Publish scheduled SARI notices to Buyers, Sellers, Logistics providers, and Riders. Announcements use Philippine time and appear only to the selected audiences while live.</p>

        <div class="ann-clock">Philippine Standard Time · {{ $philippineNow->format('M j, Y h:i:s A') }} PHT</div>

    </div>



    @if(session('success'))<div class="ann-flash ok">{{ session('success') }}</div>@endif

    @if($errors->any())<div class="ann-flash err">{{ $errors->first() }}</div>@endif



    <div class="ann-grid">

        <section class="ann-panel">

            <div class="ann-panel-head">

                <div>

                    <h2>{{ $editing ? 'Edit announcement' : 'Create announcement' }}</h2>

                    <p>Publish is the default. Choose Save as draft only when you do not want the announcement visible yet. Future start times are automatically scheduled in Philippine time.</p>

                </div>

                @if($editing)<a class="ann-btn secondary" href="{{ route('admin.announcements') }}">New</a>@endif

            </div>

            <div class="ann-body">

                <form method="POST" enctype="multipart/form-data" action="{{ $editing ? route('admin.announcements.update', $editing) : route('admin.announcements.store') }}">

                    @csrf

                    @if($editing) @method('PATCH') @endif

                    <div class="ann-fields">

                        <label class="ann-field full">

                            <span class="ann-label">Title</span>

                            <input class="ann-input" type="text" name="title" maxlength="160" value="{{ old('title', $editing?->title) }}" required>

                        </label>

                        <label class="ann-field full">

                            <span class="ann-label">Message</span>

                            <textarea class="ann-textarea" name="message" maxlength="3000" required>{{ old('message', $editing?->message) }}</textarea>

                        </label>



                        <div class="ann-field full">

                            <span class="ann-label">Announcement image (optional)</span>

                            <div class="ann-media">

                                <div class="ann-preview" data-announcement-image-preview>

                                    @if($editing?->image_path)

                                        <img

                                            src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($editing->image_path) }}"

                                            alt="Current announcement image"

                                            data-current-announcement-image

                                        >

                                    @else

                                        <span data-announcement-image-placeholder>No image selected</span>

                                    @endif

                                </div>

                                <div>

                                    <input

                                        class="ann-input"

                                        style="padding-top:9px"

                                        type="file"

                                        name="image"

                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"

                                        data-announcement-image-input

                                    >

                                    <p class="ann-upload-copy">JPG, JPEG, PNG, or WebP · maximum 5 MB · maximum 5000 × 5000 px. The image appears inside the announcement popup.</p>

                                    @if($editing?->image_path)

                                        <label class="ann-remove">

                                            <input type="checkbox" name="remove_image" value="1" data-announcement-remove-image>

                                            <span>Remove current image</span>

                                        </label>

                                    @endif

                                </div>

                            </div>

                        </div>

                        <div class="ann-field full">

                            <span class="ann-label">Audience</span>

                            <div class="ann-audiences">

                                @foreach($audienceOptions as $role)

                                    <label class="ann-audience">

                                        <input type="checkbox" name="audiences[]" value="{{ $role }}" @checked(in_array($role, (array)$audienceOld, true))>

                                        <span>{{ ucfirst($role) }}</span>

                                    </label>

                                @endforeach

                            </div>

                        </div>

                        <div class="ann-field">

                            <span class="ann-label">Priority</span>

                            <select class="ann-select" name="severity" required>

                                <option value="info" @selected(old('severity',$editing?->severity ?? 'info')==='info')>Information</option>

                                <option value="warning" @selected(old('severity',$editing?->severity ?? 'info')==='warning')>Warning</option>

                                <option value="critical" @selected(old('severity',$editing?->severity ?? 'info')==='critical')>Critical</option>

                            </select>

                        </div>

                        <label class="ann-field">

                            <span class="ann-label">Action label (optional)</span>

                            <input class="ann-input" type="text" name="action_label" maxlength="80" value="{{ old('action_label',$editing?->action_label) }}" placeholder="View orders">

                        </label>

                        <label class="ann-field full">

                            <span class="ann-label">Action URL (optional)</span>

                            <input class="ann-input" type="text" name="action_url" maxlength="500" value="{{ old('action_url',$editing?->action_url) }}" placeholder="/buyer/orders or https://...">

                        </label>

                        <label class="ann-field">

                            <span class="ann-label">Starts at · PHT</span>

                            <input class="ann-input" type="datetime-local" name="starts_at" value="{{ old('starts_at', $toPhtInput($editing?->starts_at)) }}">

                        </label>

                        <label class="ann-field">

                            <span class="ann-label">Ends at · PHT</span>

                            <input class="ann-input" type="datetime-local" name="ends_at" value="{{ old('ends_at', $toPhtInput($editing?->ends_at)) }}">

                        </label>

                        <div class="ann-field">

                            <span class="ann-label">After save</span>

                            @php $defaultSaveMode = $editing ? 'keep' : 'publish'; @endphp

                            <select class="ann-select" name="save_mode">

                                @if($editing)

                                    <option value="keep" @selected(old('save_mode', $defaultSaveMode) === 'keep')>Keep current status ({{ $editing->displayState() }})</option>

                                @endif

                                <option value="publish" @selected(old('save_mode', $defaultSaveMode) === 'publish')>Publish / schedule automatically</option>

                                <option value="draft" @selected(old('save_mode', $defaultSaveMode) === 'draft')>Save as draft</option>

                            </select>

                        </div>

                    </div>

                    <div class="ann-actions">

                        <button class="ann-btn primary" type="submit">{{ $editing ? 'Save changes' : 'Save announcement' }}</button>

                        <span class="text-[7px] leading-5 text-[#7b8691]">Published is the default. Future start times become scheduled automatically; choose Draft only when you want it hidden.</span>

                    </div>

                </form>

            </div>

        </section>



        <section class="ann-panel">

            <div class="ann-panel-head">

                <div>

                    <h2>Announcement schedule</h2>

                    <p>Draft, live, scheduled, paused, and ended notices remain traceable here.</p>

                </div>

                <span class="ann-badge">{{ number_format($announcements->total()) }} total</span>

            </div>



            @forelse($announcements as $announcement)

                @php

                    $state = strtolower($announcement->displayState());

                    $audiences = collect((array)$announcement->audiences)->map(fn($r)=>ucfirst($r))->implode(', ');

                @endphp

                <article class="ann-row">

                    <div class="ann-row-top">

                        <div class="min-w-0">

                            <div class="ann-name">{{ $announcement->title }}</div>

                            <div class="ann-sub">{{ \Illuminate\Support\Str::limit($announcement->message, 150) }}</div>

                        </div>

                        <span class="ann-badge {{ $state }}">{{ $announcement->displayState() }}</span>

                    </div>

                    <div class="ann-badges">

                        <span class="ann-badge">{{ ucfirst($announcement->severity) }}</span>

                        <span class="ann-badge">{{ $audiences }}</span>

                        @if($announcement->starts_at)

                            <span class="ann-badge">Start {{ $announcement->starts_at->copy()->timezone('Asia/Manila')->format('M j h:i A') }} PHT</span>

                        @endif

                        @if($announcement->ends_at)

                            <span class="ann-badge">End {{ $announcement->ends_at->copy()->timezone('Asia/Manila')->format('M j h:i A') }} PHT</span>

                        @endif

                    </div>

                    <div class="ann-row-actions" aria-label="Announcement actions">
                        <a class="ann-icon-action edit" href="{{ route('admin.announcements',['edit'=>$announcement->id]) }}" title="Edit announcement" aria-label="Edit announcement">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg>
                        </a>

                        @if($announcement->status === 'published')
                            <form method="POST" action="{{ route('admin.announcements.pause',$announcement) }}">
                                @csrf
                                <button class="ann-icon-action toggle" type="submit" title="Pause announcement" aria-label="Pause announcement">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 6v12"></path><path d="M15 6v12"></path></svg>
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.announcements.publish',$announcement) }}">
                                @csrf
                                <button class="ann-icon-action toggle" type="submit" title="Publish / resume announcement" aria-label="Publish or resume announcement">
                                    <svg viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M8 5.4v13.2c0 .8.9 1.3 1.6.9l9.7-6.6a1.1 1.1 0 0 0 0-1.8L9.6 4.5A1.1 1.1 0 0 0 8 5.4Z"></path></svg>
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.announcements.destroy',$announcement) }}" onsubmit="return confirm('Delete this announcement?')">
                            @csrf
                            @method('DELETE')
                            <button class="ann-icon-action delete" type="submit" title="Delete announcement" aria-label="Delete announcement">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="m6 7 1 13h10l1-13"></path><path d="M10 11v5"></path><path d="M14 11v5"></path></svg>
                            </button>
                        </form>
                    </div>

                </article>

            @empty

                <div class="ann-empty">No platform announcements have been created yet.</div>

            @endforelse



            @if($announcements->hasPages())

                <div class="border-t border-[#ECEFF2] bg-[#FAFBFC] px-3 py-2 text-[9px]">{{ $announcements->links() }}</div>

            @endif

        </section>

    </div>

</div>



<script>

    (() => {

        const input = document.querySelector('[data-announcement-image-input]');

        const preview = document.querySelector('[data-announcement-image-preview]');

        const remove = document.querySelector('[data-announcement-remove-image]');



        if (!input || !preview) return;



        let objectUrl = null;

        const originalMarkup = preview\.innerHTML;



        const clearObjectUrl = () => {

            if (objectUrl) {

                URL.revokeObjectURL(objectUrl);

                objectUrl = null;

            }

        };



        const restoreOriginal = () => {

            clearObjectUrl();

            preview\.innerHTML = originalMarkup;

        };



        input.addEventListener('change', () => {

            clearObjectUrl();



            const file = input.files?.[0];



            if (!file) {

                restoreOriginal();

                return;

            }



            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {

                input.value = '';

                restoreOriginal();

                return;

            }



            objectUrl = URL.createObjectURL(file);

            const image = document.createElement('img');

            image.src = objectUrl;

            image.alt = 'Announcement image preview';

            preview\.replaceChildren(image);



            if (remove) {

                remove.checked = false;

            }

        });



        remove?.addEventListener('change', () => {

            if (remove.checked) {

                input.value = '';

                clearObjectUrl();

                preview.innerHTML = '<span data-announcement-image-placeholder>Image will be removed</span>';

            } else {

                restoreOriginal();

            }

        });



        window\.addEventListener('beforeunload', clearObjectUrl, { once: true });

    })();



    (() => {
        const SELECT_SELECTOR = '.ann-select';

        function closeAll(except = null) {
            document.querySelectorAll('.ann-select-wrap.is-open').forEach(wrap => {
                if (wrap === except) return;
                wrap.classList.remove('is-open');
                wrap.querySelector('.ann-select-trigger')?.setAttribute('aria-expanded','false');
            });
        }

        function enhanceSelect(select) {
            if (!select || select.dataset.enhanced === '1') return;
            select.dataset.enhanced = '1';
            select.classList.add('ann-native-select');

            const wrap = document.createElement('div');
            wrap.className = 'ann-select-wrap';
            select.parentNode.insertBefore(wrap, select);
            wrap.appendChild(select);

            const trigger = document.createElement('button');
            trigger.type = 'button';
            trigger.className = 'ann-select-trigger';
            trigger.setAttribute('aria-haspopup','listbox');
            trigger.setAttribute('aria-expanded','false');
            trigger.innerHTML = '<span class="ann-select-trigger-label"></span><svg class="ann-select-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 10 5 5 5-5"></path></svg>';

            const menu = document.createElement('div');
            menu.className = 'ann-select-menu';
            menu.setAttribute('role','listbox');

            const sync = () => {
                const selected = select.options[select.selectedIndex] || select.options[0];
                trigger.querySelector('.ann-select-trigger-label').textContent = selected?.textContent?.trim() || 'Select';
                [...menu.querySelectorAll('.ann-select-option')].forEach((button, index) => {
                    const active = index === select.selectedIndex;
                    button.classList.toggle('is-selected',active);
                    button.setAttribute('aria-selected',active ? 'true' : 'false');
                });
            };

            [...select.options].forEach((option,index) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'ann-select-option';
                button.setAttribute('role','option');
                button.dataset.index = String(index);
                button.innerHTML = `<span>${option.textContent.trim()}</span><span class="ann-select-check"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m6 12 4 4 8-9"></path></svg></span>`;
                button.addEventListener('click',() => {
                    select.selectedIndex = index;
                    select.dispatchEvent(new Event('change',{bubbles:true}));
                    sync();
                    wrap.classList.remove('is-open');
                    trigger.setAttribute('aria-expanded','false');
                    trigger.focus();
                });
                menu.appendChild(button);
            });

            trigger.addEventListener('click',event => {
                event.preventDefault();
                event.stopPropagation();
                const willOpen = !wrap.classList.contains('is-open');
                closeAll(wrap);
                wrap.classList.toggle('is-open',willOpen);
                trigger.setAttribute('aria-expanded',willOpen ? 'true' : 'false');
                if (willOpen) menu.querySelector('.ann-select-option.is-selected')?.focus({preventScroll:true});
            });

            trigger.addEventListener('keydown',event => {
                if (!['ArrowDown','ArrowUp','Enter',' '].includes(event.key)) return;
                event.preventDefault();
                if (!wrap.classList.contains('is-open')) trigger.click();
            });

            menu.addEventListener('keydown',event => {
                const options = [...menu.querySelectorAll('.ann-select-option')];
                const current = options.indexOf(document.activeElement);
                if (event.key === 'Escape') {
                    event.preventDefault();
                    wrap.classList.remove('is-open');
                    trigger.setAttribute('aria-expanded','false');
                    trigger.focus();
                    return;
                }
                if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    const step = event.key === 'ArrowDown' ? 1 : -1;
                    const next = Math.max(0,Math.min(options.length-1,current + step));
                    options[next]?.focus();
                    return;
                }
                if ((event.key === 'Enter' || event.key === ' ') && current >= 0) {
                    event.preventDefault();
                    options[current].click();
                }
            });

            select.addEventListener('change',sync);
            wrap.appendChild(trigger);
            wrap.appendChild(menu);
            sync();
        }

        function initAnnouncementSelects() {
            document.querySelectorAll(SELECT_SELECTOR).forEach(enhanceSelect);
        }

        document.addEventListener('click',event => {
            if (!event.target.closest('.ann-select-wrap')) closeAll();
        });
        document.addEventListener('keydown',event => { if (event.key === 'Escape') closeAll(); });
        document.addEventListener('livewire:navigated',initAnnouncementSelects);
        initAnnouncementSelects();
    })();

</script>



@endsection
