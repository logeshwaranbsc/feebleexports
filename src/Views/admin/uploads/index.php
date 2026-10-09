<div class="page-header">
    <div class="page-title-group">
        <h1>☁️ Supabase S3 Media & Uploads</h1>
        <p>Upload product images, catalog PDFs, and marketing assets directly to your Supabase S3 bucket.</p>
    </div>
</div>

<!-- Supabase S3 Connection Status Card -->
<div class="card-panel" style="margin-bottom: 2rem; padding: 1.5rem; background: linear-gradient(135deg, rgba(30,41,59,0.9), rgba(15,23,42,0.95)); border: 1px solid var(--admin-border);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem;">
                <h3 style="font-size: 1.15rem; margin: 0; color: var(--admin-text-main);">Supabase S3 Storage Status</h3>
                <?php if ($status['configured']): ?>
                    <span class="badge" style="background: rgba(16,185,129,0.2); color: var(--admin-primary); border: 1px solid var(--admin-primary); font-size: 0.8rem; padding: 0.25rem 0.6rem;">🟢 S3 Active</span>
                <?php else: ?>
                    <span class="badge" style="background: rgba(245,158,11,0.2); color: var(--admin-warning); border: 1px solid var(--admin-warning); font-size: 0.8rem; padding: 0.25rem 0.6rem;">🟡 Keys Pending in .env</span>
                <?php endif; ?>
            </div>
            <p style="font-size: 0.85rem; color: var(--admin-text-muted); margin: 0;">
                Bucket: <strong style="color: var(--admin-primary);"><?= htmlspecialchars($status['bucket']) ?></strong> | Region: <strong><?= htmlspecialchars($status['region']) ?></strong> | Endpoint: <code style="background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 4px; font-size: 0.8rem;"><?= htmlspecialchars($status['endpoint']) ?></code>
            </p>
        </div>
        <button type="button" onclick="document.getElementById('envInstructionsModal').style.display='block'" class="btn-secondary btn-sm" style="display: flex; align-items: center; gap: 0.35rem;">
            ⚙️ View S3 .env Variables
        </button>
    </div>

    <?php if (!$status['configured']): ?>
        <div style="margin-top: 1rem; padding: 0.85rem 1rem; background: rgba(245,158,11,0.1); border-left: 4px solid var(--admin-warning); border-radius: 4px; font-size: 0.85rem; color: #fde68a;">
            <strong>💡 Quick Tip:</strong> S3 Access Keys are ready in your <code>.env</code> file. Simply open <code>.env</code> in your editor and paste your <code>SUPABASE_S3_ACCESS_KEY_ID</code> & <code>SUPABASE_S3_SECRET_ACCESS_KEY</code> from your Supabase Dashboard (<em>Storage &rarr; Settings &rarr; S3 Access Keys</em>). Uploads will fall back to local server storage until keys are added.
        </div>
    <?php endif; ?>
</div>

<!-- Upload Area Section -->
<div class="card-panel" style="margin-bottom: 2rem; padding: 2rem;">
    <h3 style="font-size: 1.1rem; margin-bottom: 1rem; color: var(--admin-primary); display: flex; align-items: center; gap: 0.5rem;">
        <span>📤</span> Upload Files to Supabase S3
    </h3>

    <form action="/admin/uploads" method="POST" enctype="multipart/form-data" id="mainUploadForm" class="admin-form">
        <div class="drag-drop-zone" id="dropZone" style="border: 2px dashed var(--admin-border); border-radius: 12px; padding: 2.5rem 1.5rem; text-align: center; background: rgba(15, 23, 42, 0.4); cursor: pointer; transition: all 0.2s ease;">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">☁️</div>
            <h4 style="font-size: 1.05rem; margin-bottom: 0.5rem; color: var(--admin-text-main);">Drag & Drop your file here, or <span style="color: var(--admin-primary); text-decoration: underline;">Browse File</span></h4>
            <p style="font-size: 0.85rem; color: var(--admin-text-muted); margin-bottom: 1rem;">Supports PNG, JPG, WEBP, SVG, PDF, document files (Max 25MB)</p>
            <input type="file" name="file" id="fileInputSelect" style="display: none;" onchange="handleFileSelected(this)">
            <button type="button" class="btn-primary" onclick="document.getElementById('fileInputSelect').click()">Choose File</button>
        </div>

        <div id="selectedFileInfo" style="display: none; margin-top: 1rem; padding: 1rem; background: var(--admin-bg); border-radius: 8px; border: 1px solid var(--admin-border); align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.5rem;">📄</span>
                <div>
                    <div id="selectedFileName" style="font-weight: 600; font-size: 0.95rem; color: var(--admin-text-main);">filename.png</div>
                    <div id="selectedFileSize" style="font-size: 0.8rem; color: var(--admin-text-muted);">0 KB</div>
                </div>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn-primary">Start Upload</button>
                <button type="button" class="btn-secondary" onclick="resetFileSelection()">Cancel</button>
            </div>
        </div>
    </form>
