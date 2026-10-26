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

/* -------------------------------------------------------------
   MOBILE RESPONSIVE BREAKPOINTS (<= 860px, <= 480px, <= 350px)
   ------------------------------------------------------------- */
@media (max-width: 860px) {
  .products-toolbar {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 14px !important;
    padding: 12px !important;
    margin-bottom: 22px !important;
    overflow: visible !important;
  }

  /* All categories fully visible on mobile via clean responsive wrapping */
  .filter-tabs {
    display: flex !important;
    flex-wrap: wrap !important;
    overflow: visible !important;
    gap: 7px !important;
    padding: 0 !important;
    width: 100% !important;
  }

  .filter-btn {
    flex-shrink: 0 !important;
    padding: 6px 13px !important;
    font-size: 0.81rem !important;
    white-space: nowrap !important;
    min-height: 34px !important;
  }
}

@media (max-width: 480px) {
  .products-section {
    padding: 36px 12px !important;
  }

  .products-header-desc {
    font-size: 0.90rem !important;
    margin-bottom: 18px !important;
  }

  .products-toolbar {
    padding: 10px 10px 12px 10px !important;
    border-radius: 14px !important;
    gap: 12px !important;
    margin-bottom: 18px !important;
  }

  .filter-tabs {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 6px !important;
    width: 100% !important;
    overflow: visible !important;
  }

  .filter-btn {
    padding: 5px 11px !important;
    font-size: 0.77rem !important;
    min-height: 31px !important;
    border-radius: 9999px !important;
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
}

@media (max-width: 350px) {
  .filter-btn {
    padding: 4px 9px !important;
    font-size: 0.74rem !important;
    min-height: 28px !important;
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

    <!-- Products Toolbar: Category Filters -->
    <div class="products-toolbar">
      <!-- Filter Buttons (Swipeable on Mobile) -->
      <div class="filter-tabs" role="tablist" aria-label="Filter products by category">
        <?php foreach ($categories as $cat): ?>
          <button class="filter-btn <?= $cat['slug'] === 'all' ? 'active' : '' ?>" data-category="<?= htmlspecialchars($cat['slug']) ?>">
            <?= htmlspecialchars($cat['name']) ?>
          </button>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Layout: Grid + Spec Sidebar -->
    <div class="products-layout">
      
      <!-- Products Grid -->
      <div class="products-grid">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $product): ?>
            <div class="product-card" data-category="<?= htmlspecialchars($product['category_slug']) ?>">
              <div class="product-thumb">
                <img src="<?= htmlspecialchars(!empty($product['image']) ? $product['image'] : '/assets/images/01_plain_handloom.png') ?>" alt="<?= htmlspecialchars($product['name']) ?>" onerror="this.src='/assets/images/01_plain_handloom.png'">
              </div>
              <div class="product-info">
                <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
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

