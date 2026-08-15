@props(['as' => 'div', 'fluid' => false])

<{{ $as }} {{ $attributes->class([
    'mx-auto w-full px-5 sm:px-8 lg:px-10 xl:px-14',
    'max-w-[90rem]' => ! $fluid,
]) }}>
    {{ $slot }}
</{{ $as }}>
