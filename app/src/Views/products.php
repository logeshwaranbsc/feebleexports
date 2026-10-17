<?php
$selectedCurrency = strtoupper($_COOKIE['feeble_currency'] ?? 'INR');
if (!in_array($selectedCurrency, ['INR', 'USD', 'GBP'], true)) {
    $selectedCurrency = 'INR';
}
?>
<style>
/* Resilient self-contained styles for Toolbar & Currency Switcher */
.products-toolbar {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  flex-wrap: wrap !important;
  gap: 16px !important;
  margin-bottom: 28px !important;
  background: #faf7f2 !important;
  padding: 10px 18px !important;
  border-radius: 14px !important;
  border: 1px solid #eae3d8 !important;
  box-shadow: 0 1px 4px rgba(0,0,0,0.02) !important;
  box-sizing: border-box !important;
  width: 100% !important;
  max-width: 100% !important;
}

.filter-tabs {
  display: flex !important;
  flex-wrap: wrap !important;
  gap: 8px !important;
  align-items: center !important;
  flex: 1 1 auto !important;
  min-width: 0 !important;
}

.filter-btn {
  padding: 8px 18px !important;
  border: 1px solid #dfd4c4 !important;
  border-radius: 9999px !important;
  background: #ffffff !important;
  color: #554838 !important;
  font-size: 0.85rem !important;
  font-weight: 600 !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
  font-family: inherit !important;
  text-decoration: none !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  line-height: 1.2 !important;
  white-space: nowrap !important;
  -webkit-tap-highlight-color: transparent !important;
}

.filter-btn:hover {
  background: #f0e6d8 !important;
  border-color: #cbbeab !important;
}

.filter-btn.active {
  background: #b85c38 !important;
  color: #ffffff !important;
  border-color: #b85c38 !important;
}

.currency-selector-card {
  display: inline-flex !important;
  align-items: center !important;
  gap: 10px !important;
  background: #ffffff !important;
  padding: 5px 8px 5px 14px !important;
  border-radius: 9999px !important;
  border: 1px solid #dfd4c4 !important;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
  flex-shrink: 0 !important;
  box-sizing: border-box !important;
}

.currency-selector-label {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  font-size: 0.82rem !important;
  font-weight: 700 !important;
  color: #2b2118 !important;
  letter-spacing: 0.4px !important;
  text-transform: uppercase !important;
  user-select: none !important;
  white-space: nowrap !important;
  flex-shrink: 0 !important;
}

.currency-selector-label .currency-icon {
  font-size: 1rem !important;
  line-height: 1 !important;
}

.currency-toggle-group {
  display: inline-flex !important;
  align-items: center !important;
  background: #f4ede3 !important;
  padding: 3px !important;
  border-radius: 9999px !important;
  gap: 2px !important;
  box-sizing: border-box !important;
}

.currency-toggle-btn {
  border: none !important;
  background: transparent;
  padding: 6px 14px !important;
  border-radius: 9999px !important;
  font-size: 0.82rem !important;
  font-weight: 700 !important;
  color: #6d6155;
  cursor: pointer !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 5px !important;
  transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1) !important;
  font-family: inherit !important;
  outline: none !important;
  -webkit-appearance: none !important;
  appearance: none !important;
  text-decoration: none !important;
  line-height: 1.2 !important;
  white-space: nowrap !important;
  -webkit-tap-highlight-color: transparent !important;
}

.currency-toggle-btn:hover {
  color: #1c140d;
  background: rgba(255, 255, 255, 0.7);
}

.currency-toggle-btn.active {
  background: #b85c38 !important;
  color: #ffffff !important;
  box-shadow: 0 2px 8px rgba(184, 92, 56, 0.35) !important;
  transform: translateY(-1px) !important;
}

.currency-toggle-btn .curr-symbol {
  font-weight: 800 !important;
  font-size: 0.92rem !important;
}

.currency-toggle-btn.active .curr-symbol {
  color: #ffffff !important;
}

