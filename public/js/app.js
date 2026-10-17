document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initHeroSlider();
    initQuoteModal();
    initProductFilters();
    initCurrencySwitcher();
    initAjaxForms();
    initPresentationTabs();
    if (window.initRecaptchas) {
        window.initRecaptchas();
    }
});

// Automatic Hero Section Slider (5-second auto-change)
function initHeroSlider() {
    const heroSections = document.querySelectorAll('.hero-section');
    heroSections.forEach(heroSection => {
        const slides = heroSection.querySelectorAll('.hero-slide');
        const dots = heroSection.querySelectorAll('.hero-dot');
        const prevBtn = heroSection.querySelector('.hero-nav-btn.prev');
        const nextBtn = heroSection.querySelector('.hero-nav-btn.next');
        const slidesWrapper = heroSection.querySelector('.hero-slides-wrapper');

        if (!slides.length) return;

        // Auto-adapt hero slider height on mobile with guaranteed bottom clearance for slider controls
        function updateHeroHeight() {
            if (!slidesWrapper) return;
            if (window.innerWidth <= 768) {
                let maxContentHeight = 0;
                slides.forEach(slide => {
                    const content = slide.querySelector('.hero-content');
                    if (content) {
                        maxContentHeight = Math.max(maxContentHeight, content.offsetHeight || content.scrollHeight || 0);
                    }
                });
                if (maxContentHeight > 0) {
                    // Top padding (~28px) + bottom clearance (~68px) = +96px total so controls never overlap buttons
                    slidesWrapper.style.minHeight = (maxContentHeight + 96) + 'px';
                }
            } else {
                slidesWrapper.style.minHeight = '';
            }
        }

        updateHeroHeight();
        window.addEventListener('resize', updateHeroHeight, { passive: true });
        window.addEventListener('load', updateHeroHeight, { passive: true });
        window.addEventListener('orientationchange', () => setTimeout(updateHeroHeight, 200), { passive: true });
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(updateHeroHeight);
        }

        let currentIndex = 0;
        let autoTimer = null;
        const INTERVAL_TIME = 5000; // 5 seconds interval

        function showSlide(index) {
            if (index < 0) {
                currentIndex = slides.length - 1;
            } else if (index >= slides.length) {
                currentIndex = 0;
            } else {
                currentIndex = index;
            }

            slides.forEach((slide, i) => {
                slide.classList.toggle('active', i === currentIndex);
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentIndex);
            });
        }

        function nextSlide() {
            showSlide(currentIndex + 1);
        }

        function prevSlide() {
            showSlide(currentIndex - 1);
        }

        function startAutoSlide() {
            stopAutoSlide();
            autoTimer = setInterval(nextSlide, INTERVAL_TIME);
        }

        function stopAutoSlide() {
            if (autoTimer) {
                clearInterval(autoTimer);
                autoTimer = null;
            }
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', (e) => {
                e.preventDefault();
                nextSlide();
                startAutoSlide();
            });
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', (e) => {
                e.preventDefault();
                prevSlide();
                startAutoSlide();
            });
        }

        dots.forEach(dot => {
            dot.addEventListener('click', (e) => {
                e.preventDefault();
                const idx = parseInt(dot.getAttribute('data-slide'), 10);
                if (!isNaN(idx)) {
                    showSlide(idx);
                    startAutoSlide();
                }
            });
        });

        // Pause auto-sliding on hover
        heroSection.addEventListener('mouseenter', stopAutoSlide);
        heroSection.addEventListener('mouseleave', startAutoSlide);

        // Start initial auto slider
        startAutoSlide();
    });
}

// Quote Modal Handler
function initQuoteModal() {
    const modal = document.getElementById('quoteModal');
    const openBtns = document.querySelectorAll('.btn-quote, .trigger-quote');
    const closeBtn = document.getElementById('closeQuoteModal');

    if (!modal) return;

    function openModal(product) {
        const selectEl = modal.querySelector('select[name="product"]');
        if (selectEl && product) {
            selectEl.value = product;
        }
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Re-check reCAPTCHA initialization for modal form
        if (window.initRecaptchas) {
            setTimeout(window.initRecaptchas, 100);
        }

        // Close mobile navbar capsule if open
        const mobileToggle = document.getElementById('mobileNavToggle');
        const navCapsule = document.getElementById('navCapsule');
        if (mobileToggle) mobileToggle.classList.remove('is-active');
        if (navCapsule) navCapsule.classList.remove('open');
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    openBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const product = btn.getAttribute('data-product') || '';
            openModal(product);
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });
}

