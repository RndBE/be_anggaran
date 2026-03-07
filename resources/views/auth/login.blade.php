<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-foreground">Welcome back</h1>
        <p class="text-sm text-muted-foreground mt-1">Sign in to your account</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-primary hover:underline" href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password"
                placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-input text-primary focus:ring-ring"
                name="remember">
            <label for="remember_me" class="ms-2 text-sm text-muted-foreground cursor-pointer">
                {{ __('Remember me') }}
            </label>
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Sign in') }}
        </x-primary-button>
    </form>
</x-guest-layout>