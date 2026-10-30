<section class="stack">
    <h2 style="font-size:20px;font-weight:800">{{ t('account.password_title') }}</h2>
    <form method="post" action="{{ route('password.update') }}" class="stack">
        @csrf @method('put')
        <div class="field"><x-input-label for="update_password_current_password" :value="t('account.current_password')" /><x-text-input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" /><x-input-error :messages="$errors->updatePassword->get('current_password')" /></div>
        <div class="field"><x-input-label for="update_password_password" :value="t('account.new_password')" /><x-text-input id="update_password_password" name="password" type="password" autocomplete="new-password" /><x-input-error :messages="$errors->updatePassword->get('password')" /></div>
        <div class="field"><x-input-label for="update_password_password_confirmation" :value="t('auth.confirm_password')" /><x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" /><x-input-error :messages="$errors->updatePassword->get('password_confirmation')" /></div>
        <div class="row"><x-primary-button>{{ t('account.save') }}</x-primary-button>@if (session('status') === 'password-updated')<span class="pill pill-ok"><i class="fa-solid fa-check"></i> {{ t('account.saved') }}</span>@endif</div>
    </form>
</section>