.product-pricing {
  margin-top: 8px !important;
  display: flex !important;
  align-items: baseline !important;
  gap: 8px !important;
  flex-wrap: wrap !important;
  min-height: 28px !important;
  transition: opacity 0.2s ease, transform 0.2s ease !important;
}
.product-pricing .old-price {
  font-size: 0.95rem !important;
  text-decoration: line-through !important;
  color: #998d82 !important;
}
.product-pricing .offer-price {
  font-size: 1.35rem !important;
  font-weight: 800 !important;
  color: #b85c38 !important;
}
.product-pricing .current-price {
  font-size: 1.35rem !important;
  font-weight: 800 !important;
  color: #2b2118 !important;
}
.product-pricing .contact-price {
  font-size: 0.95rem !important;
  font-weight: 600 !important;
  color: #7c7267 !important;
  font-style: italic !important;
}

/* -------------------------------------------------------------
   MOBILE RESPONSIVE BREAKPOINTS (<= 860px, <= 480px, <= 350px)
   ------------------------------------------------------------- */
@media (max-width: 860px) {
  .products-toolbar {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 12px !important;
    padding: 12px !important;
    margin-bottom: 22px !important;
    overflow: hidden !important;
  }

  /* Smooth horizontal swipe for category pills */
  .filter-tabs {
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch !important;
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
    gap: 8px !important;
    padding: 2px 2px 6px 2px !important;
    width: 100% !important;
  }
  .filter-tabs::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
  }

  .filter-btn {
    flex-shrink: 0 !important;
    padding: 7px 15px !important;
    font-size: 0.82rem !important;
    white-space: nowrap !important;
  }

  /* Full width sleek currency card on mobile */
  .currency-selector-card {
    display: flex !important;
    width: 100% !important;
    max-width: 100% !important;
    justify-content: space-between !important;
    align-items: center !important;
    padding: 4px 6px 4px 12px !important;
    border-radius: 9999px !important;
    gap: 8px !important;
    box-sizing: border-box !important;
  }

  .currency-selector-label {
    font-size: 0.78rem !important;
    gap: 5px !important;
  }

  .currency-toggle-group {
    display: flex !important;
    flex: 1 1 auto !important;
    justify-content: flex-end !important;
    max-width: 230px !important;
    padding: 2px !important;
    gap: 2px !important;
  }

  .currency-toggle-btn {
    flex: 1 1 0 !important;
    justify-content: center !important;
    padding: 6px 8px !important;
    font-size: 0.78rem !important;
    gap: 3px !important;
  }
  .currency-toggle-btn .curr-symbol {
    font-size: 0.84rem !important;
  }
  .currency-toggle-btn .curr-code {
    font-size: 0.72rem !important;
  }
}

@media (max-width: 480px) {
  .products-section {
    padding: 40px 14px !important;
  }

  .products-header-desc {
    font-size: 0.92rem !important;
    margin-bottom: 20px !important;
  }

  .products-toolbar {
    padding: 10px !important;
    border-radius: 14px !important;
    gap: 10px !important;
    margin-bottom: 20px !important;
  }

  .filter-tabs {
    gap: 6px !important;
    padding-bottom: 4px !important;
  }

  .filter-btn {
    padding: 6px 12px !important;
    font-size: 0.78rem !important;
    min-height: 32px !important;
  }

  .currency-selector-card {
    padding: 4px 5px 4px 10px !important;
    gap: 6px !important;
  }

  .currency-selector-label {
    font-size: 0.74rem !important;
    gap: 4px !important;
  }

  .currency-selector-label .currency-icon {
    font-size: 0.88rem !important;
  }

  .currency-toggle-group {
    max-width: 195px !important;
    padding: 2px !important;
    gap: 2px !important;
  }

  .currency-toggle-btn {
    padding: 5px 6px !important;
    font-size: 0.74rem !important;
    gap: 2px !important;
  }

  .currency-toggle-btn .curr-symbol {
    font-size: 0.80rem !important;
  }
  .currency-toggle-btn .curr-code {
    font-size: 0.68rem !important;
  }

  .products-grid {
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 12px !important;
  }

  .product-info {
    padding: 10px 8px !important;
  }

  .product-title {
    font-size: 0.86rem !important;
    line-height: 1.25 !important;
  }

  .product-pricing .old-price {
    font-size: 0.8rem !important;
  }

  .product-pricing .offer-price,
  .product-pricing .current-price {
    font-size: 1.05rem !important;
  }
}