// Product Interactive Filtering
function initProductFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-card');

    if (!filterBtns.length) return;

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const category = btn.getAttribute('data-category');

            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            productCards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (category === 'all' || cardCat === category) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// Interactive Product Currency Switcher (INR, USD, GBP)
function initCurrencySwitcher() {
    const toggleBtns = document.querySelectorAll('.currency-toggle-btn');
    const productCards = document.querySelectorAll('.product-card');

    if (!toggleBtns.length || !productCards.length) return;

    // Helper: format currency price
    function formatPrice(amount, currency) {
        if (amount === null || isNaN(amount) || amount <= 0) return null;
        if (currency === 'USD') {
            return '$' + amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } else if (currency === 'GBP') {
            return '£' + amount.toLocaleString('en-GB', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } else {
            // INR
            const isRound = Math.floor(amount) === amount;
            return '₹' + amount.toLocaleString('en-IN', {
                minimumFractionDigits: isRound ? 0 : 2,
                maximumFractionDigits: 2
            });
        }
    }

    // Function to apply currency to all product cards
    function setCurrency(currency, isUserTriggered = false) {
        currency = (currency || 'INR').toUpperCase();
        if (!['INR', 'USD', 'GBP'].includes(currency)) {
            currency = 'INR';
        }

        // Update button states
        toggleBtns.forEach(btn => {
            const btnCurr = (btn.getAttribute('data-currency') || '').toUpperCase();
            const isActive = btnCurr === currency;
            btn.classList.toggle('active', isActive);
            btn.setAttribute('aria-checked', isActive ? 'true' : 'false');
        });

        // Save preference
        try {
            localStorage.setItem('feeble_currency', currency);
            document.cookie = `feeble_currency=${currency}; path=/; max-age=31536000; SameSite=Lax`;
        } catch (e) {
            // Storage quota or restricted context
        }

        // Update each product card
        productCards.forEach(card => {
            const priceAttr = `data-price-${currency.toLowerCase()}`;
            const offerAttr = `data-offer-${currency.toLowerCase()}`;

            const rawPrice = card.getAttribute(priceAttr);
            const rawOffer = card.getAttribute(offerAttr);

            const regPrice = rawPrice !== null && rawPrice !== '' ? parseFloat(rawPrice) : null;
            const offPrice = rawOffer !== null && rawOffer !== '' ? parseFloat(rawOffer) : null;

            const hasReg = regPrice !== null && !isNaN(regPrice) && regPrice > 0;
            const hasOff = offPrice !== null && !isNaN(offPrice) && offPrice > 0;
            const isOfferValid = hasOff && hasReg && offPrice < regPrice;

            // Update offer badge
            const badge = card.querySelector('.product-offer-badge');
            if (badge) {
                badge.style.display = isOfferValid ? '' : 'none';
            }

            // Update pricing container
            const pricingEl = card.querySelector('.product-pricing');
            if (pricingEl) {
                let html = '';
                if (isOfferValid) {
                    html = `<span class="old-price">${formatPrice(regPrice, currency)}</span> <span class="offer-price">${formatPrice(offPrice, currency)}</span>`;
                } else if (hasOff) {
                    html = `<span class="offer-price">${formatPrice(offPrice, currency)}</span>`;
                } else if (hasReg) {
                    html = `<span class="current-price">${formatPrice(regPrice, currency)}</span>`;
                } else {
                    html = `<span class="contact-price">Quote on Request</span>`;
                }
                pricingEl.innerHTML = html;

                if (isUserTriggered) {
                    pricingEl.classList.remove('price-pulse');
                    void pricingEl.offsetWidth; // trigger reflow
                    pricingEl.classList.add('price-pulse');
                }
            }
        });
    }

    // Attach click listeners to currency buttons
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const curr = btn.getAttribute('data-currency');
            setCurrency(curr, true);
        });
    });

    // Check saved preference on load
    try {
        const savedCurrency = localStorage.getItem('feeble_currency');
        if (savedCurrency && ['INR', 'USD', 'GBP'].includes(savedCurrency.toUpperCase())) {
            const activeBtn = document.querySelector('.currency-toggle-btn.active');
            const currentActive = activeBtn ? activeBtn.getAttribute('data-currency') : null;
            if (currentActive !== savedCurrency) {
                setCurrency(savedCurrency, false);
            }
        }
    } catch (e) {
        // ignore
    }
}

