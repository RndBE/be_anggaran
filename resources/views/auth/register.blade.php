<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-foreground">Create an account</h1>
        <p class="text-sm text-muted-foreground mt-1">Enter your details to register</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div class="form-group">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                placeholder="Full name" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="form-group">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password"
                placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="form-group">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Create Account') }}
        </x-primary-button>

        <p class="text-center text-sm text-muted-foreground">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary hover:underline font-medium">Sign in</a>
        </p>
    </form>
</x-guest-layout>