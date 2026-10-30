<button type="button" id="chat-fab" class="chat-fab" aria-label="{{ t('chat.open') }}"><i class="fa-solid fa-comment-dots"></i></button>

<section id="chat-panel" class="chat-panel" role="dialog" aria-label="{{ t('chat.title') }}">
    <div class="chat-head">
        <div class="chat-avatar"><i class="fa-solid fa-robot"></i></div>
        <div style="flex:1;min-width:0">
            <strong>{{ site('chat.agent_name') }} · {{ site('brand.name') }}</strong>
            <small id="chat-sub">{{ t('chat.sub_ai') }}</small>
        </div>
        <button type="button" id="chat-close" class="btn btn-icon btn-sm" style="background:rgba(255,255,255,.18);color:inherit" aria-label="{{ t('chat.close') }}"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="chat-mode" role="tablist">
        <button type="button" data-chat-mode="ai" class="is-active" role="tab"><i class="fa-solid fa-wand-magic-sparkles"></i> {{ t('chat.mode_ai') }}</button>
        <button type="button" data-chat-mode="human" role="tab"><i class="fa-solid fa-headset"></i> {{ t('chat.mode_human') }}</button>
    </div>
    <div id="chat-body" class="chat-body" aria-live="polite"></div>
    <div id="chat-handoff" class="chat-handoff" hidden style="margin:0 12px 12px">
        <strong>{{ t('chat.handoff_title') }}</strong>
        <p class="muted" style="font-size:13px">{{ t('chat.handoff_text') }}</p>
        <form id="chat-handoff-form" class="stack">
            <input class="input" name="name" required maxlength="80" placeholder="{{ t('chat.name') }}" autocomplete="name" style="height:42px">
            <input class="input" type="email" name="email" required maxlength="190" placeholder="{{ t('chat.email') }}" autocomplete="email" style="height:42px">
            <button class="btn btn-primary btn-block" type="submit"><i class="fa-solid fa-headset"></i> {{ t('chat.handoff_button') }}</button>
        </form>
    </div>
    <div id="chat-quick" class="chat-quick">
        @foreach (tg('chat.quick') as $q)<button type="button">{{ $q }}</button>@endforeach
    </div>
    <form id="chat-form" class="chat-form" autocomplete="off">
        <input id="chat-input" type="text" maxlength="600" placeholder="{{ t('chat.placeholder') }}" aria-label="{{ t('chat.placeholder') }}">
        <button type="submit" class="btn btn-primary btn-icon" aria-label="{{ t('chat.send') }}"><i class="fa-solid fa-paper-plane"></i></button>
    </form>
</section>
