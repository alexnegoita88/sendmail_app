<?php

use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public string $code = '';

    public function mount()
    {
        if (!Session::has('auth.2fa.user_id')) {
            return $this->redirect(route('login'), navigate: true);
        }
    }

    public function verify()
    {
        $this->validate([
            'code' => ['required', 'string', 'min:6', 'max:6'],
        ]);

        $userId = Session::get('auth.2fa.user_id');
        $user = User::findOrFail($userId);

        if (
            $user->two_factor_code !== $this->code ||
            now()->isAfter($user->two_factor_expires_at)
        ) {

            $this->addError('code', 'The code is invalid or has expired.');
            return;
        }

        // Code is valid
        $user->resetTwoFactorCode();

        Auth::login($user, Session::get('auth.2fa.remember', false));

        Session::forget(['auth.2fa.user_id', 'auth.2fa.remember']);
        Session::regenerate();

        return $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    public function resend()
    {
        $userId = Session::get('auth.2fa.user_id');
        if (!$userId)
            return;

        $user = User::findOrFail($userId);
        $user->generateTwoFactorCode();

        Mail::to($user->email)->send(
            new TwoFactorCodeMail($user->two_factor_code)
        );

        session()->flash('status', 'A new code has been sent to your email.');
    }
}; ?>

<div>
    <div class="sm:fixed sm:top-0 sm:right-0 p-6 text-end z-10">
        <button wire:click="resend"
            class="font-semibold text-gray-600 hover:text-gray-900 focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
            Resend Code
        </button>
    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-4 text-sm text-gray-600">
            {{ __('Please enter the 6-digit code we just emailed to you. If you didn\'t receive it, you can request a new one.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="verify">
            <div>
                <x-input-label for="code" :value="__('Verification Code')" />
                <x-text-input wire:model="code" id="code" class="block mt-1 w-full" type="text" name="code" required
                    autofocus />
                <x-input-error :messages="$errors->get('code')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ __('Verify Code') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</div>