</div>

<!-- Uploaded Media Gallery Table -->
<div class="card-panel">
    <div class="panel-header">
        <div class="panel-title">
            <span>🖼️</span>
            <span>Uploaded Assets & Media Library (<?= count($mediaList) ?>)</span>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>File Name</th>
                    <th>Size</th>
                    <th>Storage</th>
                    <th>Public URL</th>
                    <th>Uploaded At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mediaList)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--admin-text-muted); padding: 2.5rem;">
                            No files uploaded yet. Select a file above to test uploading to Supabase S3!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($mediaList as $item): ?>
                        <?php 
                            $isImg = preg_match('/\.(jpg|jpeg|png|gif|webp|svg)$/i', $item['url'] ?? '');
                            $sizeKb = round(($item['size'] ?? 0) / 1024, 1);
                            $formattedSize = $sizeKb > 1024 ? round($sizeKb / 1024, 2) . ' MB' : $sizeKb . ' KB';
                        ?>
                        <tr>
                            <td>
                                <?php if ($isImg): ?>
                                    <img src="<?= htmlspecialchars($item['url']) ?>" alt="Preview" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid var(--admin-border); background: #000;">
                                <?php else: ?>
                                    <div style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; background: var(--admin-bg); border-radius: 6px; border: 1px solid var(--admin-border); font-size: 1.25rem;">📄</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($item['name'] ?? $item['filename'] ?? 'file') ?></strong>
                                <div style="font-size: 0.75rem; color: var(--admin-text-muted);"><?= htmlspecialchars($item['mime_type'] ?? '') ?></div>
                            </td>
                            <td><?= $formattedSize ?></td>
                            <td>
                                <span class="badge" style="background: rgba(16,185,129,0.15); color: var(--admin-primary); font-size: 0.75rem;">
                                    <?= htmlspecialchars($item['storage'] ?? 'S3') ?>
                                </span>
                            </td>
                            <td style="max-width: 250px;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="text" readonly value="<?= htmlspecialchars($item['url']) ?>" class="form-control" style="font-size: 0.75rem; padding: 0.25rem 0.5rem; height: 30px;" id="url_<?= htmlspecialchars($item['id']) ?>">
                                    <button type="button" class="btn-secondary btn-sm" onclick="copyUrlToClipboard('url_<?= htmlspecialchars($item['id']) ?>', this)" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Copy</button>
                                </div>
                            </td>
                            <td><?= date('M d, Y H:i', strtotime($item['created_at'] ?? 'now')) ?></td>
                            <td>
                                <form action="/admin/uploads/delete" method="POST" class="form-delete-confirm" data-item-name="file" style="display: inline;">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>">
                                    <button type="submit" class="btn-danger btn-sm" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- S3 .env Variables Instructions Modal -->
