document.addEventListener('DOMContentLoaded', () => {
    initAdminSidebar();
    initAutoDismissAlerts();
    initDeleteConfirmations();
    initSlugGenerator();
    initS3UploadInputs();
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

// Inline Form S3 Upload handler
function initS3UploadInputs() {
    document.querySelectorAll('.admin-s3-upload-input').forEach(input => {
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
                statusMsg.innerText = '⏳ Uploading file to Supabase S3...';
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
                        statusMsg.innerText = '✓ Uploaded to ' + (result.storage || 'Supabase S3') + '!';
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