@media (max-width: 350px) {
  .currency-selector-label .curr-label-text {
    display: none !important;
  }
  .currency-toggle-group {
    max-width: 100% !important;
  }
  .currency-toggle-btn {
    padding: 5px 4px !important;
  }
}
</style>

<!-- Page 3 - Products -->
<section class="products-section">
  <div class="products-container">
    
    <span class="section-tag">OUR PRODUCTS</span>
    <h2 class="section-heading">Coir Mats Collection</h2>

    <p class="products-header-desc">
      A wide range of natural, durable and sustainable coir mat products suitable for homes, offices, gardens and industries. Customization available in colours, designs and sizes.
    </p>

    <!-- Products Toolbar: Category Filters + Interactive Currency Selector -->
    <div class="products-toolbar">
      <!-- Filter Buttons (Swipeable on Mobile) -->
      <div class="filter-tabs" role="tablist" aria-label="Filter products by category">
        <?php foreach ($categories as $cat): ?>
          <button class="filter-btn <?= $cat['slug'] === 'all' ? 'active' : '' ?>" data-category="<?= htmlspecialchars($cat['slug']) ?>">
            <?= htmlspecialchars($cat['name']) ?>
          </button>
        <?php endforeach; ?>
      </div>

      <!-- Currency Switcher Option (Auto-fitting responsive pill) -->
      <div class="currency-selector-card" id="currencySelectorCard">
        <div class="currency-selector-label">
          <span class="currency-icon" aria-hidden="true">💱</span>
          <span class="curr-label-text">Currency:</span>
        </div>
        <div class="currency-toggle-group" role="radiogroup" aria-label="Select currency">
          <button type="button" 
                  class="currency-toggle-btn <?= $selectedCurrency === 'INR' ? 'active' : '' ?>" 
                  data-currency="INR" 
                  role="radio" 
                  aria-checked="<?= $selectedCurrency === 'INR' ? 'true' : 'false' ?>"
                  title="Indian Rupee (₹ INR)"
                  style="<?= $selectedCurrency === 'INR' ? 'background:#b85c38; color:#ffffff; box-shadow:0 2px 8px rgba(184,92,56,0.35);' : 'background:transparent; color:#6d6155;' ?>">
            <span class="curr-symbol">₹</span>
            <span class="curr-code">INR</span>
          </button>
          <button type="button" 
                  class="currency-toggle-btn <?= $selectedCurrency === 'USD' ? 'active' : '' ?>" 
                  data-currency="USD" 
                  role="radio" 
                  aria-checked="<?= $selectedCurrency === 'USD' ? 'true' : 'false' ?>"
                  title="US Dollar ($ USD)"
                  style="<?= $selectedCurrency === 'USD' ? 'background:#b85c38; color:#ffffff; box-shadow:0 2px 8px rgba(184,92,56,0.35);' : 'background:transparent; color:#6d6155;' ?>">
            <span class="curr-symbol">$</span>
            <span class="curr-code">USD</span>
          </button>
          <button type="button" 
                  class="currency-toggle-btn <?= $selectedCurrency === 'GBP' ? 'active' : '' ?>" 
                  data-currency="GBP" 
                  role="radio" 
                  aria-checked="<?= $selectedCurrency === 'GBP' ? 'true' : 'false' ?>"
                  title="British Pound (£ GBP)"
                  style="<?= $selectedCurrency === 'GBP' ? 'background:#b85c38; color:#ffffff; box-shadow:0 2px 8px rgba(184,92,56,0.35);' : 'background:transparent; color:#6d6155;' ?>">
            <span class="curr-symbol">£</span>
            <span class="curr-code">GBP</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Layout: Grid + Spec Sidebar -->
    <div class="products-layout">
      
      <!-- Products Grid -->
      <div class="products-grid">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $product): ?>
            <?php 
              // Multi-currency price data
              $inrPrice = isset($product['price']) && $product['price'] !== '' && (float)$product['price'] > 0 ? (float)$product['price'] : null;
              $inrOffer = isset($product['offer_price']) && $product['offer_price'] !== '' && (float)$product['offer_price'] > 0 ? (float)$product['offer_price'] : null;

              $usdPrice = isset($product['price_usd']) && $product['price_usd'] !== '' && (float)$product['price_usd'] > 0 ? (float)$product['price_usd'] : null;
              $usdOffer = isset($product['offer_price_usd']) && $product['offer_price_usd'] !== '' && (float)$product['offer_price_usd'] > 0 ? (float)$product['offer_price_usd'] : null;

              $gbpPrice = isset($product['price_gbp']) && $product['price_gbp'] !== '' && (float)$product['price_gbp'] > 0 ? (float)$product['price_gbp'] : null;
              $gbpOffer = isset($product['offer_price_gbp']) && $product['offer_price_gbp'] !== '' && (float)$product['offer_price_gbp'] > 0 ? (float)$product['offer_price_gbp'] : null;

              // Initial active currency price calculation for server-render
              if ($selectedCurrency === 'USD') {
                  $currSymbol = '$';
                  $currPrice = $usdPrice;
                  $currOffer = $usdOffer;
                  $decimals = 2;
              } elseif ($selectedCurrency === 'GBP') {
                  $currSymbol = '£';
                  $currPrice = $gbpPrice;
                  $currOffer = $gbpOffer;
                  $decimals = 2;
              } else {
                  $currSymbol = '₹';
                  $currPrice = $inrPrice;
                  $currOffer = $inrOffer;
                  $decimals = ($currPrice !== null && floor($currPrice) == $currPrice) ? 0 : 2;
              }

              $hasPrice = $currPrice !== null && $currPrice > 0;
              $hasOffer = $currOffer !== null && $currOffer > 0;
              $isOfferValid = $hasOffer && $hasPrice && $currOffer < $currPrice;
            ?>
            <div class="product-card" 
                 data-category="<?= htmlspecialchars($product['category_slug']) ?>"
                 data-price-inr="<?= $inrPrice !== null ? $inrPrice : '' ?>"
                 data-offer-inr="<?= $inrOffer !== null ? $inrOffer : '' ?>"
                 data-price-usd="<?= $usdPrice !== null ? $usdPrice : '' ?>"
                 data-offer-usd="<?= $usdOffer !== null ? $usdOffer : '' ?>"
                 data-price-gbp="<?= $gbpPrice !== null ? $gbpPrice : '' ?>"
                 data-offer-gbp="<?= $gbpOffer !== null ? $gbpOffer : '' ?>">
              
              <div class="product-offer-badge" style="<?= $isOfferValid ? '' : 'display: none;' ?>">
                Offer
              </div>
              <div class="product-thumb">
                <img src="<?= htmlspecialchars(!empty($product['image']) ? $product['image'] : '/assets/images/01_plain_handloom.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='/assets/images/01_plain_handloom.png'">
              </div>
              <div class="product-info">
                <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                
                <div class="product-pricing">
                  <?php if ($isOfferValid): ?>
                    <span class="old-price"><?= $currSymbol . number_format($currPrice, $decimals) ?></span>
                    <span class="offer-price"><?= $currSymbol . number_format($currOffer, $decimals) ?></span>
                  <?php elseif ($hasOffer): ?>
                    <span class="offer-price"><?= $currSymbol . number_format($currOffer, $decimals) ?></span>
                  <?php elseif ($hasPrice): ?>
                    <span class="current-price"><?= $currSymbol . number_format($currPrice, $decimals) ?></span>
                  <?php else: ?>
                    <span class="contact-price">Quote on Request</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div style="grid-column: 1 / -1; text-align: center; padding: 48px 20px; color: var(--color-text-muted);">
            <p style="font-size: 1.1rem; font-weight: 500;">No products available at the moment.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Right Side Specification & Customization Boxes -->
      <div class="products-sidebar">
        
        <div class="spec-box">
          <h4 class="spec-title">Product Story / Info</h4>
          <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 12px;">Standard Sizes:</p>
          <ul class="size-list">
            <?php foreach ($sizes as $sz): ?>
              <li class="size-item">
                <?php if (is_array($sz)): ?>
                  <span class="size-metric"><?= htmlspecialchars($sz['cm']) ?></span>
                  <span class="size-imperial"><?= htmlspecialchars($sz['inches']) ?></span>
                <?php else: ?>
                  <span class="size-metric"><?= htmlspecialchars($sz) ?></span>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
          <p class="size-subtext">(Sizes in both Centimetres & Inches for Global Markets)</p>
        </div>

        <div class="spec-box" style="text-align: center; background: #fff8f3; border-color: #f0d5c5;">
          <div style="font-size: 2.2rem; margin-bottom: 8px;">🎨</div>
          <h4 class="spec-title" style="color: var(--color-terracotta);">Customization Available</h4>
          <p style="font-size: 0.85rem; color: var(--color-text-body); line-height: 1.5;">
            Wide range of colours, designs and custom sizes available.
          </p>
          <button class="btn-quote" style="width: 100%; margin-top: 16px; justify-content: center;">
            Request Custom Size
          </button>
        </div>

      </div>

    </div>

  </div>
