<footer class="footer reveal">
    <div class="foot-grid">
        <div class="stack">
            <a href="{{ route('shop.home') }}" class="brand"><img src="{{ asset(site('brand.logo_mark')) }}" alt="" width="38" height="38"> {{ site('brand.name') }}</a>
            <p style="opacity:.85;font-size:14px;max-width:34ch">{{ t('footer.about') }}</p>
            <div class="socials">
                <a href="{{ site('social.instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="{{ site('social.tiktok') }}" target="_blank" rel="noopener" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                <a href="{{ site('social.youtube') }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                <a href="{{ site('social.pinterest') }}" target="_blank" rel="noopener" aria-label="Pinterest"><i class="fa-brands fa-pinterest-p"></i></a>
                <a href="{{ site('social.kakao') }}" target="_blank" rel="noopener" aria-label="KakaoTalk"><i class="fa-solid fa-comment"></i></a>
            </div>
        </div>
        <div>
            <h4>{{ t('footer.shop') }}</h4>
            <ul>
                <li><a href="{{ route('shop.catalogue') }}">{{ t('footer.all_products') }}</a></li>
                <li><a href="{{ route('shop.collections') }}">{{ t('nav.collections') }}</a></li>
                <li><a href="{{ route('pages.studio') }}">{{ t('nav.studio') }}</a></li>
                <li><a href="{{ route('pages.creators') }}">{{ t('nav.creators') }}</a></li>
                <li><a href="{{ route('blog.index') }}">{{ t('blog.nav') }}</a></li>
            </ul>
        </div>
        <div>
            <h4>{{ t('footer.help') }}</h4>
            <ul>
                <li><a href="{{ route('pages.how') }}">{{ t('nav.how') }}</a></li>
                <li><a href="{{ route('pages.shipping') }}">{{ t('footer.shipping') }}</a></li>
                <li><a href="{{ route('pages.size-guide') }}">{{ t('footer.size_guide') }}</a></li>
                <li><a href="{{ route('tracking.index') }}">{{ t('nav.tracking') }}</a></li>
                <li><a href="{{ route('pages.faq') }}">{{ t('footer.faq') }}</a></li>
                <li><a href="{{ route('pages.contact') }}">{{ t('footer.contact') }}</a></li>
            </ul>
        </div>
        <div>
            <h4>{{ t('footer.contact_title') }}</h4>
            <ul>
                <li><i class="fa-regular fa-envelope fa-fw"></i> <a href="mailto:{{ site('contact.email') }}">{{ site('contact.email') }}</a></li>
                <li><i class="fa-solid fa-phone fa-fw"></i> {{ site('contact.phone') }}</li>
                <li><i class="fa-solid fa-location-dot fa-fw"></i> {{ site('contact.address') }}</li>
                <li><i class="fa-regular fa-clock fa-fw"></i> {{ t(site('contact.hours_key')) }}</li>
            </ul>
        </div>
    </div>
    <div>
        <h4>{{ t('footer.fulfilled_by') }}</h4>
        <div class="row" style="gap:10px">
            @foreach (site('providers') as $p)
                <span class="pill" style="background:rgba(255,255,255,.12);color:inherit"><i class="{{ $p['icon'] }}"></i> {{ $p['name'] }}</span>
            @endforeach
        </div>
    </div>
    <div class="foot-bottom">
        <span>© {{ date('Y') }} {{ site('brand.legal_name') }} · {{ t('footer.rights') }}</span>
        <div class="pay-icons" aria-label="{{ t('footer.payments') }}"><i class="fa-brands fa-cc-visa"></i><i class="fa-brands fa-cc-mastercard"></i><i class="fa-brands fa-cc-paypal"></i><i class="fa-brands fa-apple-pay"></i><i class="fa-brands fa-google-pay"></i></div>
        <div class="row" style="gap:16px">
            <a href="{{ route('pages.cgv') }}">{{ t('footer.terms') }}</a>
            <a href="{{ route('pages.confidentialite') }}">{{ t('footer.privacy') }}</a>
            <a href="{{ route('pages.mentions-legales') }}">{{ t('footer.legal') }}</a>
        </div>
    </div>
</footer>
