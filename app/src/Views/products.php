<!-- Page 3 - Products -->
<section class="products-section">
  <div class="products-container">
    
    <span class="section-tag">OUR PRODUCTS</span>
    <h2 class="section-heading">Coir Mats Collection</h2>

    <p class="products-header-desc">
      A wide range of natural, durable and sustainable coir mat products suitable for homes, offices, gardens and industries. Customization available in colours, designs and sizes.
    </p>

    <!-- Filter Buttons -->
    <div class="filter-tabs">
      <?php foreach ($categories as $cat): ?>
        <button class="filter-btn <?= $cat['slug'] === 'all' ? 'active' : '' ?>" data-category="<?= htmlspecialchars($cat['slug']) ?>">
          <?= htmlspecialchars($cat['name']) ?>
        </button>
      <?php endforeach; ?>
    </div>

    <!-- Layout: Grid + Spec Sidebar -->
    <div class="products-layout">
      
      <!-- Products Grid -->
      <div class="products-grid">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $product): ?>
            <?php 
              $hasPrice = !empty($product['price']) && (float)$product['price'] > 0;
              $hasOffer = !empty($product['offer_price']) && (float)$product['offer_price'] > 0;
              $isOfferValid = $hasOffer && $hasPrice && (float)$product['offer_price'] < (float)$product['price'];
            ?>
            <div class="product-card" data-category="<?= htmlspecialchars($product['category_slug']) ?>">
              <?php if ($isOfferValid): ?>
                <div class="product-offer-badge">
                  Offer
                </div>
              <?php endif; ?>
              <div class="product-thumb">
                <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
              </div>
              <div class="product-info">
                <h3 class="product-title"><?= htmlspecialchars($product['name']) ?></h3>
                
                <div class="product-pricing">
                  <?php if ($isOfferValid): ?>
                    <span class="old-price">₹<?= number_format((float)$product['price'], 0) ?></span>
                    <span class="offer-price">₹<?= number_format((float)$product['offer_price'], 0) ?></span>
                  <?php elseif ($hasOffer): ?>
                    <span class="offer-price">₹<?= number_format((float)$product['offer_price'], 0) ?></span>
                  <?php elseif ($hasPrice): ?>
                    <span class="current-price">₹<?= number_format((float)$product['price'], 0) ?></span>
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
              <li class="size-item"><?= htmlspecialchars($sz) ?></li>
            <?php endforeach; ?>
          </ul>
          <p class="size-subtext">(Inches for US & Canada / Centimetres for UK & Europe)</p>
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
          <span>+91 8630489610 | +44 7900558965</span>
        </div>
      </div>
    </div>
  </div>
</section>

