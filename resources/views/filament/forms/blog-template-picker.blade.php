@php
    $statePath = $getStatePath();
    $current = \App\Models\BlogPost::resolveTemplate($getState());
    $svgs = [
        'classic' => '<rect class="s-ink" x="8" y="6" width="34" height="4" rx="2"/><rect class="s-soft" x="8" y="13" width="26" height="3" rx="1.5"/><rect class="s-blush" x="8" y="20" width="64" height="14" rx="3"/><rect class="s-card" x="8" y="38" width="16" height="14" rx="2"/><rect class="s-ink2" x="28" y="39" width="44" height="3" rx="1.5"/><rect class="s-ink2" x="28" y="45" width="44" height="3" rx="1.5"/>',
        'magazine' => '<rect class="s-blush" x="0" y="0" width="80" height="22"/><rect class="s-ink" x="22" y="26" width="36" height="4" rx="2"/><text class="s-drop" x="8" y="48">A</text><rect class="s-ink2" x="22" y="36" width="50" height="3" rx="1.5"/><rect class="s-ink2" x="22" y="42" width="50" height="3" rx="1.5"/><rect class="s-ink2" x="8" y="49" width="64" height="3" rx="1.5"/>',
        'sidebar-toc' => '<rect class="s-ink" x="6" y="6" width="40" height="4" rx="2"/><rect class="s-blush" x="6" y="13" width="46" height="12" rx="3"/><rect class="s-ink2" x="6" y="29" width="46" height="3" rx="1.5"/><rect class="s-ink2" x="6" y="35" width="46" height="3" rx="1.5"/><rect class="s-ink2" x="6" y="41" width="46" height="3" rx="1.5"/><rect class="s-brand" x="58" y="6" width="2" height="30"/><rect class="s-soft" x="62" y="8" width="14" height="3" rx="1.5"/><rect class="s-soft" x="62" y="15" width="14" height="3" rx="1.5"/><rect class="s-soft" x="62" y="22" width="14" height="3" rx="1.5"/>',
        'centered-minimal' => '<rect class="s-ink" x="24" y="8" width="32" height="4" rx="2"/><rect class="s-soft" x="28" y="15" width="24" height="3" rx="1.5"/><rect class="s-ink2" x="24" y="24" width="32" height="3" rx="1.5"/><rect class="s-ink2" x="24" y="30" width="32" height="3" rx="1.5"/><rect class="s-ink2" x="24" y="36" width="32" height="3" rx="1.5"/><rect class="s-ink2" x="24" y="42" width="22" height="3" rx="1.5"/>',
        'hero-overlay' => '<rect class="s-dark" x="0" y="0" width="80" height="34"/><rect class="s-white" x="8" y="20" width="40" height="4" rx="2"/><rect class="s-white2" x="8" y="27" width="28" height="3" rx="1.5"/><rect class="s-ink2" x="8" y="39" width="64" height="3" rx="1.5"/><rect class="s-ink2" x="8" y="45" width="64" height="3" rx="1.5"/>',
        'split' => '<rect class="s-blush" x="6" y="6" width="30" height="44" rx="4"/><rect class="s-ink" x="42" y="8" width="30" height="4" rx="2"/><rect class="s-ink2" x="42" y="18" width="30" height="3" rx="1.5"/><rect class="s-ink2" x="42" y="24" width="30" height="3" rx="1.5"/><rect class="s-ink2" x="42" y="30" width="30" height="3" rx="1.5"/><rect class="s-ink2" x="42" y="36" width="20" height="3" rx="1.5"/>',
        'longform-numbered' => '<text class="s-num" x="6" y="20">01</text><rect class="s-ink2" x="30" y="9" width="42" height="3" rx="1.5"/><rect class="s-ink2" x="30" y="15" width="42" height="3" rx="1.5"/><text class="s-num" x="6" y="42">02</text><rect class="s-ink2" x="30" y="31" width="42" height="3" rx="1.5"/><rect class="s-ink2" x="30" y="37" width="42" height="3" rx="1.5"/><rect class="s-soft" x="6" y="24" width="66" height="1.5"/>',
        'cards' => '<rect class="s-card s-edge" x="6" y="6" width="68" height="20" rx="5"/><rect class="s-ink" x="12" y="11" width="26" height="3" rx="1.5"/><rect class="s-ink2" x="12" y="18" width="52" height="3" rx="1.5"/><rect class="s-card s-edge" x="6" y="30" width="68" height="20" rx="5"/><rect class="s-ink" x="12" y="35" width="26" height="3" rx="1.5"/><rect class="s-ink2" x="12" y="42" width="52" height="3" rx="1.5"/>',
        'timeline' => '<rect class="s-soft" x="11" y="6" width="2" height="44"/><circle class="s-brand" cx="12" cy="12" r="4"/><circle class="s-brand" cx="12" cy="30" r="4"/><circle class="s-brand" cx="12" cy="46" r="4"/><rect class="s-ink" x="22" y="9" width="30" height="3" rx="1.5"/><rect class="s-ink2" x="22" y="15" width="48" height="3" rx="1.5"/><rect class="s-ink" x="22" y="27" width="30" height="3" rx="1.5"/><rect class="s-ink2" x="22" y="33" width="48" height="3" rx="1.5"/><rect class="s-ink2" x="22" y="43" width="40" height="3" rx="1.5"/>',
        'gallery-showcase' => '<rect class="s-blush" x="0" y="0" width="80" height="26"/><rect class="s-ink" x="22" y="30" width="36" height="3" rx="1.5"/><rect class="s-card s-edge" x="6" y="37" width="20" height="16" rx="3"/><rect class="s-card s-edge" x="30" y="37" width="20" height="16" rx="3"/><rect class="s-card s-edge" x="54" y="37" width="20" height="16" rx="3"/>',
    ];
@endphp
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <fieldset class="dbk-tplpick" x-data="{ state: $wire.$entangle('{{ $statePath }}') }">
        <legend class="sr-only">Blog post template</legend>
        <div class="dbk-tplpick-grid">
            @foreach (config('blog_templates.templates') as $key => $tpl)
                <label class="dbk-tplcard" wire:key="tpl-{{ $key }}">
                    <input type="radio" class="dbk-tplcard-input" name="{{ $statePath }}-radio" value="{{ $key }}" x-model="state" @checked($current === $key)>
                    <span class="dbk-tplcard-box">
                        <span class="dbk-tplcard-check" aria-hidden="true"><svg viewBox="0 0 16 16" width="14" height="14"><path d="M3 8.5l3 3 7-7" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <svg class="dbk-tplcard-mock" viewBox="0 0 80 56" role="img" aria-hidden="true" focusable="false">{!! $svgs[$key] ?? '' !!}</svg>
                        <span class="dbk-tplcard-name">{{ \App\Support\I18n::get("blog_templates.$key.name", [], 'en') }}</span>
                        <span class="dbk-tplcard-desc">{{ \App\Support\I18n::get("blog_templates.$key.desc", [], 'en') }}</span>
                    </span>
                </label>
            @endforeach
        </div>
    </fieldset>
</x-dynamic-component>
