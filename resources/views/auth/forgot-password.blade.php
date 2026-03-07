<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-foreground">Forgot password</h1>
        <p class="text-sm text-muted-foreground mt-1">Enter your email and we'll send a reset link</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Send Reset Link') }}
        </x-primary-button>

        <p class="text-center text-sm text-muted-foreground">
            <a href="{{ route('login') }}" class="text-primary hover:underline">← Back to login</a>
        </p>
    </form>
</x-guest-layout>