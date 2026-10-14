document.addEventListener('DOMContentLoaded', () => {
    initAdminSidebar();
    initAutoDismissAlerts();
    initDeleteConfirmations();
    initSlugGenerator();
    initFileUploadInputs();
    initSeoEditor();
});

// Mobile Sidebar Toggle
function initAdminSidebar() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.admin-sidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 992 && !sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
    }
}

// Auto dismiss success/error alerts after 5 seconds
function initAutoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert-banner');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
}

// Confirm deletion prompt
function initDeleteConfirmations() {
    const deleteForms = document.querySelectorAll('.form-delete-confirm');
    deleteForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const itemName = form.getAttribute('data-item-name') || 'item';
            if (!confirm(`Are you sure you want to delete this ${itemName}? This action cannot be undone.`)) {
                e.preventDefault();
            }
        });
    });
}

// Auto generate slug from Title or Name field
function initSlugGenerator() {
    const titleInput = document.getElementById('title') || document.getElementById('name');
    const slugInput = document.getElementById('slug') || document.getElementById('id');

    if (titleInput && slugInput && !slugInput.getAttribute('data-manual')) {
        titleInput.addEventListener('input', () => {
            if (!slugInput.value || slugInput.getAttribute('data-auto') === 'true') {
                slugInput.value = titleInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^\w\s-]/g, '')
                    .replace(/[\s_-]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.setAttribute('data-auto', 'true');
            }
        });

        slugInput.addEventListener('input', () => {
            slugInput.setAttribute('data-auto', 'false');
        });
    }
}

// Inline Form File Upload handler
function initFileUploadInputs() {
    document.querySelectorAll('.admin-file-upload-input, .admin-s3-upload-input').forEach(input => {
        input.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            const targetSelector = input.getAttribute('data-target');
            const previewSelector = input.getAttribute('data-preview');
            const targetInput = document.querySelector(targetSelector);
            const previewImg = previewSelector ? document.querySelector(previewSelector) : null;
            const container = input.closest('.form-group') || input.parentElement;
            const statusMsg = container.querySelector('.upload-status-msg');

            if (statusMsg) {
                statusMsg.style.display = 'block';
                statusMsg.style.color = 'var(--admin-primary)';
                statusMsg.innerText = '⏳ Uploading file to server storage...';
            }

            const formData = new FormData();
            formData.append('file', file);

            try {
                const response = await fetch('/admin/api/upload', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    if (targetInput) {
                        targetInput.value = result.url;
                        targetInput.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                    if (previewImg) {
                        previewImg.src = result.url;
                        previewImg.style.display = 'block';
                    }
                    if (statusMsg) {
                        statusMsg.style.color = 'var(--admin-primary)';
                        statusMsg.innerText = '✓ Uploaded to ' + (result.storage || 'Local Storage') + '!';
                        setTimeout(() => { statusMsg.style.display = 'none'; }, 4000);
                    }
                } else {
                    if (statusMsg) {
                        statusMsg.style.color = 'var(--admin-danger)';
                        statusMsg.innerText = '⚠️ ' + (result.error || 'Upload failed.');
                    }
                }
            } catch (err) {
                if (statusMsg) {
                    statusMsg.style.color = 'var(--admin-danger)';
                    statusMsg.innerText = '⚠️ Upload request failed: ' + err.message;
                }
            } finally {
                input.value = '';
            }
        });
    });
}

