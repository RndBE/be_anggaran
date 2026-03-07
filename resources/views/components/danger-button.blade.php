<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-destructive']) }}>
    {{ $slot }}
</button>