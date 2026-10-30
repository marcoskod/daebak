/**
 * Daebak — interactions front (vanilla JS, aucune dépendance).
 * Config injectée par le layout : window.DAEBAK = { locale, currency, rates, symbols, decimals, csrf, i18n, routes, shipping }
 */
(function () {
    'use strict';
    var CFG = window.DAEBAK || {};
    var $ = function (s, r) { return (r || document).querySelector(s); };
    var $$ = function (s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); };
    var store = {
        get: function (k, d) { try { var v = localStorage.getItem(k); return v === null ? d : JSON.parse(v); } catch (e) { return d; } },
        set: function (k, v) { try { localStorage.setItem(k, JSON.stringify(v)); } catch (e) { /* stockage indisponible */ } }
    };

    /* ---------- Traductions & monnaie ---------- */
    function t(key, vars) {
        var s = (CFG.i18n && CFG.i18n[key]) || key;
        if (vars) Object.keys(vars).forEach(function (k) { s = s.split(':' + k).join(vars[k]); });
        return s;
    }
    function money(usd) {
        var c = CFG.currency || 'USD';
        var v = usd * ((CFG.rates || {})[c] || 1);
        if (c === 'KRW') v = Math.round(v / 100) * 100;
        var dec = (CFG.decimals || {})[c]; dec = dec === undefined ? 2 : dec;
        var sym = (CFG.symbols || {})[c] || c + ' ';
        var n = v.toFixed(dec).replace(/\B(?=(\d{3})+(?!\d))/g, CFG.locale === 'fr' ? ' ' : ',');
        if (CFG.locale === 'fr') n = n.replace('.', ',');
        return (c === 'EUR' && CFG.locale === 'fr') ? n + ' ' + sym : sym + n;
    }
    function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }

    /* ---------- Barre de progression ---------- */
    var bar = $('#top-progress'), barTimer, barVal = 0, pending = 0;
    var Progress = {
        start: function () {
            pending++; if (!bar) return; clearInterval(barTimer); bar.style.opacity = 1; barVal = Math.max(barVal, 8); bar.style.width = barVal + '%';
            barTimer = setInterval(function () { barVal += (90 - barVal) * 0.08; bar.style.width = barVal + '%'; }, 200);
        },
        done: function () {
            pending = Math.max(0, pending - 1); if (!bar || pending) return; clearInterval(barTimer); bar.style.width = '100%';
            setTimeout(function () { bar.style.opacity = 0; setTimeout(function () { bar.style.width = '0'; barVal = 0; }, 300); }, 200);
        }
    };

    /* ---------- Toasts ---------- */
    function toast(message, type, ms) {
        var root = $('#toast-root'); if (!root) return;
        type = type || 'success';
        var icon = type === 'error' ? 'fa-circle-exclamation' : (type === 'info' ? 'fa-circle-info' : 'fa-circle-check');
        var el = document.createElement('div');
        el.className = 'toast ' + type; el.setAttribute('role', type === 'error' ? 'alert' : 'status');
        el.innerHTML = '<i class="fa-solid ' + icon + '"></i><span>' + esc(message) + '</span><button class="t-close" aria-label="' + esc(t('js.close')) + '"><i class="fa-solid fa-xmark" style="color:inherit"></i></button>';
        root.appendChild(el);
        var close = function () { el.classList.add('leaving'); setTimeout(function () { el.remove(); }, 300); };
        el.querySelector('.t-close').addEventListener('click', close);
        setTimeout(close, ms || 3600);
    }

    /* ---------- HTTP ---------- */
    function http(url, opts) {
        opts = opts || {}; Progress.start();
        var headers = { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CFG.csrf };
        var body = opts.body;
        if (body && !(body instanceof FormData)) { headers['Content-Type'] = 'application/json'; body = JSON.stringify(body); }
        return fetch(url, { method: opts.method || 'GET', headers: headers, body: body, credentials: 'same-origin' })
            .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { return { ok: r.ok, status: r.status, data: j }; }); })
            .catch(function () { return { ok: false, status: 0, data: { message: t('js.network_error') } }; })
            .then(function (res) { Progress.done(); return res; });
    }
    var post = function (url, body) { return http(url, { method: 'POST', body: body || {} }); };

    window.Daebak = { t: t, money: money, toast: toast, http: http, post: post, progress: Progress, store: store, esc: esc };

    /* ---------- Navigation : progress sur liens internes ---------- */
    document.addEventListener('click', function (e) {
        var a = e.target.closest('a[href]');
        if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || a.target === '_blank' || a.hasAttribute('download')) return;
        var href = a.getAttribute('href');
        if (!href || href.charAt(0) === '#' || /^(mailto:|tel:|javascript:)/.test(href)) return;
        if (a.host !== location.host) return;
        Progress.start();
    });
    window.addEventListener('pageshow', function () { pending = 0; Progress.done(); });

    /* ---------- Thème ---------- */
    var root = document.documentElement;
    function applyTheme(m) { root.setAttribute('data-theme', m); store.set('daebak-theme', m); $$('[data-theme-toggle]').forEach(function (b) { b.setAttribute('aria-pressed', m === 'dark'); }); }
    $$('[data-theme-toggle]').forEach(function (b) {
        b.addEventListener('click', function () { applyTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark'); });
    });

    /* ---------- Dropdowns ---------- */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-dd-toggle]');
        $$('.dd.open').forEach(function (d) { if (!btn || d !== btn.closest('.dd')) d.classList.remove('open'); });
        if (btn) { var dd = btn.closest('.dd'); dd.classList.toggle('open'); btn.setAttribute('aria-expanded', dd.classList.contains('open')); }
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { $$('.dd.open').forEach(function (d) { d.classList.remove('open'); }); closeDrawer(); closeModals(); } });

    /* ---------- Sidebar responsive ---------- */
    var side = $('#sidebar'), backdrop = $('#drawer-backdrop');
    function openDrawer() { if (!side) return; side.classList.add('open'); backdrop && backdrop.classList.add('open'); document.body.style.overflow = 'hidden'; $('[data-drawer-close]', side) && $('[data-drawer-close]', side).focus(); }
    function closeDrawer() { if (!side) return; side.classList.remove('open'); backdrop && backdrop.classList.remove('open'); document.body.style.overflow = ''; }
    $$('[data-drawer-open]').forEach(function (b) { b.addEventListener('click', openDrawer); });
    $$('[data-drawer-close]').forEach(function (b) { b.addEventListener('click', closeDrawer); });
    backdrop && backdrop.addEventListener('click', closeDrawer);
    if (side) $$('a.nav-link', side).forEach(function (a) { a.addEventListener('click', closeDrawer); });

    /* ---------- Modales ---------- */
    function openModal(id) { var m = document.getElementById(id); if (m) { m.classList.add('open'); document.body.style.overflow = 'hidden'; } }
    function closeModals() { $$('.modal.open').forEach(function (m) { m.classList.remove('open'); }); if (!(side && side.classList.contains('open'))) document.body.style.overflow = ''; }
    document.addEventListener('click', function (e) {
        var o = e.target.closest('[data-modal-open]'); if (o) { e.preventDefault(); openModal(o.getAttribute('data-modal-open')); }
        if (e.target.closest('[data-modal-close]') || (e.target.classList && e.target.classList.contains('modal'))) closeModals();
    });

    /* ---------- Reveal au scroll ---------- */
    var io = 'IntersectionObserver' in window ? new IntersectionObserver(function (es) { es.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); } }); }, { threshold: 0.12 }) : null;
    function bindReveal(r) { $$('.reveal', r).forEach(function (el) { if (el.dataset.rv) return; el.dataset.rv = 1; io ? io.observe(el) : el.classList.add('is-visible'); }); }
    bindReveal(document);

    /* ---------- Compteurs animés ---------- */
    $$('[data-count]').forEach(function (el) {
        var target = parseFloat(el.getAttribute('data-count')), suffix = el.getAttribute('data-suffix') || '', done = false;
        var run = function () { if (done) return; done = true; var s = null; (function step(ts) { s = s || ts; var p = Math.min((ts - s) / 1100, 1); el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))).toLocaleString(CFG.locale) + suffix; if (p < 1) requestAnimationFrame(step); })(performance.now()); };
        if (io) { var o = new IntersectionObserver(function (es) { if (es[0].isIntersecting) { run(); o.disconnect(); } }); o.observe(el); } else run();
    });

    /* ---------- Rails (carrousels) ---------- */
    $$('[data-rail-prev],[data-rail-next]').forEach(function (b) {
        b.addEventListener('click', function () { var r = document.getElementById(b.getAttribute('data-rail-prev') || b.getAttribute('data-rail-next')); if (r) r.scrollBy({ left: (b.hasAttribute('data-rail-prev') ? -1 : 1) * r.clientWidth * 0.8, behavior: 'smooth' }); });
    });

    /* ---------- Wishlist (localStorage) ---------- */
    var wish = store.get('daebak-wishlist', []);
    function paintWish() {
        $$('[data-wish]').forEach(function (b) { var on = wish.indexOf(parseInt(b.getAttribute('data-wish'), 10)) > -1; b.classList.toggle('is-on', on); b.setAttribute('aria-pressed', on); var i = b.querySelector('i'); if (i) i.className = (on ? 'fa-solid' : 'fa-regular') + ' fa-heart'; });
        $$('[data-wish-count]').forEach(function (c) { c.textContent = wish.length; c.style.display = wish.length ? '' : 'none'; });
    }
    document.addEventListener('click', function (e) {
        var b = e.target.closest('[data-wish]'); if (!b) return; e.preventDefault(); e.stopPropagation();
        var id = parseInt(b.getAttribute('data-wish'), 10), i = wish.indexOf(id);
        if (i > -1) { wish.splice(i, 1); toast(t('js.wish_removed'), 'info'); } else { wish.push(id); toast(t('js.wish_added'), 'success'); }
        store.set('daebak-wishlist', wish); paintWish();
        var holder = $('#wishlist-grid'); if (holder && i > -1) { var card = b.closest('.pcard'); card && card.remove(); if (!$('.pcard', holder)) loadWishlist(); }
    });
    function loadWishlist() {
        var holder = $('#wishlist-grid'); if (!holder) return;
        if (!wish.length) { holder.innerHTML = $('#wishlist-empty').innerHTML; return; }
        http(CFG.routes.wishlist + '?ids=' + wish.join(',')).then(function (r) { holder.innerHTML = r.data.html || $('#wishlist-empty').innerHTML; bindReveal(holder); paintWish(); });
    }
    paintWish(); loadWishlist();

    /* ---------- Panier ---------- */
    function setCartCount(n) { $$('[data-cart-count]').forEach(function (c) { c.textContent = n; c.style.display = n > 0 ? '' : 'none'; }); }
    document.addEventListener('click', function (e) {
        var b = e.target.closest('[data-add-to-cart]'); if (!b) return; e.preventDefault(); e.stopPropagation();
        if (b.getAttribute('data-needs-options') === '1') { location.href = b.getAttribute('data-url'); return; }
        addToCart(b, parseInt(b.getAttribute('data-product-id'), 10), {});
    });
    function addToCart(btn, id, opts, then) {
        var html = btn && btn.innerHTML; if (btn) { btn.classList.add('is-loading'); btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'; }
        return post(CFG.routes.cartAdd, Object.assign({ product_id: id, qty: 1 }, opts)).then(function (r) {
            if (btn) { btn.classList.remove('is-loading'); btn.innerHTML = html; }
            if (r.ok && r.data.success) { setCartCount(r.data.cart_count); toast(r.data.message || t('js.added_to_cart')); then && then(r.data); }
            else toast((r.data && r.data.message) || t('js.error'), 'error');
        });
    }
    /* Page panier */
    var cartRoot = $('#cart-root');
    if (cartRoot) {
        cartRoot.addEventListener('click', function (e) {
            var b = e.target.closest('[data-qty-step],[data-remove]'); if (!b) return;
            var line = b.closest('[data-line]'), key = line.getAttribute('data-line'), input = $('input', line), qty = parseInt(input.value, 10) || 1;
            if (b.hasAttribute('data-remove')) qty = 0; else qty = Math.max(0, Math.min(20, qty + parseInt(b.getAttribute('data-qty-step'), 10)));
            post(CFG.routes.cartUpdate, { key: key, qty: qty }).then(function (r) {
                if (!r.ok) return toast((r.data && r.data.message) || t('js.error'), 'error');
                setCartCount(r.data.cart_count);
                if (r.data.html) { cartRoot.innerHTML = r.data.html; bindReveal(cartRoot); }
                if (qty === 0) toast(t('js.removed_from_cart'), 'info');
            });
        });
    }

    /* ---------- Formulaires AJAX génériques ---------- */
    document.addEventListener('submit', function (e) {
        var f = e.target.closest('form[data-ajax]'); if (!f) return; e.preventDefault();
        var btn = $('[type=submit]', f), html = btn && btn.innerHTML;
        $$('.err', f).forEach(function (x) { x.remove(); }); $$('.invalid', f).forEach(function (x) { x.classList.remove('invalid'); });
        if (btn) { btn.classList.add('is-loading'); btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'; }
        http(f.action, { method: 'POST', body: new FormData(f) }).then(function (r) {
            if (btn) { btn.classList.remove('is-loading'); btn.innerHTML = html; }
            if (r.ok && r.data.success !== false) {
                toast(r.data.message || t('js.sent')); if (f.hasAttribute('data-reset')) f.reset();
                if (r.data.redirect_url) location.href = r.data.redirect_url;
            } else if (r.status === 422 && r.data.errors) {
                Object.keys(r.data.errors).forEach(function (k) { var el = f.querySelector('[name="' + k + '"]'); if (el) { el.classList.add('invalid'); var p = document.createElement('div'); p.className = 'err'; p.textContent = r.data.errors[k][0]; el.closest('.field') ? el.closest('.field').appendChild(p) : el.after(p); } });
                toast(t('js.check_form'), 'error');
            } else toast((r.data && r.data.message) || t('js.error'), 'error');
        });
    });

    /* ---------- Recherche suggestions ---------- */
    $$('[data-search]').forEach(function (inp) {
        var pop = inp.closest('.search-pill').querySelector('.search-pop'), tm;
        inp.addEventListener('input', function () {
            clearTimeout(tm); var q = inp.value.trim(); if (q.length < 2) { pop.classList.remove('open'); return; }
            tm = setTimeout(function () {
                http(CFG.routes.suggest + '?q=' + encodeURIComponent(q)).then(function (r) {
                    var items = (r.data && r.data.items) || [];
                    pop.innerHTML = items.length ? items.map(function (i) { return '<a href="' + i.url + '"><img src="' + i.image + '" alt="" loading="lazy"><span>' + esc(i.name) + '<small>' + esc(i.category) + ' · ' + i.price + '</small></span></a>'; }).join('') : '<div style="padding:12px;color:var(--faint);font-size:13px">' + esc(t('js.no_results')) + '</div>';
                    pop.classList.add('open');
                });
            }, 250);
        });
        document.addEventListener('click', function (e) { if (!e.target.closest('.search-pill')) pop.classList.remove('open'); });
    });

    /* ---------- Catalogue : filtres AJAX + skeleton ---------- */
    var cat = $('#catalogue');
    if (cat) {
        var grid = $('#product-grid'), pag = $('#pagination'), count = $('#result-count'), form = $('#filters');
        var skeleton = function (n) { var s = ''; for (var i = 0; i < n; i++) s += '<div class="skel-card"><div class="skel"></div><div class="skel skel-line w60"></div><div class="skel skel-line w40"></div></div>'; return s; };
        var load = function (params, push) {
            var qs = params.toString(); grid.innerHTML = skeleton(8);
            http(CFG.routes.filter + (qs ? '?' + qs : '')).then(function (r) {
                if (!r.ok) return toast(t('js.error'), 'error');
                grid.innerHTML = r.data.html; pag.innerHTML = r.data.pagination; count && (count.textContent = r.data.total);
                bindReveal(grid); paintWish(); if (push) history.replaceState(null, '', location.pathname + (qs ? '?' + qs : ''));
            });
        };
        var current = function () { var p = new URLSearchParams(); if (form.q.value.trim()) p.set('q', form.q.value.trim()); if (form.category.value) p.set('category', form.category.value); if (form.sort.value) p.set('sort', form.sort.value); if (form.collection && form.collection.value) p.set('collection', form.collection.value); if (form.sale && form.sale.value) p.set('sale', form.sale.value); return p; };
        $$('[data-cat]').forEach(function (c) { c.addEventListener('click', function (e) { e.preventDefault(); form.category.value = c.getAttribute('data-cat'); $$('[data-cat]').forEach(function (x) { x.classList.toggle('is-active', x === c); }); load(current(), true); }); });
        form.addEventListener('submit', function (e) { e.preventDefault(); load(current(), true); });
        form.sort.addEventListener('change', function () { load(current(), true); });
        pag.addEventListener('click', function (e) { var a = e.target.closest('a'); if (!a) return; e.preventDefault(); var u = new URL(a.href); load(u.searchParams, true); window.scrollTo({ top: cat.offsetTop - 20, behavior: 'smooth' }); });
    }

    /* ---------- Fiche produit ---------- */
    var P = window.PRODUCT;
    if (P) {
        var state = { size: P.sizes && P.sizes.length ? P.sizes[0] : null, color: P.colors && P.colors.length ? P.colors[0].key : null, qty: 1, img: 0 };
        var mainImg = $('#gallery-main-img'), thumbs = $$('.thumb'), colorLabel = $('#color-label'), priceEl = $('#pdp-price'), oldEl = $('#pdp-old');
        var show = function (i) { state.img = (i + P.gallery.length) % P.gallery.length; mainImg.style.opacity = 0; setTimeout(function () { mainImg.src = P.gallery[state.img]; mainImg.style.opacity = 1; }, 120); thumbs.forEach(function (th, k) { th.classList.toggle('is-active', k === state.img); }); };
        thumbs.forEach(function (th, k) { th.addEventListener('click', function () { show(k); }); });
        $('#gal-prev') && $('#gal-prev').addEventListener('click', function () { show(state.img - 1); });
        $('#gal-next') && $('#gal-next').addEventListener('click', function () { show(state.img + 1); });
        var price = function () { var delta = 0; if (P.sizePrices && state.size) { var i = P.sizes.indexOf(state.size); delta = P.sizePrices[i] || 0; } var cur = P.price + delta; if (priceEl) priceEl.textContent = money(cur); var sp = $('#sticky-price'); if (sp) sp.textContent = money(cur); if (oldEl && P.old) oldEl.textContent = money(P.old + delta); updateTotal(); return cur; };
        var updateTotal = function () { var el = $('#pdp-total'); if (el) { var delta = (P.sizePrices && state.size) ? (P.sizePrices[P.sizes.indexOf(state.size)] || 0) : 0; el.textContent = money((P.price + delta) * state.qty); } };
        $$('.size-btn').forEach(function (b) { b.addEventListener('click', function () { $$('.size-btn').forEach(function (x) { x.classList.remove('is-active'); }); b.classList.add('is-active'); state.size = b.getAttribute('data-size'); var l = $('#size-label'); l && (l.textContent = state.size); price(); }); });
        $$('.color-btn').forEach(function (b) {
            b.addEventListener('click', function () {
                $$('.color-btn').forEach(function (x) { x.classList.remove('is-active'); }); b.classList.add('is-active'); state.color = b.getAttribute('data-color');
                colorLabel && (colorLabel.textContent = b.getAttribute('data-label'));
                var idx = P.gallery.indexOf(b.getAttribute('data-image')); if (idx > -1) show(idx);
            });
        });
        var qin = $('#qty-input'); var setQty = function (v) { state.qty = Math.max(1, Math.min(20, v)); qin.value = state.qty; updateTotal(); };
        $('#qty-minus') && $('#qty-minus').addEventListener('click', function () { setQty(state.qty - 1); });
        $('#qty-plus') && $('#qty-plus').addEventListener('click', function () { setQty(state.qty + 1); });
        qin && qin.addEventListener('change', function () { setQty(parseInt(qin.value, 10) || 1); });
        var addNow = function (btn, go) { addToCart(btn, P.id, { qty: state.qty, size: state.size, color: state.color }, function () { if (go) location.href = CFG.routes.checkout; }); };
        $$('[data-pdp-add]').forEach(function (b) { b.addEventListener('click', function () { addNow(b, false); }); });
        $$('[data-pdp-buy]').forEach(function (b) { b.addEventListener('click', function () { addNow(b, true); }); });
        var country = $('#eta-country');
        var eta = function () {
            if (!country) return; var c = country.value, d = (P.shipDays[c] || P.shipDays['*']), prod = P.production, min = prod[0] + d[0], max = prod[1] + d[1];
            $('#eta-prod').textContent = t('js.days', { a: prod[0], b: prod[1] }); $('#eta-ship').textContent = t('js.days', { a: d[0], b: d[1] }); $('#eta-total').textContent = t('js.days', { a: min, b: max });
            var cost = P.shipCost[c] !== undefined ? P.shipCost[c] : P.shipCost['*']; $('#eta-cost').textContent = P.price >= P.freeShip ? t('js.free') : money(cost);
            var now = new Date(), a = new Date(now.getTime() + min * 86400000 * 1.4), b = new Date(now.getTime() + max * 86400000 * 1.4), fmt = function (x) { return x.toLocaleDateString(CFG.locale, { day: 'numeric', month: 'short' }); };
            $('#eta-dates').textContent = fmt(a) + ' – ' + fmt(b);
            store.set('daebak-country', c);
        };
        if (country) { var sc = store.get('daebak-country', null); if (sc && country.querySelector('option[value="' + sc + '"]')) country.value = sc; country.addEventListener('change', eta); eta(); }
        $('#share-btn') && $('#share-btn').addEventListener('click', function () { var d = { title: document.title, url: location.href }; if (navigator.share) navigator.share(d).catch(function () {}); else { navigator.clipboard && navigator.clipboard.writeText(location.href); toast(t('js.link_copied'), 'info'); } });
        price();
        document.body.classList.add('has-sticky');
        // CTA collant mobile
        var stickyBtn = $('[data-pdp-sticky]'); stickyBtn && stickyBtn.addEventListener('click', function () { addNow(stickyBtn, false); });
    }

    /* ---------- Checkout ---------- */
    var co = $('#checkout-form');
    if (co) {
        var S = CFG.shipping || {}, sub = parseFloat(co.getAttribute('data-subtotal')), cc = $('#ship-country');
        var recalc = function () {
            var c = cc.value, cost = (S.zones && (S.zones[c] !== undefined ? S.zones[c] : S.zones['*'])) || 0; if (sub >= S.free) cost = 0;
            $('#sum-ship').textContent = cost === 0 ? t('js.free') : money(cost); $('#sum-total').textContent = money(sub + cost);
        };
        cc.addEventListener('change', recalc); recalc();
        co.addEventListener('submit', function (e) {
            e.preventDefault(); var btn = $('#pay-btn'), html = btn.innerHTML, errs = $('#checkout-errors');
            errs.hidden = true; btn.classList.add('is-loading'); btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + esc(t('js.processing'));
            http(co.action, { method: 'POST', body: new FormData(co) }).then(function (r) {
                if (r.ok && r.data.success) { toast(t('js.order_ok')); location.href = r.data.redirect_url; return; }
                btn.classList.remove('is-loading'); btn.innerHTML = html;
                var msg = (r.data && r.data.message) || t('js.error');
                if (r.status === 422 && r.data.errors) msg = Object.keys(r.data.errors).map(function (k) { return r.data.errors[k][0]; }).join(' · ');
                errs.textContent = msg; errs.hidden = false; errs.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
    }
    var demoPay = $('#demo-pay-form');
    if (demoPay) demoPay.addEventListener('submit', function (e) { e.preventDefault(); var b = $('[type=submit]', demoPay); b.classList.add('is-loading'); post(demoPay.action).then(function (r) { if (r.ok) location.href = r.data.redirect_url; else { b.classList.remove('is-loading'); toast(t('js.error'), 'error'); } }); });

    /* ---------- FAQ : recherche ---------- */
    var fq = $('#faq-search');
    if (fq) fq.addEventListener('input', function () { var q = fq.value.toLowerCase(); $$('#faq-list details').forEach(function (d) { d.hidden = q.length > 1 && d.textContent.toLowerCase().indexOf(q) === -1; }); });

    /* ---------- Chat ---------- */
    var fab = $('#chat-fab'), panel = $('#chat-panel');
    if (fab && panel) {
        var chat = store.get('daebak-chat', { uuid: null, mode: 'ai', last: 0 }), body = $('#chat-body'), input = $('#chat-input'), form2 = $('#chat-form'), busy = false, pollT = null;
        var addMsg = function (sender, text, id) {
            var d = document.createElement('div'); d.className = 'msg ' + sender; d.innerHTML = esc(text).replace(/\n/g, '<br>').replace(/(^|\s)(https?:\/\/[^\s<]+|\/[a-z][\w\-\/]*)/g, function (m, pre, u) { return pre + '<a href="' + u + '">' + u + '</a>'; });
            body.appendChild(d); body.scrollTop = body.scrollHeight; if (id) { chat.last = Math.max(chat.last, id); store.set('daebak-chat', chat); } return d;
        };
        var typing = function (on) { var x = $('.typing', body); if (on && !x) { var d = document.createElement('div'); d.className = 'typing'; d.innerHTML = '<i></i><i></i><i></i>'; body.appendChild(d); body.scrollTop = body.scrollHeight; } if (!on && x) x.remove(); };
        var setMode = function (m, silent) {
            chat.mode = m; store.set('daebak-chat', chat);
            $$('[data-chat-mode]').forEach(function (b) { b.classList.toggle('is-active', b.getAttribute('data-chat-mode') === m); });
            $('#chat-handoff').hidden = !(m === 'human' && !chat.handoff); form2.hidden = (m === 'human' && !chat.handoff);
            $('#chat-sub').textContent = t(m === 'human' ? 'chat.sub_human' : 'chat.sub_ai');
            $('#chat-quick').hidden = m === 'human';
            if (m === 'human' && chat.handoff) startPoll(); else stopPoll();
        };
        var greet = function () { addMsg('bot', t('chat.greeting')); };
        var restore = function () {
            if (!chat.uuid) { greet(); return; }
            http(CFG.routes.chatHistory + '?uuid=' + chat.uuid).then(function (r) { var ms = (r.data && r.data.messages) || []; if (!ms.length) greet(); ms.forEach(function (m) { addMsg(m.sender, m.body, m.id); }); if (r.data && r.data.mode) { chat.mode = r.data.mode; chat.handoff = r.data.mode === 'human'; } setMode(chat.mode); });
        };
        var opened = false;
        var openChat = function () { panel.classList.add('open'); fab.classList.add('is-hidden'); hold(true); if (!opened) { opened = true; restore(); setMode(chat.mode); } setTimeout(function () { input && input.focus(); }, 200); };
        var closeChat = function () { panel.classList.remove('open'); hold(false); show(); };
        fab.addEventListener('click', openChat); $('#chat-close').addEventListener('click', closeChat);
        $$('[data-chat-mode]').forEach(function (b) { b.addEventListener('click', function () { setMode(b.getAttribute('data-chat-mode')); }); });
        var send = function (text) {
            text = (text || '').trim(); if (!text || busy) return; busy = true; addMsg('visitor', text); input.value = ''; typing(true);
            post(CFG.routes.chatMessage, { uuid: chat.uuid, message: text, mode: chat.mode }).then(function (r) {
                busy = false; typing(false);
                if (r.ok) { chat.uuid = r.data.uuid; store.set('daebak-chat', chat); (r.data.messages || []).forEach(function (m) { addMsg(m.sender, m.body, m.id); }); }
                else addMsg('bot', (r.data && r.data.message) || t('js.error'));
            });
        };
        form2.addEventListener('submit', function (e) { e.preventDefault(); send(input.value); });
        $$('#chat-quick button').forEach(function (b) { b.addEventListener('click', function () { send(b.textContent); }); });
        $('#chat-handoff-form').addEventListener('submit', function (e) {
            e.preventDefault(); var fd = new FormData(e.target); fd.append('uuid', chat.uuid || '');
            http(CFG.routes.chatHandoff, { method: 'POST', body: fd }).then(function (r) {
                if (r.ok) { chat.uuid = r.data.uuid; chat.handoff = true; store.set('daebak-chat', chat); addMsg('bot', t('chat.handoff_ok')); setMode('human'); } else toast((r.data && r.data.message) || t('js.error'), 'error');
            });
        });
        var startPoll = function () { stopPoll(); pollT = setInterval(function () { if (!chat.uuid || !panel.classList.contains('open')) return; http(CFG.routes.chatPoll + '?uuid=' + chat.uuid + '&after=' + chat.last).then(function (r) { ((r.data && r.data.messages) || []).forEach(function (m) { addMsg(m.sender, m.body, m.id); }); }); }, 5000); };
        var stopPoll = function () { clearInterval(pollT); };

        /* Bouton auto-masquable : visible/caché en alternance toutes les 5 s sans scroll ; caché pendant le scroll. */
        var held = false, alt = null, scrolling = false, endT = null, STEP = (CFG.chatToggleMs || 5000);
        function show() { if (!panel.classList.contains('open')) fab.classList.remove('is-hidden'); }
        function hide() { fab.classList.add('is-hidden'); }
        function hold(v) { held = v; if (v) { clearInterval(alt); } else startAlt(); }
        function startAlt() { clearInterval(alt); if (held) return; alt = setInterval(function () { if (held || scrolling) return; fab.classList.contains('is-hidden') ? show() : hide(); }, STEP); }
        window.addEventListener('scroll', function () {
            if (held) return; scrolling = true; hide(); clearInterval(alt); clearTimeout(endT);
            endT = setTimeout(function () { scrolling = false; startAlt(); }, 5000);
        }, { passive: true });
        fab.addEventListener('mouseenter', function () { if (!held) { clearInterval(alt); show(); } }); fab.addEventListener('mouseleave', startAlt);
        fab.addEventListener('focus', function () { clearInterval(alt); show(); });
        startAlt();
        window.DaebakChat = { open: openChat };
        $$('[data-open-chat]').forEach(function (b) { b.addEventListener('click', function (e) { e.preventDefault(); closeDrawer(); openChat(); }); });
    }
})();
