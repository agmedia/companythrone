<div>
    <h1 class="h5 mb-3">{{ __('auth.verify_email_title') }}</h1>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <p class="mb-3">{{ __('auth.verify_email_text') }}</p>

    <form wire:submit.prevent="sendVerification" class="vstack gap-2 mb-3">
        <button class="btn btn-primary w-100" type="submit">{{ __('auth.resend_verification') }}</button>
    </form>

    <form wire:submit.prevent="logout" class="vstack">
        <button class="btn btn-outline-secondary w-100" type="submit">{{ __('Log out') }}</button>
    </form>

</div>