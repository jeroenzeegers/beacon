<?php

use App\Events\NewUserRegistered;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        // Create a default team for the new user
        $user->createTeam($user->name . "'s Team");

        // Broadcast new user event for admin dashboard
        broadcast(new NewUserRegistered($user));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-white">Create your account</h1>
        <p class="text-slate-400 mt-2">Start your 14-day free trial. No credit card required.</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <!-- Name -->
        <flux:field>
            <flux:label>{{ __('Name') }}</flux:label>
            <flux:input wire:model="name" type="text" placeholder="John Doe" autofocus autocomplete="name" />
            <flux:error name="name" />
        </flux:field>

        <!-- Email Address -->
        <flux:field>
            <flux:label>{{ __('Email') }}</flux:label>
            <flux:input wire:model="email" type="email" placeholder="you@example.com" autocomplete="username" />
            <flux:error name="email" />
        </flux:field>

        <!-- Password -->
        <flux:field>
            <flux:label>{{ __('Password') }}</flux:label>
            <flux:input wire:model="password" type="password" placeholder="••••••••" autocomplete="new-password" />
            <flux:error name="password" />
        </flux:field>

        <!-- Confirm Password -->
        <flux:field>
            <flux:label>{{ __('Confirm Password') }}</flux:label>
            <flux:input wire:model="password_confirmation" type="password" placeholder="••••••••" autocomplete="new-password" />
            <flux:error name="password_confirmation" />
        </flux:field>

        <!-- Submit Button -->
        <flux:button type="submit" variant="primary" class="w-full mt-2">
            {{ __('Create account') }}
        </flux:button>

        <!-- Terms -->
        <p class="text-center text-slate-500 text-xs">
            By creating an account, you agree to our
            <flux:link href="#">Terms of Service</flux:link>
            and
            <flux:link href="#">Privacy Policy</flux:link>
        </p>

        <!-- Login Link -->
        <p class="text-center text-slate-400 text-sm pt-2">
            Already have an account?
            <flux:link href="{{ route('login') }}" wire:navigate>
                Sign in
            </flux:link>
        </p>
    </form>
</div>
