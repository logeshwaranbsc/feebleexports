<!-- Get Quote Interactive Modal -->
<div class="modal-overlay" id="quoteModal">
  <div class="modal-card">
    <button class="modal-close" id="closeQuoteModal">&times;</button>
    <span class="section-tag">EXPORT INQUIRY</span>
    <h3 class="section-heading" style="font-size: 1.8rem; margin-bottom: 20px;">Request a Quote</h3>
    
    <form id="quoteForm">
      <div class="form-group">
        <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; display: block;">Full Name *</label>
        <input type="text" name="name" class="form-input" placeholder="e.g. John Doe" required>
      </div>

      <div class="form-group">
        <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; display: block;">Email Address *</label>
        <input type="email" name="email" class="form-input" placeholder="john@example.com" required>
      </div>

      <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
        <div>
          <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; display: block;">Phone Number</label>
          <input type="text" name="phone" class="form-input" placeholder="+1 (555) 000-0000">
        </div>
        <div>
          <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; display: block;">Country</label>
          <input type="text" name="country" class="form-input" placeholder="e.g. United States">
        </div>
      </div>

      <div class="form-group" style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 12px;">
        <div>
          <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; display: block;">Product Category *</label>
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
          <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; display: block;">Est. Quantity (Units)</label>
          <input type="number" name="quantity" class="form-input" placeholder="100" min="10" value="100">
        </div>
      </div>

      <div class="form-group">
        <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; display: block;">Custom Specifications / Message</label>
        <textarea name="notes" class="form-textarea" placeholder="Specify sizes (e.g. 18x30), colors, or custom logo print request..."></textarea>
      </div>

      <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; margin-top: 10px;">
        Submit Quote Request &rarr;
      </button>
    </form>
  </div>
</div>
