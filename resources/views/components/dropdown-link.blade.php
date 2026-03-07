<a {{ $attributes->merge(['class' => 'flex w-full items-center px-3 py-2 text-sm text-foreground hover:bg-accent hover:text-accent-foreground cursor-pointer transition-colors']) }}>
    {{ $slot }}
</a>