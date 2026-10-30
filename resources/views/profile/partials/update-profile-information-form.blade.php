<section class="stack">
    <h2 style="font-size:20px;font-weight:800">{{ t('account.profile_info') }}</h2>
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>
    <form method="post" action="{{ route('profile.update') }}" class="stack">
        @csrf @method('patch')
        <div class="field"><x-input-label for="name" :value="t('auth.name')" /><x-text-input id="name" name="name" type="text" :value="old('name', $user->name)" required autocomplete="name" /><x-input-error :messages="$errors->get('name')" /></div>
        <div class="field"><x-input-label for="email" :value="t('auth.email')" /><x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" /><x-input-error :messages="$errors->get('email')" />
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="muted" style="font-size:13px">{{ t('auth.verify_text') }} <button form="send-verification" class="btn btn-ghost btn-sm">{{ t('auth.resend') }}</button></p>
            @endif
        </div>
        <div class="row"><x-primary-button>{{ t('account.save') }}</x-primary-button>@if (session('status') === 'profile-updated')<span class="pill pill-ok"><i class="fa-solid fa-check"></i> {{ t('account.saved') }}</span>@endif</div>
    </form>
</section>