// Google reCAPTCHA explicit rendering & management
window.recaptchaWidgets = {};

window.initRecaptchas = function() {
    if (typeof grecaptcha === 'undefined' || !grecaptcha.render) return;

    document.querySelectorAll('.g-recaptcha').forEach(el => {
        const siteKey = el.getAttribute('data-sitekey');
        if (siteKey && !el.dataset.widgetId && el.childNodes.length === 0) {
            try {
                const widgetId = grecaptcha.render(el, {
                    'sitekey': siteKey,
                    'theme': el.dataset.theme || 'light'
                });
                el.dataset.widgetId = widgetId;
            } catch (err) {
                // Widget may already be rendered
            }
        }
    });
};

// AJAX Form Handling for Contact & Quote Forms
function initAjaxForms() {
    const contactForm = document.getElementById('contactForm');
    const quoteForm = document.getElementById('quoteForm');
    const seoQuoteForm = document.getElementById('seoHeroQuoteForm');

    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await handleFormSubmit(contactForm, '/api/contact');
        });
    }

    if (quoteForm) {
        quoteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const success = await handleFormSubmit(quoteForm, '/api/quote');
            if (success) {
                setTimeout(() => {
                    const modalEl = document.getElementById('quoteModal');
                    if (modalEl) {
                        modalEl.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }, 1500);
            }
        });
    }

    if (seoQuoteForm) {
        seoQuoteForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await handleFormSubmit(seoQuoteForm, '/api/quote');
        });
    }
}

async function handleFormSubmit(form, endpoint) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn ? submitBtn.innerHTML : '';

    const recaptchaEl = form.querySelector('.g-recaptcha');
    let recaptchaWidgetId = recaptchaEl ? recaptchaEl.dataset.widgetId : null;
    let recaptchaToken = '';

    if (recaptchaEl && typeof grecaptcha !== 'undefined') {
        try {
            if (recaptchaWidgetId !== null && recaptchaWidgetId !== undefined) {
                recaptchaToken = grecaptcha.getResponse(recaptchaWidgetId);
            } else {
                recaptchaToken = grecaptcha.getResponse();
            }
        } catch (e) {
            // Widget not yet rendered or error
        }

        if (!recaptchaToken) {
            showToast('Please check the CAPTCHA box to confirm you are not a robot.', 'error');
            return false;
        }
    }

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Sending...';
    }

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    if (recaptchaToken) {
        data['g-recaptcha-response'] = recaptchaToken;
    }

    try {
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            showToast(result.message || 'Submitted successfully!', 'success');
            form.reset();
            if (recaptchaEl && typeof grecaptcha !== 'undefined') {
                try {
                    if (recaptchaWidgetId !== null && recaptchaWidgetId !== undefined) {
                        grecaptcha.reset(recaptchaWidgetId);
                    } else {
                        grecaptcha.reset();
                    }
                } catch (e) {}
            }
            return true;
        } else {
            const msg = result.errors ? Object.values(result.errors).join(' ') : (result.message || 'An error occurred. Please check input.');
            showToast(msg, 'error');
            if (recaptchaEl && typeof grecaptcha !== 'undefined') {
                try {
                    if (recaptchaWidgetId !== null && recaptchaWidgetId !== undefined) {
                        grecaptcha.reset(recaptchaWidgetId);
                    } else {
                        grecaptcha.reset();
                    }
                } catch (e) {}
            }
            return false;
        }
    } catch (err) {
        showToast('Network error. Please try again.', 'error');
        return false;
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }
}

// Toast notification helper
function showToast(message, type = 'success') {
    let toast = document.getElementById('toastNotification');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toastNotification';
        toast.className = 'toast-notification';
        document.body.appendChild(toast);
    }

    toast.innerHTML = `
        <div style="font-size: 1.2rem;">${type === 'success' ? '✓' : '⚠️'}</div>
        <div>${message}</div>
    `;

    toast.classList.add('show');

    setTimeout(() => {
        toast.classList.remove('show');
    }, 4500);
}