</section>

<!-- Products Page Pre-Footer CTA -->
<section class="section-padding bg-dark-banner text-light" style="margin-top: 60px;">
  <div class="container grid-2-col align-center">
    <div>
      <span class="section-tag tag-gold">TAILORED COIR SOLUTIONS</span>
      <h2 class="section-heading text-light">Need Custom Dimensions or Bulk Export Quantities?</h2>
      <p class="text-light-subtle max-w-500">
        We specialize in bespoke manufacturing for commercial distributors, hotel chains, retailers, and industrial partners worldwide. Contact us for custom sizes, branding, and bulk pricing.
      </p>
    </div>
    <div>
      <div class="cta-btn-group mb-6">
        <button class="btn-primary btn-quote">Request Bulk Quote &rarr;</button>
        <a href="/contact" class="btn-outline-light">Talk to Export Team</a>
      </div>
      <div class="footer-contact-list">
        <div class="footer-contact-item">
          <span>✉️</span>
          <a href="mailto:feebleexports@outlook.com">feebleexports@outlook.com</a>
        </div>
        <div class="footer-contact-item">
          <span>📞</span>
          <a href="tel:+918838489610" style="color: inherit; text-decoration: none;">+91 8838489610</a> | <a href="tel:+447900558965" style="color: inherit; text-decoration: none;">+44 7900558965</a>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
