<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-default']) }}>
    {{ $slot }}
</button>