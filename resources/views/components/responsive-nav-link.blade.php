@props(['href', 'active' => false])

@php
    $classes = $active
        ? 'flex w-full items-start py-2 pe-4 ps-3 border-l-4 border-primary text-sm font-medium text-foreground bg-primary/5 focus:outline-none'
        : 'flex w-full items-start py-2 pe-4 ps-3 border-l-4 border-transparent text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-muted hover:border-muted focus:outline-none transition duration-150 ease-in-out';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>