// Presentation Tab View Switcher
function initPresentationTabs() {
    const tabs = document.querySelectorAll('.view-mode-tab');
    if (!tabs.length) return;

    tabs.forEach(tab => {
        tab.addEventListener('click', (e) => {
            const mode = tab.getAttribute('data-mode');
            if (mode === 'single') {
                window.location.href = '/';
            } else if (mode === 'all') {
                window.location.href = '/all-pages';
            }
        });
    });
}

/* Global Markets — dotted world map with animated export routes */
(function () {
  'use strict';

  var MASK = [
    ".......................................########......##########.................................................................................",
    ".................................###########.####################.............#####..........................###................................",
    "........................##...........####....###################..............#..................................#..............................",
    "...........................##...########.........###############..............................##...........##########..........###.#............",
    "......................##.###.#.##.#.####..........#############..............................#......#...####################....##..............",
    ".......########..........#######..#...##.####.....#############.................####...............##.##################################....#...",
    "##....######################.##########..#.###.....########...................##########...##########.##########################################",
    "..#..################################.#..####......#####.......###..........####..####..########################################################",
    "......#############################...#.....#.......###...................#####.################################################_________#####",
    ".....########..###################.......###..............................#####..#..##################################################...##.....",
    ".........##........################......######.......................#......##...##############################################.......##.......",
    "......#.............###################..########.....................#....#....##############################################........###.......",
    ".....................##################..#########..................#.###.#######################################################.....#.........",
    ".....................########################....#......................########################################################.#..............",
    "......................########################....#....................################################################_________................",
    "......................#######################.#........................######.######.#.####..##################################.................",
    "......................######################........................#####..###.####......##..###############################....#...............",
    "......................####################..........................####...#..#.##.#########.###########################.##.....#...............",
    ".......................##################...........................####.....#.....#########..###########################..#...#................",
    "........................##################...........................#######.........################################___.....###................",
    ".........................###############............................##########..#.....##################################....#...................",
    "...........................#########..#.............................#######################.#############################.......................",
    "..........................#.#####......#...........................##################.######.###########################........................",
    "...........................#.####.................................####################.######.#....######################.......................",
    "..............................###.....##.........................######################.########....########.#########..........................",
    "..............................###...#....#.......................######################.#######......#####...#####..............................",
    "...............................######.............................#####################..#####.......####.....#####.....#.......................",
    "...................................####..........................#######################.###..........##.......#####....#.......................",
    ".....................................##..........................########################.............##.......#.###....#.......................",
    "......................................#...#.###...................##########################..........##.......#..#....#........................",
    ".........................................########..................#########################............#.................#.....................",
    ".........................................##########.................##.#..#################...................#.#.....##........................",
    ".........................................###########........................##############.....................#.#...##.........................",
    "........................................############........................#############.......................##..###.........................",
    "........................................###############.....................############........................##..###.#....###................",
    ".......................................##################....................###########.......................................###..............",
    "........................................##################...................###########...........................##..........####.............",
    ".........................................#################...................###########.................................#.........#............",
    ".........................................################....................###########....................................###.................",
    "..........................................##############.....................###########...#..............................####...#..............",
    "...........................................#############.....................##########...##................ me .............#########..............",
    "............................................############.....................#########....#.............................###########.............",
    "............................................###########.......................########...##..........................###############............",
    "............................................#########.........................########....#..........................################...........",
    "............................................#########.........................#######.................................###############...........",
    "...........................................#########...........................#####..................................###############...........",
    "...........................................########............................####...................................####....#######...........",
    "...........................................######..............................................................................#####............",
    "...........................................######...............................................................................####............",
    "...........................................####...............................................................................................#.",
    "..........................................#####...................................................................................#.........#...",
    "..........................................####.............................................................................................#....",
    "..........................................####..................................................................................................",
    "..........................................###...................................................................................................",
    "..........................................##....................................................................................................",
    "............................................##.................................................................................................."
  ];

  var STEP = 2.5, TOP = 83.75;
  var COLS = MASK[0].length, ROWS = MASK.length;
  var W = 1000, CELL = W / COLS, H = ROWS * CELL;
  var NS = 'http://www.w3.org/2000/svg';
  var uid = 0;

  function el(name, attrs, parent) {
    var n = document.createElementNS(NS, name);
    for (var k in attrs) n.setAttribute(k, attrs[k]);
    if (parent) parent.appendChild(n);
    return n;
  }
  function project(lat, lon) {
    return { x: (lon + 180) / 360 * W, y: (TOP - lat) / STEP * CELL };
  }

  function init(visual) {
    var root = visual.closest('.fx-markets');
    var host = visual.querySelector('.fx-map');
    var origin, markets;
    try {
      origin = JSON.parse(visual.getAttribute('data-origin'));
      markets = JSON.parse(visual.getAttribute('data-markets'));
    } catch (e) { return; }

    var id = 'fx' + (++uid);
    var o = project(origin.lat, origin.lon);
    var svg = el('svg', { viewBox: '0 0 ' + W + ' ' + H, preserveAspectRatio: 'xMidYMid meet', 'aria-hidden': 'true' });
    var defs = el('defs', {}, svg);

    /* 1. dotted land — ripples outward from the origin */
    var dots = el('g', {}, svg);
    var maxD = 0, list = [];
    for (var r = 0; r < ROWS; r++) {
      for (var c = 0; c < COLS; c++) {
        if (MASK[r].charAt(c) !== '#') continue;
        var x = (c + 0.5) * CELL, y = (r + 0.5) * CELL;
        var d = Math.hypot(x - o.x, y - o.y);
        if (d > maxD) maxD = d;
        list.push([x, y, d]);
      }
    }
    list.forEach(function (p) {
      var dot = el('circle', { cx: p[0].toFixed(1), cy: p[1].toFixed(1), r: 1.9, 'class': 'fx-dot' }, dots);
      dot.style.setProperty('--d', (p[2] / maxD * 1.8).toFixed(2) + 's');
    });

    /* 2. routes */
    var routes = el('g', {}, svg);
    var pulses = [];
    var n = 0;
    markets.forEach(function (m) {
      var g = el('g', { 'class': 'fx-route', 'data-group': m.id }, routes);
      m.cities.forEach(function (city) {
        var p = project(city.lat, city.lon);
        var dist = Math.hypot(p.x - o.x, p.y - o.y);
        var lift = Math.min(dist * 0.32, 130);
        var cx = (o.x + p.x) / 2, cy = Math.min(o.y, p.y) - lift * 0.6 - dist * 0.04;
        if (Math.abs(p.x - o.x) < 40) cy = (o.y + p.y) / 2 - 24; // very short hops
        var dStr = 'M' + o.x.toFixed(1) + ' ' + o.y.toFixed(1) + ' Q' + cx.toFixed(1) + ' ' + cy.toFixed(1) + ' ' + p.x.toFixed(1) + ' ' + p.y.toFixed(1);
        var delay = 0.9 + n * 0.16;
        var drawTime = 1.5;

        var mask = el('mask', { id: id + '-m' + n, maskUnits: 'userSpaceOnUse', x: 0, y: -40, width: W, height: H + 80 }, defs);
        var rv = el('path', { d: dStr, 'class': 'fx-reveal', pathLength: 1 }, mask);
        rv.style.setProperty('--d', delay + 's');
        var arc = el('path', { d: dStr, 'class': 'fx-arc', mask: 'url(#' + id + '-m' + n + ')' }, g);

        var cg = el('g', { 'class': 'fx-city', transform: 'translate(' + p.x.toFixed(1) + ' ' + p.y.toFixed(1) + ')' }, g);
        cg.style.setProperty('--d', (delay + drawTime - 0.2) + 's');
        var ring = el('circle', { r: 5, 'class': 'fx-city__ring' }, cg);
        ring.style.setProperty('--r', (n * 0.35 % 2.8).toFixed(2) + 's');
        el('circle', { r: 3.6, 'class': 'fx-city__dot' }, cg);
        var label = el('text', { x: 0, y: -10, 'text-anchor': 'middle', 'class': 'fx-city__name' }, cg);
        label.textContent = city.name;

        var pulse = el('circle', { r: 2.8, 'class': 'fx-pulse' }, g);
        pulses.push({ node: pulse, path: arc, len: arc.getTotalLength(), offset: (n * 0.37) % 1, group: m.id });
        n++;
      });
    });

    /* 3. origin pin + label */
    var og = el('g', { transform: 'translate(' + o.x.toFixed(1) + ' ' + o.y.toFixed(1) + ')' }, svg);
    ['0s', '1.2s'].forEach(function (delay) {
      var rg = el('circle', { r: 6, 'class': 'fx-origin__ring' }, og);
      rg.style.setProperty('--r', delay);
    });
    var pin = el('g', { 'class': 'fx-origin' }, og);
    el('path', { d: 'M0 0C-5 -8 -9 -12 -9 -17a9 9 0 1 1 18 0c0 5 -4 9 -9 17z', 'class': 'fx-origin__pin' }, pin);
    el('circle', { cy: -17, r: 3.4, 'class': 'fx-origin__hole' }, pin);
    var tag = el('g', { 'class': 'fx-origin__tag', transform: 'translate(14 -34)' }, pin);
    var tw = origin.name.length * 6.6 + 18;
    el('rect', { width: tw.toFixed(0), height: 20, rx: 10 }, tag);
    var tt = el('text', { x: tw / 2, y: 13.8, 'text-anchor': 'middle' }, tag);
    tt.textContent = origin.name.toUpperCase();

    host.appendChild(svg);

    /* 4. legend hover / focus highlights a market group */
    var items = visual.querySelectorAll('.fx-legend__item');
    function setActive(group) {
      if (group) root.setAttribute('data-active', group); else root.removeAttribute('data-active');
      root.querySelectorAll('.fx-route').forEach(function (r) {
        r.classList.toggle('is-active', r.getAttribute('data-group') === group);
      });
      items.forEach(function (b) { b.classList.toggle('is-on', b.getAttribute('data-group') === group); });
    }
    items.forEach(function (b) {
      var g = b.getAttribute('data-group');
      b.addEventListener('mouseenter', function () { setActive(g); });
      b.addEventListener('focus', function () { setActive(g); });
      b.addEventListener('mouseleave', function () { setActive(null); });
      b.addEventListener('blur', function () { setActive(null); });
      b.addEventListener('click', function () { setActive(root.getAttribute('data-active') === g ? null : g); });
    });

    /* 5. start when scrolled into view */
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var raf = null, last = 0, running = false;

    function frame(t) {
      var dt = last ? (t - last) / 1000 : 0; last = t;
      pulses.forEach(function (p) {
        p.offset = (p.offset + dt / 3.4) % 1;
        var pt = p.path.getPointAtLength(p.len * p.offset);
        p.node.setAttribute('cx', pt.x); p.node.setAttribute('cy', pt.y);
        var edge = Math.min(p.offset, 1 - p.offset) * 8;
        p.node.style.opacity = Math.min(1, edge);
      });
      raf = requestAnimationFrame(frame);
    }
    function startPulses() { if (reduce || running) return; running = true; last = 0; raf = requestAnimationFrame(frame); }
    function stopPulses() { running = false; if (raf) cancelAnimationFrame(raf); }

    if (reduce || !('IntersectionObserver' in window)) {
      root.classList.add('is-visible');
      return;
    }
    var introDone = false;
    new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          root.classList.add('is-visible');
          if (!introDone) {
            introDone = true;
            setTimeout(function () { root.classList.add('is-live'); if (running === false) startPulses(); }, 3200);
          } else if (root.classList.contains('is-live')) { startPulses(); }
        } else { stopPulses(); }
      });
    }, { threshold: 0.3 }).observe(visual);
  }

  function boot() { document.querySelectorAll('[data-fx-map]').forEach(init); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', boot); else boot();
})();

// Floating Navbar Scroll & Mobile Menu Handler
function initNavbar() {
    const headerWrapper = document.querySelector('.header-wrapper');
    const mobileToggle = document.getElementById('mobileNavToggle');
    const navCapsule = document.getElementById('navCapsule');
    const navLinks = document.querySelectorAll('.nav-link');

    // Scroll state handler
    function handleScroll() {
        if (!headerWrapper) return;
        if (window.scrollY > 20) {
            headerWrapper.classList.add('scrolled');
        } else {
            headerWrapper.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    // Mobile Toggle handler
    if (mobileToggle && navCapsule) {
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isActive = mobileToggle.classList.toggle('is-active');
            navCapsule.classList.toggle('open', isActive);
        });

        // Close mobile nav on outside click
        document.addEventListener('click', (e) => {
            if (!navCapsule.contains(e.target) && !mobileToggle.contains(e.target)) {
                mobileToggle.classList.remove('is-active');
                navCapsule.classList.remove('open');
            }
        });

        // Close mobile nav when clicking link
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileToggle.classList.remove('is-active');
                navCapsule.classList.remove('open');
            });
        });
    }
}

