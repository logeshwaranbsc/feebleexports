<!-- Get Quote Interactive Modal -->
<div class="modal-overlay" id="quoteModal" role="dialog" aria-modal="true" aria-labelledby="quoteModalTitle">
  <div class="modal-card">
    <div class="modal-header">
      <div>
        <span class="section-tag" style="margin-bottom: 2px; display: inline-block;">EXPORT INQUIRY</span>
        <h3 class="section-heading" id="quoteModalTitle" style="font-size: 1.55rem; margin-bottom: 0; line-height: 1.2;">Request a Quote</h3>
      </div>
      <button class="modal-close" id="closeQuoteModal" aria-label="Close quote modal" type="button">&times;</button>
    </div>

    <div class="modal-body">
      <form id="quoteForm">
        <div class="form-group modal-grid-2">
          <div>
            <label class="modal-label">Full Name *</label>
            <input type="text" name="name" class="form-input" placeholder="e.g. John Doe" required>
          </div>
          <div>
            <label class="modal-label">Email Address *</label>
            <input type="email" name="email" class="form-input" placeholder="john@example.com" required>
          </div>
        </div>

        <div class="form-group modal-grid-2">
          <div>
            <label class="modal-label">Phone Number</label>
            <input type="text" name="phone" class="form-input" placeholder="+1 (555) 000-0000">
          </div>
          <div>
            <label class="modal-label">Country</label>
            <input type="text" name="country" class="form-input" placeholder="e.g. United States">
          </div>
        </div>

        <div class="form-group modal-grid-2-custom">
          <div>
            <label class="modal-label">Product Category *</label>
            <select name="product" class="form-select" required>
              <option value="">-- Select Coir Product --</option>
              <option value="Plain and Handloom">Plain and Handloom</option>
              <option value="Tufted Coir Mats">Tufted Coir Mats</option>
              <option value="Creel and Rod">Creel and Rod</option>
              <option value="Rope and Braided">Rope and Braided</option>
              <option value="PVC - Backed">PVC - Backed</option>
              <option value="Rubber-Backed">Rubber-Backed</option>
              <option value="Latex-Backed">Latex-Backed</option>
              <option value="Printed and Logo">Printed and Logo</option>
              <option value="Coir Carpet & Rolls">Coir Carpet & Rolls</option>
              <option value="Custom Design / Size">Custom Design / Size</option>
            </select>
          </div>
          <div>
            <label class="modal-label">Est. Quantity (Units)</label>
            <input type="number" name="quantity" class="form-input" placeholder="100" min="10" value="100">
          </div>
        </div>

        <div class="form-group">
          <label class="modal-label">Preferred Size (cms / inches)</label>
          <input type="text" name="custom_size" class="form-input" placeholder="e.g. 40x60 cm / 16x24 in, 45x75 cm, or custom dimensions">
        </div>

        <div class="form-group">
          <label class="modal-label">Custom Specifications / Message</label>
          <textarea name="notes" class="form-textarea modal-textarea" rows="2" placeholder="Specify colors, backing options, logo print, or custom requirements..."></textarea>
        </div>

        <div class="form-group recaptcha-form-group">
          <div class="g-recaptcha" data-sitekey="<?= htmlspecialchars(\App\Services\RecaptchaService::getSiteKey()) ?>"></div>
        </div>

        <div class="modal-actions">
          <button type="submit" class="btn-primary" style="width: 100%; justify-content: center;">
            Submit Quote Request &rarr;
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
