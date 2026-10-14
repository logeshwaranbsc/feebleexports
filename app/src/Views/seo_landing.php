<?php
/**
 * Dynamic Public SEO Landing Page View
 * Renders all sections: Hero with lead form, Why Choose Us, About Us, Content Block 1,
 * Selected Projects, Content Block 2, Testimonials, FAQs, and Preview banner.
 * Streamlined vertical spacing for a cohesive, compact, premium layout.
 */

$isPreview = $isPreview ?? false;
$projects = $projects ?? [];
$testimonials = $page['testimonials'] ?? [];
$faqs = $page['faqs'] ?? [];
?>

<div class="seo-landing-page">

  <?php if ($isPreview): ?>
    <!-- Admin Preview Notification Banner -->
    <div class="seo-preview-ribbon">
      <div class="container" style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <div style="display: flex; align-items: center; gap: 0.75rem;">
          <span style="font-size: 1.25rem;">👁️</span>
          <div>
            <strong>Draft Preview Mode:</strong>
            <span>Viewing '<?= htmlspecialchars($page['title']) ?>' (Status: <strong style="text-transform: uppercase;"><?= htmlspecialchars($page['status'] ?? 'draft') ?></strong>). This page is visible only to logged-in administrators.</span>
          </div>
        </div>
        <div>
          <a href="/admin/seo/<?= htmlspecialchars($page['id']) ?>/edit" class="btn-primary btn-sm" style="background: #10b981; color: #fff; text-decoration: none; padding: 0.4rem 0.9rem; border-radius: 6px; font-weight: 600;">
            ✏️ Edit in Admin
          </a>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- 1. HERO SECTION -->
  <section class="seo-landing-hero" style="background-image: linear-gradient(135deg, rgba(20, 16, 12, 0.88) 0%, rgba(36, 31, 26, 0.85) 100%), url('<?= htmlspecialchars($page['hero_image'] ?: '/assets/images/01_plain_handloom.png') ?>');">
    <div class="container seo-hero-grid">
      
      <!-- Left Hero Copy -->
      <div class="seo-hero-copy">
        <div class="seo-hero-eyebrow-tag">
          <span>FEEBLE EXPORTS • SUSTAINABLE COIR</span>
        </div>
        
        <h1 class="seo-hero-title">
          <?= htmlspecialchars($page['hero_heading'] ?: $page['title']) ?>
        </h1>
        
        <?php if (!empty($page['hero_subheading'])): ?>
          <p class="seo-hero-subheading">
            <?= htmlspecialchars($page['hero_subheading']) ?>
          </p>
        <?php endif; ?>

        <!-- Quick Trust Badges -->
        <div class="seo-hero-trust-row">
          <div class="seo-trust-badge">
            <span class="trust-icon">🌱</span>
            <span>100% Biodegradable</span>
          </div>
          <div class="seo-trust-badge">
            <span class="trust-icon">📐</span>
            <span>Custom cm &amp; inches</span>
          </div>
          <div class="seo-trust-badge">
            <span class="trust-icon">🌍</span>
            <span>Global Exporting</span>
          </div>
        </div>
      </div>

      <!-- Right Hero Lead / Quote Form Card -->
      <div class="seo-hero-form-col">
        <div class="seo-quote-card">
          <h3 class="seo-form-title"><?= htmlspecialchars($page['hero_form_title'] ?: 'Get a Quote') ?></h3>
          <p class="seo-form-subtitle">Fill in your requirements below for wholesale and custom pricing.</p>
          
          <form action="/api/quote" method="POST" class="ajax-quote-form" id="seoHeroQuoteForm">
            <div class="seo-form-field">
              <label for="lead_name">Your Name *</label>
              <input type="text" id="lead_name" name="name" required placeholder="e.g. John Doe">
            </div>

            <div class="seo-form-field">
              <label for="lead_email">Work Email *</label>
              <input type="email" id="lead_email" name="email" required placeholder="john@company.com">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
              <div class="seo-form-field">
                <label for="lead_phone">Phone / WhatsApp</label>
                <input type="tel" id="lead_phone" name="phone" placeholder="+1 234 567 8900">
              </div>

              <div class="seo-form-field">
                <label for="lead_country">Destination Country</label>
                <input type="text" id="lead_country" name="country" placeholder="e.g. United Kingdom">
              </div>
            </div>

            <div class="seo-form-field">
              <label for="lead_product">Preferred Matting Type</label>
              <input type="text" id="lead_product" name="product" value="<?= htmlspecialchars($page['title']) ?>">
            </div>

            <div class="seo-form-field">
              <label for="lead_notes">Estimated Quantity &amp; Specifications</label>
              <textarea id="lead_notes" name="notes" rows="2" placeholder="e.g. 500 pcs, 45x75 cm, custom logo..."></textarea>
            </div>

            <button type="submit" class="btn-primary w-full" style="width: 100%; justify-content: center; padding: 13px; font-weight: 700;">
              Submit Inquiry &rarr;
            </button>
            
            <div class="form-feedback-msg" style="display: none; margin-top: 10px; font-size: 0.85rem; text-align: center;"></div>
          </form>
        </div>
      </div>

    </div>
  </section>

  <!-- 2. WHY CHOOSE US SECTION -->
  <section class="section-padding bg-cream seo-why-choose-section">
    <div class="container grid-2-col align-center">
      
      <div class="seo-visual-wrap">
        <img 
          src="<?= htmlspecialchars($page['why_choose_image'] ?: '/assets/images/02_tufted_pattern.png') ?>" 
          alt="<?= htmlspecialchars($page['why_choose_heading']) ?>" 
          class="seo-section-img"
          loading="lazy"
          onerror="this.onerror=null; this.src='/assets/images/02_tufted_pattern.png';"
        >
      </div>

      <div class="seo-copy-wrap">
        <span class="section-tag tag-terracotta">SUSTAINABLE ADVANTAGE</span>
        <h2 class="section-heading text-dark"><?= htmlspecialchars($page['why_choose_heading'] ?: 'Why Choose Feeble Exports') ?></h2>
        <div class="seo-rich-text">
          <?= $page['why_choose_content'] ?>
        </div>
        <div style="margin-top: 18px;">
          <button class="btn-primary btn-quote">Request Specifications &rarr;</button>
        </div>
      </div>

    </div>
  </section>

  <!-- 3. ABOUT US SECTION -->
  <section class="section-padding bg-white seo-about-section">
    <div class="container grid-2-col align-center">
      
      <div class="seo-copy-wrap">
        <span class="section-tag tag-forest">HERITAGE &amp; MANUFACTURING</span>
        <h2 class="section-heading text-dark"><?= htmlspecialchars($page['about_heading'] ?: 'About Feeble Exports') ?></h2>
        <div class="seo-rich-text">
          <?= $page['about_content'] ?>
        </div>
        <div style="margin-top: 18px;">
          <a href="/our-story" class="btn-secondary">Read Our Story &rarr;</a>
        </div>
      </div>

      <div class="seo-visual-wrap">
        <img 
          src="<?= htmlspecialchars($page['about_image'] ?: '/assets/images/03_creel_and_rod.png') ?>" 
          alt="<?= htmlspecialchars($page['about_heading']) ?>" 
          class="seo-section-img"
          loading="lazy"
          onerror="this.onerror=null; this.src='/assets/images/03_creel_and_rod.png';"
        >
      </div>

    </div>
  </section>

  <!-- 4. CONTENT BLOCK 1 (EDITORIAL HIGHLIGHT) -->
  <?php if (!empty($page['content_block_1_heading']) || !empty($page['content_block_1_content'])): ?>
    <section class="section-padding bg-warm seo-content-block-section">
      <div class="container text-center" style="max-width: 860px; margin: 0 auto;">
        <?php if (!empty($page['content_block_1_heading'])): ?>
          <h2 class="section-heading text-dark" style="margin-bottom: 12px;">
            <?= htmlspecialchars($page['content_block_1_heading']) ?>
          </h2>
        <?php endif; ?>
        <div class="seo-rich-text editorial-text" style="font-size: 1.05rem; line-height: 1.7; color: #4a3e35;">
          <?= $page['content_block_1_content'] ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- 5. FEATURED PROJECTS / PRODUCTS SECTION -->
  <?php if (!empty($projects)): ?>
    <section class="section-padding bg-cream seo-projects-section" id="featured-products">
      <div class="container">
        <div class="seo-section-header-compact">
          <span class="section-tag tag-gold">SELECTED COLLECTIONS</span>
          <h2 class="section-heading text-dark">Associated Export Products &amp; Designs</h2>
          <p class="section-subheading" style="max-width: 660px; margin: 0 auto; color: #6b5e52; font-size: 0.95rem;">
            Hand-woven and precision-tufted natural coconut fibre door mats crafted for residential and heavy commercial entryways.
          </p>
        </div>

        <div class="seo-projects-showcase-grid">
          <?php foreach ($projects as $prod): ?>
            <div class="seo-project-card">
              <div class="seo-project-thumb-wrap">
                <img 
                  src="<?= htmlspecialchars($prod['image'] ?: '/assets/images/01_plain_handloom.png') ?>" 
                  alt="<?= htmlspecialchars($prod['name']) ?>" 
                  class="seo-project-thumb"
                  loading="lazy"
                  onerror="this.onerror=null; this.src='/assets/images/01_plain_handloom.png';"
                >
                <span class="seo-project-badge"><?= htmlspecialchars($prod['category'] ?? 'Natural Coir') ?></span>
              </div>
              <div class="seo-project-card-body">
                <h3 class="seo-project-title"><?= htmlspecialchars($prod['name']) ?></h3>
                <p class="seo-project-desc"><?= htmlspecialchars($prod['description'] ?? '100% natural organic coconut husk fibres.') ?></p>
                
                <div class="seo-project-footer">
                  <span style="font-size: 0.8rem; color: #8c7b6d;">Standard &amp; Custom Sizes</span>
                  <button class="btn-quote seo-project-inquire-btn" data-product="<?= htmlspecialchars($prod['name']) ?>">
                    Inquire &rarr;
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- 6. CONTENT BLOCK 2 -->
  <?php if (!empty($page['content_block_2_content'])): ?>
    <section class="section-padding bg-white seo-content-block-2">
      <div class="container" style="max-width: 880px; margin: 0 auto;">
        <div class="content-block-2-box">
          <?= $page['content_block_2_content'] ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- 7. TESTIMONIALS SECTION -->
  <?php if (!empty($page['show_testimonials']) && !empty($testimonials)): ?>
    <section class="section-padding bg-cream seo-testimonials-section">
      <div class="container">
        <div class="seo-section-header-compact">
          <span class="section-tag tag-terracotta">GLOBAL CLIENTS</span>
          <h2 class="section-heading text-dark">What International Buyers Say</h2>
          <p class="section-subheading" style="color: #6b5e52; max-width: 580px; margin: 0 auto; font-size: 0.95rem;">
            Feedback from overseas distributors, interior designers, and architects sourcing coir mats from FEEBLE EXPORTS.
          </p>
        </div>

        <div class="seo-testimonials-grid">
          <?php foreach ($testimonials as $t): ?>
            <div class="seo-testimonial-card">
              <div class="seo-testimonial-rating">
                <?= str_repeat('★', max(1, min(5, intval($t['rating'] ?? 5)))) ?>
              </div>
              <p class="seo-testimonial-quote">
                &ldquo;<?= htmlspecialchars($t['text']) ?>&rdquo;
              </p>
              <div class="seo-testimonial-author">
                <div class="seo-author-avatar-circle">
                  <?= strtoupper(substr($t['name'] ?? 'A', 0, 1)) ?>
                </div>
                <div>
                  <strong class="seo-author-name"><?= htmlspecialchars($t['name']) ?></strong>
                  <?php if (!empty($t['role'])): ?>
                    <span class="seo-author-role"><?= htmlspecialchars($t['role']) ?></span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- 8. FAQ SECTION -->
  <?php if (!empty($page['show_faq']) && !empty($faqs)): ?>
    <section class="section-padding bg-white seo-faq-section" id="faq">
      <div class="container" style="max-width: 840px; margin: 0 auto;">
        <div class="seo-section-header-compact">
          <span class="section-tag tag-forest">HAVE QUESTIONS?</span>
          <h2 class="section-heading text-dark">Frequently Asked Questions</h2>
          <p class="section-subheading" style="color: #6b5e52; font-size: 0.95rem;">
            Everything you need to know about specifications, shipping times, and custom branding.
          </p>
        </div>

        <div class="seo-accordion-wrapper">
          <?php foreach ($faqs as $fIndex => $f): ?>
            <details class="seo-accordion-item" <?= $fIndex === 0 ? 'open' : '' ?>>
              <summary class="seo-accordion-summary">
                <span class="seo-accordion-question"><?= htmlspecialchars($f['question']) ?></span>
                <span class="seo-accordion-icon">+</span>
              </summary>
              <div class="seo-accordion-body">
                <p><?= htmlspecialchars($f['answer']) ?></p>
              </div>
            </details>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <!-- 9. BOTTOM CALL TO ACTION BANNER -->
  <section class="section-padding seo-cta-banner" style="background: linear-gradient(135deg, #1f3d2b 0%, #15291d 100%); color: #ffffff;">
    <div class="container text-center" style="max-width: 780px; margin: 0 auto;">
      <h2 style="font-family: 'Playfair Display', Georgia, serif; font-size: clamp(1.85rem, 3.2vw, 2.4rem); margin-bottom: 0.85rem; color: #ffffff;">
        Ready to Source Sustainable Coir Mats?
      </h2>
      <p style="font-size: 1rem; line-height: 1.6; color: #d0e2d5; margin-bottom: 1.5rem;">
        Connect directly with our export manufacturing headquarters in Namakkal, Tamil Nadu, India for catalog specifications and wholesale quotations.
      </p>
      <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
        <button class="btn-primary btn-quote" style="background-color: #c0643c; border-color: #c0643c; padding: 12px 24px;">
          Request Free Quote &rarr;
        </button>
        <a href="/contact" class="btn-secondary" style="background: rgba(255,255,255,0.12); color: #fff; border-color: rgba(255,255,255,0.3); text-decoration: none; padding: 12px 24px;">
          Contact Export Desk
        </a>
      </div>
    </div>
  </section>

</div>
