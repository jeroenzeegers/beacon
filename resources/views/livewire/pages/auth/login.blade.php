<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-white">Welcome back</h1>
        <p class="text-slate-400 mt-2">Sign in to your account to continue</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <flux:field>
            <flux:label>{{ __('Email') }}</flux:label>
            <flux:input wire:model="form.email" type="email" placeholder="you@example.com" autofocus autocomplete="username" />
            <flux:error name="form.email" />
        </flux:field>

        <!-- Password -->
        <flux:field>
            <flux:label>{{ __('Password') }}</flux:label>
            <flux:input wire:model="form.password" type="password" placeholder="••••••••" autocomplete="current-password" />
            <flux:error name="form.password" />
        </flux:field>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between">
            <flux:checkbox wire:model="form.remember" label="{{ __('Remember me') }}" />

            @if (Route::has('password.request'))
                <flux:link href="{{ route('password.request') }}" wire:navigate class="text-sm">
                    {{ __('Forgot password?') }}
                </flux:link>
            @endif
        </div>

        <!-- Submit Button -->
        <flux:button type="submit" variant="primary" class="w-full">
            {{ __('Sign in') }}
        </flux:button>

        <!-- Register Link -->
        <p class="text-center text-slate-400 text-sm">
            Don't have an account?
            <flux:link href="{{ route('register') }}" wire:navigate>
                Start free trial
            </flux:link>
        </p>
    </form>
</div>