(function() {
  function initProductsCurrencySwitcher() {
    var toggleGroup = document.querySelector('.currency-toggle-group');
    if (!toggleGroup) return;

    var toggleBtns = toggleGroup.querySelectorAll('.currency-toggle-btn');
    var productCards = document.querySelectorAll('.product-card');

    function formatPriceVal(num, curr) {
      if (num === null || num === undefined || isNaN(num)) return '';
      var sym = curr === 'USD' ? '$' : (curr === 'GBP' ? '£' : '₹');
      var val = parseFloat(num);
      var dec = (curr === 'INR' && Math.floor(val) === val) ? 0 : 2;
      return sym + val.toLocaleString('en-US', { minimumFractionDigits: dec, maximumFractionDigits: dec });
    }

    function applyCurrencySelection(currency, isUserAction) {
      currency = (currency || 'INR').toUpperCase();
      if (!['INR', 'USD', 'GBP'].includes(currency)) {
        currency = 'INR';
      }

      // Update button visual styles
      toggleBtns.forEach(function(btn) {
        var btnCurr = (btn.getAttribute('data-currency') || '').toUpperCase();
        var isActive = (btnCurr === currency);
        btn.classList.toggle('active', isActive);
        btn.setAttribute('aria-checked', isActive ? 'true' : 'false');
        if (isActive) {
          btn.style.background = '#b85c38';
          btn.style.color = '#ffffff';
          btn.style.boxShadow = '0 2px 8px rgba(184, 92, 56, 0.35)';
          btn.style.transform = 'translateY(-1px)';
        } else {
          btn.style.background = 'transparent';
          btn.style.color = '#6d6155';
          btn.style.boxShadow = 'none';
          btn.style.transform = 'none';
        }
      });

      // Save to localStorage & cookie
      try {
        localStorage.setItem('feeble_currency', currency);
        document.cookie = 'feeble_currency=' + currency + '; path=/; max-age=31536000; SameSite=Lax';
      } catch (e) {}

      // Update all product cards
      productCards.forEach(function(card) {
        var pAttr = 'data-price-' + currency.toLowerCase();
        var oAttr = 'data-offer-' + currency.toLowerCase();

        var rawP = card.getAttribute(pAttr);
        var rawO = card.getAttribute(oAttr);

        var regPrice = (rawP !== null && rawP !== '') ? parseFloat(rawP) : null;
        var offPrice = (rawO !== null && rawO !== '') ? parseFloat(rawO) : null;

        var hasReg = regPrice !== null && !isNaN(regPrice) && regPrice > 0;
        var hasOff = offPrice !== null && !isNaN(offPrice) && offPrice > 0;
        var isOfferValid = hasOff && hasReg && offPrice < regPrice;

        var badge = card.querySelector('.product-offer-badge');
        if (badge) {
          badge.style.display = isOfferValid ? '' : 'none';
        }

        var pricingEl = card.querySelector('.product-pricing');
        if (pricingEl) {
          var html = '';
          if (isOfferValid) {
            html = '<span class="old-price" style="font-size:0.95rem;text-decoration:line-through;color:#998d82;margin-right:8px;">' + formatPriceVal(regPrice, currency) + '</span>' +
                   '<span class="offer-price" style="font-size:1.35rem;font-weight:800;color:#b85c38;">' + formatPriceVal(offPrice, currency) + '</span>';
          } else if (hasOff) {
            html = '<span class="offer-price" style="font-size:1.35rem;font-weight:800;color:#b85c38;">' + formatPriceVal(offPrice, currency) + '</span>';
          } else if (hasReg) {
            html = '<span class="current-price" style="font-size:1.35rem;font-weight:800;color:#2b2118;">' + formatPriceVal(regPrice, currency) + '</span>';
          } else {
            html = '<span class="contact-price" style="font-size:0.95rem;font-weight:600;color:#7c7267;font-style:italic;">Quote on Request</span>';
          }
          pricingEl.innerHTML = html;

          if (isUserAction) {
            pricingEl.style.transition = 'transform 0.2s ease';
            pricingEl.style.transform = 'scale(1.05)';
            setTimeout(function() { pricingEl.style.transform = 'scale(1)'; }, 200);
          }
        }
      });
    }

    // Attach click listeners
    toggleBtns.forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        var curr = btn.getAttribute('data-currency');
        applyCurrencySelection(curr, true);
      });
    });

    // Check localStorage preference
    try {
      var saved = localStorage.getItem('feeble_currency');
      if (saved && ['INR', 'USD', 'GBP'].includes(saved.toUpperCase())) {
        var activeBtn = toggleGroup.querySelector('.currency-toggle-btn.active');
        var currActive = activeBtn ? activeBtn.getAttribute('data-currency') : null;
        if (currActive !== saved) {
          applyCurrencySelection(saved, false);
        }
      }
    } catch (e) {}
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductsCurrencySwitcher);
  } else {
    initProductsCurrencySwitcher();
  }
})();
</script>