<div id="envInstructionsModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: var(--admin-card-bg); border: 1px solid var(--admin-border); border-radius: 12px; max-width: 650px; width: 100%; padding: 2rem; position: relative; margin: 5vh auto; max-height: 85vh; overflow-y: auto;">
        <button type="button" onclick="document.getElementById('envInstructionsModal').style.display='none'" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; color: var(--admin-text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
        
        <h3 style="font-size: 1.2rem; color: var(--admin-primary); margin-bottom: 0.75rem;">🔑 Supabase S3 Environment Variables</h3>
        <p style="font-size: 0.85rem; color: var(--admin-text-muted); margin-bottom: 1.25rem;">
            Below are the variables added to your <code>.env</code> file. Open <code>.env</code> in your root project directory and replace the placeholders with your Supabase S3 keys:
        </p>

        <pre style="background: #0f172a; padding: 1.25rem; border-radius: 8px; border: 1px solid var(--admin-border); color: #38bdf8; font-size: 0.82rem; overflow-x: auto; font-family: monospace; margin-bottom: 1.25rem;">
SUPABASE_URL=https://cmspxtxukztkxyjinatz.supabase.co
SUPABASE_S3_ENDPOINT=https://cmspxtxukztkxyjinatz.supabase.co/storage/v1/s3
SUPABASE_S3_REGION=us-east-1
SUPABASE_S3_ACCESS_KEY_ID=YOUR_SUPABASE_S3_ACCESS_KEY_ID
SUPABASE_S3_SECRET_ACCESS_KEY=YOUR_SUPABASE_S3_SECRET_ACCESS_KEY
SUPABASE_S3_BUCKET=feebleexports
SUPABASE_SERVICE_ROLE_KEY=YOUR_SUPABASE_SERVICE_ROLE_KEY
</pre>

        <h4 style="font-size: 0.95rem; color: var(--admin-text-main); margin-bottom: 0.5rem;">How to get your S3 keys in Supabase:</h4>
        <ol style="font-size: 0.85rem; color: var(--admin-text-muted); margin-left: 1.25rem; display: flex; flex-direction: column; gap: 0.4rem;">
            <li>Log into your <strong>Supabase Dashboard</strong>.</li>
            <li>Go to <strong>Storage</strong> &rarr; <strong>Settings</strong> &rarr; <strong>S3 Access Keys</strong>.</li>
            <li>Click <strong>Generate New S3 Access Key</strong>.</li>
            <li>Copy the <strong>Access Key ID</strong> and <strong>Secret Access Key</strong> into <code>.env</code>.</li>
            <li>Ensure a bucket named <code>feebleexports</code> (or your custom bucket name) exists in Supabase Storage and is set to <strong>Public</strong>.</li>
        </ol>

        <div style="text-align: right; margin-top: 1.5rem;">
            <button type="button" class="btn-primary btn-sm" onclick="document.getElementById('envInstructionsModal').style.display='none'">Got it</button>
        </div>
    </div>
</div>

<script>
function handleFileSelected(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        document.getElementById('selectedFileName').innerText = file.name;
        const kb = (file.size / 1024).toFixed(1);
        document.getElementById('selectedFileSize').innerText = kb > 1024 ? (kb / 1024).toFixed(2) + ' MB' : kb + ' KB';
        document.getElementById('selectedFileInfo').style.display = 'flex';
        document.getElementById('dropZone').style.display = 'none';
    }
}

function resetFileSelection() {
    document.getElementById('fileInputSelect').value = '';
    document.getElementById('selectedFileInfo').style.display = 'none';
    document.getElementById('dropZone').style.display = 'block';
}

function copyUrlToClipboard(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input) {
        input.select();
        navigator.clipboard.writeText(input.value).then(() => {
            const origText = btn.innerText;
            btn.innerText = 'Copied!';
            btn.style.background = 'var(--admin-primary)';
            btn.style.color = '#fff';
            setTimeout(() => {
                btn.innerText = origText;
                btn.style.background = '';
                btn.style.color = '';
            }, 2000);
        });
    }
}

// Setup Drag & Drop
const dropZone = document.getElementById('dropZone');
if (dropZone) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
        }, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.style.borderColor = 'var(--admin-primary)';
            dropZone.style.background = 'rgba(16, 185, 129, 0.08)';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.style.borderColor = 'var(--admin-border)';
            dropZone.style.background = 'rgba(15, 23, 42, 0.4)';
        }, false);
    });

    dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            document.getElementById('fileInputSelect').files = files;
            handleFileSelected(document.getElementById('fileInputSelect'));
        }
    }, false);
}
</script>
