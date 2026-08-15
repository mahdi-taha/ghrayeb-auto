@php
    $errorLocale = request()->hasSession() ? request()->session()->get('locale') : null;
    if (in_array($errorLocale, ['en', 'ar'], true)) {
        app()->setLocale($errorLocale);
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>@yield('title') | {{ config('app.name', 'Automotive Service') }}</title>
        <style>
            :root { color-scheme: light; font-family: Arial, sans-serif; }
            * { box-sizing: border-box; }
            body { margin: 0; background: #f2f3f3; color: #111315; }
            main { min-height: 100vh; display: grid; place-items: center; padding: 2rem 1rem; }
            .error-panel { width: min(100%, 42rem); border-top: .25rem solid #d40000; background: #fff; padding: clamp(2rem, 7vw, 4.5rem); text-align: center; box-shadow: 0 1.5rem 4rem rgba(17, 19, 21, .12); }
            .logo { display: block; width: min(16rem, 72vw); max-height: 5rem; object-fit: contain; margin: 0 auto 2rem; }
            .code { margin: 0; color: #b80000; font-size: .8rem; font-weight: 800; letter-spacing: .18em; }
            h1 { margin: .75rem 0 0; font-size: clamp(2rem, 7vw, 3.5rem); line-height: 1.05; }
            .message { margin: 1.25rem auto 0; max-width: 32rem; color: #586069; font-size: 1rem; line-height: 1.75; }
            .button { display: inline-flex; min-height: 3rem; align-items: center; justify-content: center; margin-top: 2rem; padding: .75rem 1.35rem; background: #d40000; color: #fff; font-weight: 800; text-decoration: none; }
            .button:focus-visible { outline: .2rem solid #111315; outline-offset: .2rem; }
        </style>
    </head>
    <body>
        <main>
            <section class="error-panel" aria-labelledby="error-heading">
                <img class="logo" src="{{ asset('images/logo.jpg') }}" alt="{{ config('app.name', 'Automotive Service') }}">
                <p class="code">@yield('code')</p>
                <h1 id="error-heading">@yield('heading')</h1>
                <p class="message">@yield('message')</p>
                <a class="button" href="{{ url('/') }}">{{ __('site.back_home') }}</a>
            </section>
        </main>
    </body>
</html>
