<?php
/**
 * Blog Detail View
 * Shows full article content, meta details, author profile, and related/recent articles.
 */
?>
<article class="blog-detail-page">

  <!-- Header & Breadcrumb -->
  <header class="blog-detail-header">
    <div class="container">
      <nav class="blog-breadcrumb" aria-label="Breadcrumb">
        <a href="/home">Home</a>
        <span class="crumb-sep">/</span>
        <a href="/home#home-blogs">Blogs &amp; Journal</a>
        <span class="crumb-sep">/</span>
        <span class="crumb-current"><?= htmlspecialchars($blog['title'] ?? '') ?></span>
      </nav>

      <div class="blog-detail-badge-wrap">
        <span class="blog-detail-category-badge"><?= htmlspecialchars($blog['category'] ?? 'Export Journal') ?></span>
      </div>

      <h1 class="blog-detail-title"><?= htmlspecialchars($blog['title'] ?? '') ?></h1>

      <div class="blog-detail-meta-bar">
        <div class="blog-author-info">
          <div class="blog-author-avatar">🌿</div>
          <div>
            <span class="blog-author-name"><?= htmlspecialchars($blog['author'] ?? 'Kavimayil Venkatachalam') ?></span>
            <span class="blog-author-role">FEEBLE EXPORTS, Namakkal</span>
          </div>
        </div>

        <div class="blog-meta-right">
          <span class="blog-meta-date">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <?= date('F d, Y', strtotime($blog['created_at'] ?? 'now')) ?>
          </span>
          <span class="blog-meta-sep">•</span>
          <span class="blog-meta-readtime">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            3 min read
          </span>
        </div>
      </div>
    </div>
  </header>

  <!-- Hero Visual -->
  <section class="blog-detail-visual-section">
    <div class="container">
      <div class="blog-hero-image-wrap">
        <img 
          src="<?= htmlspecialchars($blog['image'] ?: '/assets/images/01_plain_handloom.png') ?>" 
          alt="<?= htmlspecialchars($blog['title'] ?? '') ?>" 
          class="blog-hero-image"
          onerror="this.onerror=null; this.src='/assets/images/01_plain_handloom.png';"
        >
      </div>
    </div>
  </section>

  <!-- Main Content Layout -->
  <section class="blog-detail-body-section">
    <div class="container blog-content-grid">
      
      <!-- Article Body -->
      <div class="blog-article-main">
        <?php if (!empty($blog['excerpt'])): ?>
          <div class="blog-lead-box">
            <p class="blog-lead-text"><?= htmlspecialchars($blog['excerpt']) ?></p>
          </div>
        <?php endif; ?>

        <div class="blog-rich-content">
          <?= nl2br(htmlspecialchars($blog['content'] ?? '')) ?>
        </div>

        <!-- Highlight Callout Box -->
        <div class="blog-callout-box">
          <div class="blog-callout-icon">🌱</div>
          <div class="blog-callout-content">
            <h4 class="blog-callout-title">Commitment to Sustainable Manufacturing</h4>
            <p class="blog-callout-text">
              At FEEBLE EXPORTS, every coir doormat and roll is handcrafted from pure, biodegradable coconut husk fibres sourced ethically across Namakkal and Tamil Nadu. We supply durable, climate-friendly entrance solutions tailored to global standards.
            </p>
          </div>
        </div>

        <!-- Share / Return -->
        <div class="blog-footer-nav">
          <a href="/home#home-blogs" class="btn-secondary">
            &larr; Back to All Articles
          </a>
          <button class="btn-primary btn-quote">
            Request an Export Quote &rarr;
          </button>
        </div>
      </div>

      <!-- Sidebar -->
      <aside class="blog-sidebar">
        <!-- Author Profile Card -->
        <div class="blog-sidebar-card">
          <h4 class="sidebar-card-title">About the Author</h4>
          <div class="sidebar-author-profile">
            <div class="sidebar-avatar-large">KV</div>
            <div>
              <strong class="sidebar-author-name"><?= htmlspecialchars($blog['author'] ?? 'Kavimayil Venkatachalam') ?></strong>
              <p class="sidebar-author-bio">Proprietress &amp; Export Lead at FEEBLE EXPORTS, leading natural coir manufacturing and international trade from Namakkal, Tamil Nadu, India.</p>
            </div>
          </div>
        </div>

        <!-- Recent Articles -->
        <?php if (!empty($recentBlogs)): ?>
          <div class="blog-sidebar-card">
            <h4 class="sidebar-card-title">More Articles</h4>
            <div class="sidebar-recent-list">
              <?php foreach ($recentBlogs as $rec): ?>
                <a href="/blog/<?= htmlspecialchars($rec['slug'] ?? '') ?>" class="sidebar-recent-item">
                  <div class="sidebar-recent-thumb-wrap">
                    <img 
                      src="<?= htmlspecialchars($rec['image'] ?: '/assets/images/01_plain_handloom.png') ?>" 
                      alt="<?= htmlspecialchars($rec['title'] ?? '') ?>"
                      class="sidebar-recent-thumb"
                      onerror="this.onerror=null; this.src='/assets/images/01_plain_handloom.png';"
                    >
                  </div>
                  <div class="sidebar-recent-details">
                    <span class="sidebar-recent-date"><?= date('M d, Y', strtotime($rec['created_at'] ?? 'now')) ?></span>
                    <h5 class="sidebar-recent-title"><?= htmlspecialchars($rec['title'] ?? '') ?></h5>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>

        <!-- Quick Contact Card -->
        <div class="blog-sidebar-card sidebar-cta-box">
          <span class="sidebar-cta-icon">📦</span>
          <h4 class="sidebar-cta-title">Looking for Bulk Coir Mats?</h4>
          <p class="sidebar-cta-text">
            We supply Plain Handloom, Tufted, Creel &amp; Rod, and PVC Backed mats customized in both cm and inches for export worldwide.
          </p>
          <button class="btn-primary btn-quote w-full" style="width: 100%;">
            Get Free Quote &rarr;
          </button>
        </div>
      </aside>

    </div>
  </section>

</article>
