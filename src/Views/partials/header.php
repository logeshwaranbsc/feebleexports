<?php
$currentPage = $currentPage ?? 'home';
?>
<!-- Main Navigation -->
<nav class="navbar">
  <a href="/home" class="brand-logo">
    <img src="/assets/images/Feeble%20Logo.png" alt="FEEBLE EXPORTS" class="logo-img">
    <div class="brand-text">
      <span class="brand-name">FEEBLE EXPORTS</span>
      <span class="brand-tagline">Not a Big Deal</span>
    </div>
  </a>

  <div class="nav-capsule">
    <ul class="nav-menu">
      <li><a href="/home" class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>">Home</a></li>
      <li><a href="/discover" class="nav-link <?= $currentPage === 'discover' ? 'active' : '' ?>">Discover Us</a></li>
      <li><a href="/products" class="nav-link <?= $currentPage === 'products' ? 'active' : '' ?>">Products</a></li>
      <li><a href="/our-story" class="nav-link <?= $currentPage === 'story' ? 'active' : '' ?>">Our Story</a></li>
      <li><a href="/contact" class="nav-link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact Us</a></li>
      <li>
        <button class="btn-quote" id="openQuoteBtn">Get Quote &rarr;</button>
      </li>
    </ul>
  </div>
</nav>
