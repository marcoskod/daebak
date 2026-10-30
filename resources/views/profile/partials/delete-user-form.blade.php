<section class="stack">
    <h2 style="font-size:20px;font-weight:800;color:var(--danger)">{{ t('account.delete_title') }}</h2>
    <p class="muted">{{ t('account.delete_text') }}</p>
    <form method="post" action="{{ route('profile.destroy') }}" class="stack" onsubmit="return confirm(@json(t('account.delete_text')));">
        @csrf @method('delete')
        <div class="field"><x-input-label for="delete_password" :value="t('auth.password')" /><x-text-input id="delete_password" name="password" type="password" autocomplete="current-password" /><x-input-error :messages="$errors->userDeletion->get('password')" /></div>
        <x-danger-button style="width:fit-content">{{ t('account.delete_confirm') }}</x-danger-button>
    </form>
</section>
