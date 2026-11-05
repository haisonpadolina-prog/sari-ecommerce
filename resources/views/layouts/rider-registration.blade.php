<!DOCTYPE html>
<html lang="en" class="light-mode">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f5f7f9">
    <title>@yield('title', 'SARI Rider Registration')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('styles')
</head>
<body class="sari-rider-registration @yield('body-class')">
    <header class="sari-rider-brand">
        <a href="{{ route('register') }}" aria-label="SARI Home — Register">
            <img src="{{ asset('images/sari-logo.png') }}" alt="SARI — Elevated Everyday" width="116" height="65">
        </a>
    </header>

    @yield('content')

    <style>
        :root{--rider-gold:#c77e00;--rider-gold-dark:#a96700;--rider-ink:#20242a;--rider-muted:#707b8a;--rider-line:#e1e6ec}
        html{color-scheme:light}
        body.sari-rider-registration{margin:0;min-height:100vh;background:#f5f7f9 url("{{ asset('images/login-bg.png') }}") center center/cover fixed no-repeat;color:#20242a;font-family:'Poppins',sans-serif}
        .sari-rider-brand{display:flex;justify-content:center;padding:18px 16px 0;position:relative;z-index:2}
        .sari-rider-brand a{display:block;line-height:0}
        .sari-rider-brand img{width:112px;height:auto;max-height:70px;object-fit:contain}
        body.sari-rider-registration .rider-page-shell{background:transparent!important;color:#20242a!important;min-height:calc(100vh - 87px)}
        .rider-page-shell main{padding-top:16px!important;padding-bottom:32px!important}
        .rider-page-shell main.max-w-\[1180px\]{max-width:1080px!important}
        .rider-page-shell .rider-form-card,.rider-page-shell .rider-provider-summary,.rider-page-shell .rider-provider-card{background:#fff!important;border:1px solid #e3e7ed!important;box-shadow:0 18px 44px rgba(20,34,52,.10)!important;border-radius:18px!important}
        .rider-page-shell .rider-form-card{padding:27px!important}
        .rider-page-shell .rider-provider-card:hover{transform:translateY(-2px);box-shadow:0 18px 40px rgba(24,34,51,.12)!important}
        .rider-page-shell .rider-control{min-height:44px!important;padding:9px 13px!important;border:1px solid #d4dce6!important;border-radius:10px!important;background:#fff!important;color:#20242a!important;font-family:inherit!important;font-size:14px!important}
        .rider-page-shell .rider-control:focus{border-color:#c77e00!important;box-shadow:0 0 0 3px rgba(199,126,0,.11)!important}
        .rider-page-shell .rider-label{font-size:13px!important;font-weight:600!important;margin-bottom:7px!important;color:#323c49!important}
        .rider-page-shell .rider-help{font-size:11px!important;line-height:1.55!important;color:#7d8797!important}
        .rider-page-shell .rider-section{border-color:#e5e8ee!important}
        .rider-page-shell h1{line-height:1.18!important;letter-spacing:-.035em!important}
        .rider-page-shell h2{line-height:1.25!important}
        .rider-page-shell button,.rider-page-shell a{transition:background-color .16s,border-color .16s,box-shadow .16s}
        .rider-page-shell button[type=submit],.rider-page-shell a[href*="register/rider/"]{font-size:12px!important}
        .rider-page-shell .rider-profile-zone{min-height:142px!important;background:#fbfcfd!important;border:1px dashed #c5ceda!important;border-radius:13px!important}
        .rider-page-shell .rider-profile-row{border-top:1px solid #e9edf2!important}
        .rider-page-shell .rider-location-list{background:#fff!important;border-color:#d5dce6!important}
        .rider-page-shell input[type=file]:focus-visible{outline:2px solid #c77e00;outline-offset:3px}
        .rider-page-shell input[type=checkbox]{accent-color:#c77e00}
        .rider-page-shell [class*="text-[8px]"]{font-size:11px!important}
        .rider-page-shell [class*="text-[9px]"]{font-size:11px!important}
        .rider-page-shell [class*="text-[10px]"]{font-size:12px!important}
        .rider-page-shell [class*="text-[11px]"]{font-size:13px!important}
        .rider-page-shell .rider-provider-card h3{font-size:17px!important}
        .rider-page-shell .rider-provider-summary h2{font-size:17px!important}
        .rider-page-shell p{overflow-wrap:anywhere}
        /* OTP email form: reuse the same clear background and white card */
        .rider-page-shell>main>section{border:1px solid #e3e7ed!important;background:#fff!important;box-shadow:0 18px 44px rgba(20,34,52,.11)!important;border-radius:18px!important}
        .sari-rider-toast{position:fixed;z-index:99999;right:18px;bottom:18px;width:min(390px,calc(100vw - 36px));padding:14px 17px;border-radius:12px;border:1px solid #f0d1d1;background:#fff;color:#8b2323;box-shadow:0 15px 46px rgba(26,32,40,.17);font-size:13px;font-weight:600;line-height:1.55;display:none}
        .sari-rider-toast[data-open=true]{display:block}
        @media(max-width:768px){.sari-rider-brand{padding-top:12px}.sari-rider-brand img{width:92px}.rider-page-shell main{padding-top:13px!important;padding-bottom:23px!important}.rider-page-shell .rider-form-card{padding:17px!important}.rider-page-shell .rider-provider-summary{position:static!important}.rider-page-shell h1{font-size:27px!important}.rider-page-shell .rider-label{font-size:12px!important}.sari-rider-toast{right:12px;bottom:12px;width:calc(100vw - 24px)}}
        @media(prefers-reduced-motion:reduce){.rider-page-shell *, .sari-rider-toast{transition-duration:0s!important;animation-duration:0s!important}}
    </style>

    <div id="sariRiderToast" class="sari-rider-toast" role="alert" aria-live="assertive"></div>
    <script>
    (function(){
      'use strict';
      const soundUrl = @json(asset('sounds/buyer/notification-error.wav'));
      let lastToastAt=0;
      let toastTimer;
      function showError(message,playSound){
        if(document.body.classList.contains('rider-application-page'))return;
        const toast=document.getElementById('sariRiderToast');
        if(!toast||!message)return;
        toast.textContent=String(message);
        toast.dataset.open='true';
        clearTimeout(toastTimer);
        toastTimer=setTimeout(()=>{toast.dataset.open='false'},4400);
        if(playSound){
          try{const audio=new Audio(soundUrl);audio.volume=.58;const p=audio.play();if(p&&typeof p.catch==='function')p.catch(()=>{});}catch(_error){}
        }
      }
      document.addEventListener('invalid',function(event){
        if(!event.target.closest('form'))return;
        const now=Date.now();if(now-lastToastAt<1100)return;lastToastAt=now;
        const label=event.target.closest('div')?.querySelector('label');
        const name=label?.textContent?.trim()||event.target.getAttribute('aria-label')||event.target.name||'Required field';
        showError(name+': '+(event.target.validationMessage||'Please check your entry.'),true);
      },true);
      @if($errors->any())
      document.addEventListener('DOMContentLoaded',function(){showError(@json($errors->first()),false)});
      @endif
    })();
    </script>
    @stack('scripts')
</body>
</html>
