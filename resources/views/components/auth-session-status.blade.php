@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'alert-success text-sm font-medium']) }}>
        {{ $status }}
    </div>
@endif