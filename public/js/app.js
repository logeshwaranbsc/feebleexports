document.addEventListener('DOMContentLoaded', () => {
    initNavbar();
    initHeroSlider();
    initQuoteModal();
    initProductFilters();
    initAjaxForms();
    initPresentationTabs();
});

// Automatic Hero Section Slider (5-second auto-change)
function initHeroSlider() {
    const heroSections = document.querySelectorAll('.hero-section');
    heroSections.forEach(heroSection => {
        const slides = heroSection.querySelectorAll('.hero-slide');
        const dots = heroSection.querySelectorAll('.hero-dot');
        const prevBtn = heroSection.querySelector('.hero-nav-btn.prev');
        const nextBtn = heroSection.querySelector('.hero-nav-btn.next');

        if (!slides.length) return;

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

    openBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const product = btn.getAttribute('data-product') || '';
            const selectEl = modal.querySelector('select[name="product"]');
            if (selectEl && product) {
                selectEl.value = product;
            }
            modal.classList.add('active');
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.remove('active');
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
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// AJAX Form Handling for Contact & Quote Forms
function initAjaxForms() {
    const contactForm = document.getElementById('contactForm');
    const quoteForm = document.getElementById('quoteForm');

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
                    document.getElementById('quoteModal')?.classList.remove('active');
                }, 1500);
            }
        });
    }
}

async function handleFormSubmit(form, endpoint) {
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn ? submitBtn.innerHTML : '';

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Sending...';
    }

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

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
            showToast(result.message, 'success');
            form.reset();
            return true;
        } else {
            const msg = result.errors ? Object.values(result.errors).join(' ') : 'An error occurred. Please check input.';
            showToast(msg, 'error');
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

