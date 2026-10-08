<?php
$currentPage = $currentPage ?? 'home';
?>
<!-- Main Navigation Header -->
<header class="header-wrapper">
  <nav class="navbar floating-navbar" id="mainNavbar">
    <div class="navbar-glass-card">
      <!-- Brand Logo -->
      <a href="/home" class="brand-logo">
        <div class="logo-wrapper">
          <img src="/assets/images/Feeble%20Logo.png" alt="FEEBLE EXPORTS" class="logo-img">
        </div>
        <div class="brand-text">
          <span class="brand-name">FEEBLE EXPORTS</span>
          <span class="brand-tagline">Not a Big Deal</span>
        </div>
      </a>

      <!-- Central Floating Nav Capsule -->
      <div class="nav-capsule" id="navCapsule">
        <ul class="nav-menu">
          <li><a href="/home" class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>">Home</a></li>
          <li><a href="/discover" class="nav-link <?= $currentPage === 'discover' ? 'active' : '' ?>">Discover Us</a></li>
          <li><a href="/products" class="nav-link <?= $currentPage === 'products' ? 'active' : '' ?>">Products</a></li>
          <li><a href="/our-story" class="nav-link <?= $currentPage === 'story' ? 'active' : '' ?>">Our Story</a></li>
          <li><a href="/contact" class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact Us</a></li>
          <li class="nav-cta-item">
            <button class="btn-quote" id="openQuoteBtn">
              <span>Get Quote</span>
              <svg class="cta-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </button>
          </li>
        </ul>
      </div>

      <!-- Mobile Hamburger Toggle -->
      <button class="mobile-toggle" id="mobileNavToggle" aria-label="Toggle navigation menu">
        <span class="hamburger-bar bar-1"></span>
        <span class="hamburger-bar bar-2"></span>
        <span class="hamburger-bar bar-3"></span>
      </button>
    </div>
  </nav>
</header>