// SEO Landing Page Editor Interactivity
function initSeoEditor() {
    const seoForm = document.getElementById('seoPageForm');
    if (!seoForm) return;

    // 1. Meta Title & Description Live Character Counters
    const metaTitle = document.getElementById('meta_title');
    const metaTitleCount = document.getElementById('metaTitleCount');
    if (metaTitle && metaTitleCount) {
        metaTitle.addEventListener('input', () => {
            metaTitleCount.textContent = metaTitle.value.length;
        });
    }

    const metaDesc = document.getElementById('meta_description');
    const metaDescCount = document.getElementById('metaDescCount');
    if (metaDesc && metaDescCount) {
        metaDesc.addEventListener('input', () => {
            metaDescCount.textContent = metaDesc.value.length;
        });
    }

    // 2. Slug & Live URL Preview
    const slugInput = document.getElementById('slug');
    const urlPreview = document.getElementById('publicUrlPreview');
    if (slugInput && urlPreview) {
        slugInput.addEventListener('input', () => {
            urlPreview.textContent = '/' + slugInput.value.replace(/^\/+/, '');
        });
    }

    // 3. Media Remove Buttons
    document.querySelectorAll('.btn-media-remove').forEach(btn => {
        btn.addEventListener('click', () => {
            const targetSel = btn.getAttribute('data-target');
            const previewSel = btn.getAttribute('data-preview');
            const targetInput = targetSel ? document.querySelector(targetSel) : null;
            const previewImg = previewSel ? document.querySelector(previewSel) : null;

            if (targetInput) targetInput.value = '';
            if (previewImg) previewImg.src = '/assets/images/01_plain_handloom.png';
        });
    });

    // 4. Rich-Text Editors
    document.querySelectorAll('.rich-text-wrapper').forEach(wrapper => {
        const toolbar = wrapper.querySelector('.rich-text-toolbar');
        const canvas = wrapper.querySelector('.rich-text-canvas');
        const textarea = wrapper.querySelector('.rich-text-textarea');
        let isSourceMode = false;

        if (!canvas || !textarea) return;

        // Toolbar actions
        if (toolbar) {
            toolbar.querySelectorAll('.rt-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (btn.classList.contains('rt-btn-source')) {
                        // Toggle source view
                        isSourceMode = !isSourceMode;
                        if (isSourceMode) {
                            textarea.value = canvas.innerHTML;
                            canvas.style.display = 'none';
                            textarea.style.display = 'block';
                            textarea.style.width = '100%';
                            textarea.style.minHeight = '140px';
                            btn.classList.add('is-active');
                        } else {
                            canvas.innerHTML = textarea.value;
                            textarea.style.display = 'none';
                            canvas.style.display = 'block';
                            btn.classList.remove('is-active');
                        }
                        return;
                    }

                    if (isSourceMode) return;

                    const command = btn.getAttribute('data-command');
                    const value = btn.getAttribute('data-value') || null;

                    if (command === 'createLink') {
                        const url = prompt('Enter link URL (e.g. https://... or /contact):', 'https://');
                        if (url) document.execCommand('createLink', false, url);
                    } else if (command === 'formatBlock') {
                        document.execCommand('formatBlock', false, value);
                    } else if (command) {
                        document.execCommand(command, false, null);
                    }
                    syncContent();
                });
            });
        }

        function syncContent() {
            if (!isSourceMode) {
                textarea.value = canvas.innerHTML;
            } else {
                canvas.innerHTML = textarea.value;
            }
        }

        canvas.addEventListener('input', syncContent);
        canvas.addEventListener('blur', syncContent);
        textarea.addEventListener('input', syncContent);

        // Sync before form submission
        seoForm.addEventListener('submit', () => {
            syncContent();
        });
    });

    // 5. Projects Multi-Picker
    const projectCards = document.querySelectorAll('.project-picker-card');
    const selectedCounter = document.getElementById('selectedProjectsCount');

    function updateProjectCounter() {
        const checked = document.querySelectorAll('.project-picker-checkbox:checked').length;
        if (selectedCounter) selectedCounter.textContent = checked;
    }

    projectCards.forEach(card => {
        card.addEventListener('click', (e) => {
            if (e.target.tagName === 'INPUT') return;
            const checkbox = card.querySelector('.project-picker-checkbox');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                card.classList.toggle('is-selected', checkbox.checked);
                updateProjectCounter();
            }
        });
    });

    // 6. Testimonials Toggle & Repeater
    const toggleTestimonials = document.getElementById('toggleTestimonials');
    const testimonialsContainer = document.getElementById('testimonialsContainer');
    const testimonialsList = document.getElementById('testimonialsList');
    const btnAddTestimonial = document.getElementById('btnAddTestimonial');

    if (toggleTestimonials && testimonialsContainer) {
        toggleTestimonials.addEventListener('change', () => {
            testimonialsContainer.style.display = toggleTestimonials.checked ? 'block' : 'none';
        });
    }

    if (btnAddTestimonial && testimonialsList) {
        btnAddTestimonial.addEventListener('click', () => {
            const nextIndex = testimonialsList.querySelectorAll('.repeater-item-card').length;
            const newCard = document.createElement('div');
            newCard.className = 'repeater-item-card';
            newCard.setAttribute('data-index', nextIndex);
            newCard.innerHTML = `
                <div class="repeater-item-header">
                    <span class="repeater-item-num">Testimonial #${nextIndex + 1}</span>
                    <button type="button" class="btn-danger-icon btn-remove-repeater" title="Delete Testimonial">✕</button>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 100px; gap: 1rem; margin-bottom: 0.75rem;">
                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" name="testimonials[${nextIndex}][name]" class="form-control" placeholder="e.g. Client Name">
                    </div>
                    <div class="form-group">
                        <label>Designation / Company</label>
                        <input type="text" name="testimonials[${nextIndex}][role]" class="form-control" placeholder="e.g. Interior Designer, London">
                    </div>
                    <div class="form-group">
                        <label>Rating</label>
                        <select name="testimonials[${nextIndex}][rating]" class="form-control">
                            <option value="5" selected>5 ★</option>
                            <option value="4">4 ★</option>
                            <option value="3">3 ★</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Testimonial Text</label>
                    <textarea name="testimonials[${nextIndex}][text]" class="form-control" rows="2" placeholder="Write testimonial feedback..."></textarea>
                </div>
            `;
            testimonialsList.appendChild(newCard);
            attachRepeaterRemover(newCard);
        });
    }

    // 7. FAQ Toggle & Repeater
    const toggleFaq = document.getElementById('toggleFaq');
    const faqContainer = document.getElementById('faqContainer');
    const faqList = document.getElementById('faqList');
    const btnAddFaq = document.getElementById('btnAddFaq');

    if (toggleFaq && faqContainer) {
        toggleFaq.addEventListener('change', () => {
            faqContainer.style.display = toggleFaq.checked ? 'block' : 'none';
        });
    }

    if (btnAddFaq && faqList) {
        btnAddFaq.addEventListener('click', () => {
            const nextIndex = faqList.querySelectorAll('.repeater-item-card').length;
            const newCard = document.createElement('div');
            newCard.className = 'repeater-item-card';
            newCard.setAttribute('data-index', nextIndex);
            newCard.innerHTML = `
                <div class="repeater-item-header">
                    <span class="repeater-item-num">Question #${nextIndex + 1}</span>
                    <button type="button" class="btn-danger-icon btn-remove-repeater" title="Delete FAQ">✕</button>
                </div>
                <div class="form-group" style="margin-bottom: 0.75rem;">
                    <label>Question</label>
                    <input type="text" name="faqs[${nextIndex}][question]" class="form-control" placeholder="e.g. Do you provide custom cut sizes?">
                </div>
                <div class="form-group">
                    <label>Answer</label>
                    <textarea name="faqs[${nextIndex}][answer]" class="form-control" rows="2" placeholder="Provide answer details..."></textarea>
                </div>
            `;
            faqList.appendChild(newCard);
            attachRepeaterRemover(newCard);
        });
    }

    function attachRepeaterRemover(card) {
        const btn = card.querySelector('.btn-remove-repeater');
        if (btn) {
            btn.addEventListener('click', () => {
                card.remove();
            });
        }
    }

    document.querySelectorAll('.btn-remove-repeater').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const card = e.target.closest('.repeater-item-card');
            if (card) card.remove();
        });
    });
}



