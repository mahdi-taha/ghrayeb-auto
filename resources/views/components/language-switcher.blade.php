@props(['mobile' => false, 'dark' => false])

<div {{ $attributes->class([
    'inline-flex items-center gap-2 text-xs font-extrabold tracking-[0.12em]',
    'justify-center border border-line px-4 py-3' => $mobile,
]) }} aria-label="{{ __('site.language') }}">
    <a href="{{ route('locale.switch', 'en') }}" @if(app()->isLocale('en')) aria-current="page" @endif class="transition-colors {{ $dark ? (app()->isLocale('en') ? 'text-white' : 'text-white/45 hover:text-white') : (app()->isLocale('en') ? 'text-ink' : 'text-steel hover:text-ink') }}">EN</a>
    <span class="{{ $dark ? 'text-white/20' : 'text-line' }}" aria-hidden="true">|</span>
    <a href="{{ route('locale.switch', 'ar') }}" @if(app()->isLocale('ar')) aria-current="page" @endif class="transition-colors {{ $dark ? (app()->isLocale('ar') ? 'text-white' : 'text-white/45 hover:text-white') : (app()->isLocale('ar') ? 'text-ink' : 'text-steel hover:text-ink') }}">AR</a>
</div>
