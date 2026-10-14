<?php
/**
 * Admin Create / Edit SEO Page View
 * Comprehensive multi-section SEO Landing Page Builder matching reference images 1, 2, and 3.
 */

$isEdit = $isEdit ?? false;
$pageId = $page['id'] ?? '';
$formAction = $isEdit ? "/admin/seo/{$pageId}/edit" : "/admin/seo/create";
$selectedProjects = $page['selected_projects'] ?? [];
$testimonials = $page['testimonials'] ?? [];
$faqs = $page['faqs'] ?? [];
?>

<form action="<?= $formAction ?>" method="POST" id="seoPageForm" class="seo-editor-form">

    <!-- Top Action Bar & Breadcrumb -->
    <div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <div>
            <div style="font-size: 0.85rem; color: var(--admin-text-muted); margin-bottom: 0.35rem;">
                <a href="/admin/seo" style="color: var(--admin-text-muted); text-decoration: none;">&lsaquo; SEO Pages</a>
                <span style="margin: 0 0.35rem;">/</span>
                <span style="color: var(--admin-primary);"><?= $isEdit ? 'Edit ' . htmlspecialchars($page['title']) : 'Create New' ?></span>
            </div>
            <h1 style="font-size: 1.75rem; font-weight: 700; color: var(--admin-text-main); margin-bottom: 0.25rem;">
                <?= $isEdit ? 'Edit SEO Page' : 'Create SEO Page' ?>
            </h1>
            <p style="color: var(--admin-text-muted); font-size: 0.92rem;">
                Add page details, SEO metadata, content sections and publish.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <?php if ($isEdit && !empty($page['id'])): ?>
                <a href="/admin/seo/<?= htmlspecialchars($page['id']) ?>/preview" target="_blank" class="btn-secondary" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.65rem 1.15rem; text-decoration: none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    <span>Preview</span>
                </a>
            <?php endif; ?>

            <button type="submit" name="submit_action" value="draft" class="btn-secondary" style="padding: 0.65rem 1.25rem; font-weight: 600;">
                Save as Draft
            </button>

            <button type="submit" name="submit_action" value="publish" class="btn-primary" style="padding: 0.65rem 1.5rem; font-weight: 600;">
                <?= $isEdit ? 'Update &amp; Publish' : 'Publish Page' ?>
            </button>
        </div>
    </div>

    <!-- SECTION 1: Page Details -->
    <div class="card-panel seo-section-card" id="section-page-details">
        <div class="seo-section-header">
            <span class="seo-step-badge">1</span>
            <h2 class="seo-section-title">Page Details</h2>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.25rem;">
            <div class="form-group">
                <label for="title">Page Title <span style="color: var(--admin-danger);">*</span></label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    class="form-control" 
                    value="<?= htmlspecialchars($page['title'] ?? '') ?>" 
                    placeholder="e.g. Best Interior Designer in Coimbatore"
                    required
                >
            </div>

            <div class="form-group">
                <label for="slug">URL Slug <span style="color: var(--admin-danger);">*</span></label>
                <div style="position: relative;">
                    <input 
                        type="text" 
                        id="slug" 
                        name="slug" 
                        class="form-control" 
                        value="<?= htmlspecialchars($page['slug'] ?? '') ?>" 
                        placeholder="e.g. best-interior-designer"
                        required
                        style="padding-right: 2.5rem;"
                    >
                    <span style="position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%); color: var(--admin-text-muted); font-size: 0.95rem;">🔗</span>
                </div>
                <small style="color: var(--admin-text-muted); font-size: 0.8rem; margin-top: 4px; display: block;">
                    Public URL: <span id="publicUrlPreview" style="color: #60a5fa; font-family: monospace;">/<?= htmlspecialchars($page['slug'] ?? '') ?></span>
                </small>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            <div class="form-group">
                <label for="status">Publication Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="published" <?= ($page['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    <option value="draft" <?= ($page['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                </select>
            </div>

            <div class="form-group">
                <label for="canonical_url">Canonical URL (Optional)</label>
                <input 
                    type="url" 
                    id="canonical_url" 
                    name="canonical_url" 
                    class="form-control" 
                    value="<?= htmlspecialchars($page['canonical_url'] ?? '') ?>" 
                    placeholder="https://example.com/best-interior-designer"
                >
            </div>
        </div>
    </div>

    <!-- SECTION 2: SEO Meta -->
    <div class="card-panel seo-section-card" id="section-seo-meta">
        <div class="seo-section-header">
            <span class="seo-step-badge">2</span>
            <h2 class="seo-section-title">SEO Meta</h2>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.25rem;">
            <div class="form-group">
                <label for="meta_title">Meta Title</label>
                <input 
                    type="text" 
                    id="meta_title" 
                    name="meta_title" 
                    class="form-control" 
                    value="<?= htmlspecialchars($page['meta_title'] ?? '') ?>" 
                    placeholder="e.g. Best Interior Designer in Coimbatore | Feeble Exports"
                >
                <div class="seo-counter-text" id="metaTitleCounter">
                    <span id="metaTitleCount"><?= mb_strlen($page['meta_title'] ?? '') ?></span> characters (Recommended: 50-60 characters)
                </div>
            </div>

            <div class="form-group">
                <label for="meta_keywords">Meta Keywords</label>
                <input 
                    type="text" 
                    id="meta_keywords" 
                    name="meta_keywords" 
                    class="form-control" 
                    value="<?= htmlspecialchars($page['meta_keywords'] ?? '') ?>" 
                    placeholder="interior designer, coir mats, coir export, Namakkal"
                >
                <small style="color: var(--admin-text-muted); font-size: 0.8rem; margin-top: 4px; display: block;">
                    Comma separated keywords
                </small>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="meta_description">Meta Description</label>
            <textarea 
                id="meta_description" 
                name="meta_description" 
                class="form-control" 
                rows="3"
                placeholder="Leading manufacturer & exporter of natural coir mats, tufted mats, PVC backed mats and eco-friendly coconut fibre products from Namakkal, Tamil Nadu."
            ><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
            <div class="seo-counter-text" id="metaDescCounter">
                <span id="metaDescCount"><?= mb_strlen($page['meta_description'] ?? '') ?></span> characters (Recommended: 120-160 characters)
            </div>
        </div>

        <div class="form-group">
            <label for="custom_head_html">Custom Head HTML</label>
            <textarea 
                id="custom_head_html" 
                name="custom_head_html" 
                class="form-control code-editor-font" 
                rows="2"
                placeholder='<meta name="robots" content="index, follow">'
            ><?= htmlspecialchars($page['custom_head_html'] ?? '') ?></textarea>
            <small style="color: var(--admin-text-muted); font-size: 0.8rem; margin-top: 4px; display: block;">
                Custom tracking codes, verification tags, or extra robots metadata injected into &lt;head&gt;.
            </small>
        </div>
    </div>

    <!-- SECTION 3: Hero Section -->
    <div class="card-panel seo-section-card" id="section-hero">
        <div class="seo-section-header">
            <span class="seo-step-badge">3</span>
            <h2 class="seo-section-title">Hero Section</h2>
        </div>

        <!-- Background Image Upload widget -->
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label>Background Image</label>
            <div class="seo-media-row">
                <div class="seo-media-preview-box">
                    <img 
                        src="<?= htmlspecialchars($page['hero_image'] ?: '/assets/images/01_plain_handloom.png') ?>" 
                        alt="Hero Preview" 
                        id="heroImagePreview"
                        class="seo-media-thumb"
                    >
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.5rem;">
                        <input 
                            type="text" 
                            name="hero_image" 
                            id="heroImageInput" 
                            class="form-control" 
                            value="<?= htmlspecialchars($page['hero_image'] ?? '') ?>" 
                            placeholder="/assets/images/01_plain_handloom.png"
                        >
                        <label class="btn-primary" style="cursor: pointer; padding: 0.65rem 1rem; white-space: nowrap; margin: 0; display: inline-flex; align-items: center; gap: 4px;">
                            <span>Upload Image</span>
                            <input 
                                type="file" 
                                class="admin-file-upload-input" 
                                data-target="#heroImageInput" 
                                data-preview="#heroImagePreview" 
                                accept="image/*" 
                                style="display: none;"
                            >
                        </label>
                        <button type="button" class="btn-danger-outline btn-media-remove" data-target="#heroImageInput" data-preview="#heroImagePreview">
                            Remove
                        </button>
                    </div>
                    <div class="upload-status-msg" style="display: none; font-size: 0.82rem; margin-top: 4px;"></div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.25rem;">
            <div class="form-group">
                <label for="hero_heading">Heading</label>
                <input 
                    type="text" 
                    id="hero_heading" 
                    name="hero_heading" 
                    class="form-control" 
                    value="<?= htmlspecialchars($page['hero_heading'] ?? '') ?>" 
                    placeholder="e.g. Natural Coir Mats for a Greener Tomorrow"
                >
            </div>

            <div class="form-group">
                <label for="hero_form_title">Form Title</label>
                <input 
                    type="text" 
                    id="hero_form_title" 
                    name="hero_form_title" 
                    class="form-control" 
                    value="<?= htmlspecialchars($page['hero_form_title'] ?? 'Get a Quote') ?>" 
                    placeholder="e.g. Get a Quote"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="hero_subheading">Subheading</label>
            <input 
                type="text" 
                id="hero_subheading" 
                name="hero_subheading" 
                class="form-control" 
                value="<?= htmlspecialchars($page['hero_subheading'] ?? '') ?>" 
                placeholder="e.g. Sustainable. Durable. Eco-Friendly."
            >
        </div>
    </div>

    <!-- SECTION 4: Why Choose Us -->
    <div class="card-panel seo-section-card" id="section-why-choose">
        <div class="seo-section-header">
            <span class="seo-step-badge">4</span>
            <h2 class="seo-section-title">Why Choose Us</h2>
        </div>

        <!-- Image Upload Widget -->
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label>Image</label>
            <div class="seo-media-row">
                <div class="seo-media-preview-box">
                    <img 
                        src="<?= htmlspecialchars($page['why_choose_image'] ?: '/assets/images/02_tufted_pattern.png') ?>" 
                        alt="Why Choose Preview" 
                        id="whyChoosePreview"
                        class="seo-media-thumb"
                    >
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.5rem;">
                        <input 
                            type="text" 
                            name="why_choose_image" 
                            id="whyChooseInput" 
                            class="form-control" 
                            value="<?= htmlspecialchars($page['why_choose_image'] ?? '') ?>" 
                            placeholder="/assets/images/02_tufted_pattern.png"
                        >
                        <label class="btn-primary" style="cursor: pointer; padding: 0.65rem 1rem; white-space: nowrap; margin: 0; display: inline-flex; align-items: center; gap: 4px;">
                            <span>Upload Image</span>
                            <input 
                                type="file" 
                                class="admin-file-upload-input" 
                                data-target="#whyChooseInput" 
                                data-preview="#whyChoosePreview" 
                                accept="image/*" 
                                style="display: none;"
                            >
                        </label>
                        <button type="button" class="btn-danger-outline btn-media-remove" data-target="#whyChooseInput" data-preview="#whyChoosePreview">
                            Remove
                        </button>
                    </div>
                    <div class="upload-status-msg" style="display: none; font-size: 0.82rem; margin-top: 4px;"></div>
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="why_choose_heading">Heading</label>
            <input 
                type="text" 
                id="why_choose_heading" 
                name="why_choose_heading" 
                class="form-control" 
                value="<?= htmlspecialchars($page['why_choose_heading'] ?? 'Why Choose Feeble Exports') ?>" 
                placeholder="e.g. Why Choose Feeble Exports"
            >
        </div>

        <div class="form-group">
            <label>Body Content</label>
            <div class="rich-text-wrapper" data-editor-id="why_choose_content">
                <div class="rich-text-toolbar">
                    <button type="button" class="rt-btn" data-command="formatBlock" data-value="p" title="Paragraph">P</button>
                    <button type="button" class="rt-btn" data-command="formatBlock" data-value="h3" title="Heading 3">H3</button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="bold" title="Bold"><b>B</b></button>
                    <button type="button" class="rt-btn" data-command="italic" title="Italic"><i>I</i></button>
                    <button type="button" class="rt-btn" data-command="underline" title="Underline"><u>U</u></button>
                    <button type="button" class="rt-btn" data-command="strikeThrough" title="Strike"><s>S</s></button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="insertUnorderedList" title="Bullet List">• List</button>
                    <button type="button" class="rt-btn" data-command="insertOrderedList" title="Numbered List">1. List</button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="createLink" title="Link">🔗 Link</button>
                    <button type="button" class="rt-btn" data-command="unlink" title="Unlink">Unlink</button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn rt-btn-source" title="Toggle Source View">&lt;/&gt; Source</button>
                </div>
                <div class="rich-text-canvas" contenteditable="true"><?= $page['why_choose_content'] ?? '' ?></div>
                <textarea name="why_choose_content" id="why_choose_content" class="rich-text-textarea" style="display: none;"><?= htmlspecialchars($page['why_choose_content'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- SECTION 5: About Us -->
    <div class="card-panel seo-section-card" id="section-about-us">
        <div class="seo-section-header">
            <span class="seo-step-badge">5</span>
            <h2 class="seo-section-title">About Us</h2>
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label>Image</label>
            <div class="seo-media-row">
                <div class="seo-media-preview-box">
                    <img 
                        src="<?= htmlspecialchars($page['about_image'] ?: '/assets/images/03_creel_and_rod.png') ?>" 
                        alt="About Us Preview" 
                        id="aboutImagePreview"
                        class="seo-media-thumb"
                    >
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; gap: 0.75rem; align-items: center; margin-bottom: 0.5rem;">
                        <input 
                            type="text" 
                            name="about_image" 
                            id="aboutImageInput" 
                            class="form-control" 
                            value="<?= htmlspecialchars($page['about_image'] ?? '') ?>" 
                            placeholder="/assets/images/03_creel_and_rod.png"
                        >
                        <label class="btn-primary" style="cursor: pointer; padding: 0.65rem 1rem; white-space: nowrap; margin: 0; display: inline-flex; align-items: center; gap: 4px;">
                            <span>Upload Image</span>
                            <input 
                                type="file" 
                                class="admin-file-upload-input" 
                                data-target="#aboutImageInput" 
                                data-preview="#aboutImagePreview" 
                                accept="image/*" 
                                style="display: none;"
                            >
                        </label>
                        <button type="button" class="btn-danger-outline btn-media-remove" data-target="#aboutImageInput" data-preview="#aboutImagePreview">
                            Remove
                        </button>
                    </div>
                    <div class="upload-status-msg" style="display: none; font-size: 0.82rem; margin-top: 4px;"></div>
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="about_heading">Heading</label>
            <input 
                type="text" 
                id="about_heading" 
                name="about_heading" 
                class="form-control" 
                value="<?= htmlspecialchars($page['about_heading'] ?? 'About Feeble Exports') ?>" 
                placeholder="e.g. About Feeble Exports"
            >
        </div>

        <div class="form-group">
            <label>Body Content</label>
            <div class="rich-text-wrapper" data-editor-id="about_content">
                <div class="rich-text-toolbar">
                    <button type="button" class="rt-btn" data-command="formatBlock" data-value="p" title="Paragraph">P</button>
                    <button type="button" class="rt-btn" data-command="formatBlock" data-value="h3" title="Heading 3">H3</button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="bold" title="Bold"><b>B</b></button>
                    <button type="button" class="rt-btn" data-command="italic" title="Italic"><i>I</i></button>
                    <button type="button" class="rt-btn" data-command="underline" title="Underline"><u>U</u></button>
                    <button type="button" class="rt-btn" data-command="strikeThrough" title="Strike"><s>S</s></button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="insertUnorderedList" title="Bullet List">• List</button>
                    <button type="button" class="rt-btn" data-command="insertOrderedList" title="Numbered List">1. List</button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="createLink" title="Link">🔗 Link</button>
                    <button type="button" class="rt-btn" data-command="unlink" title="Unlink">Unlink</button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn rt-btn-source" title="Toggle Source View">&lt;/&gt; Source</button>
                </div>
                <div class="rich-text-canvas" contenteditable="true"><?= $page['about_content'] ?? '' ?></div>
                <textarea name="about_content" id="about_content" class="rich-text-textarea" style="display: none;"><?= htmlspecialchars($page['about_content'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- SECTION 6: Content Block 1 -->
    <div class="card-panel seo-section-card" id="section-content-1">
        <div class="seo-section-header">
            <span class="seo-step-badge">6</span>
            <h2 class="seo-section-title">Content Block 1</h2>
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label for="content_block_1_heading">Heading</label>
            <input 
                type="text" 
                id="content_block_1_heading" 
                name="content_block_1_heading" 
                class="form-control" 
                value="<?= htmlspecialchars($page['content_block_1_heading'] ?? '') ?>" 
                placeholder="e.g. Handcrafted Excellence & Export Quality Standards"
            >
        </div>

        <div class="form-group">
            <label>Rich-text Content</label>
            <div class="rich-text-wrapper" data-editor-id="content_block_1_content">
                <div class="rich-text-toolbar">
                    <button type="button" class="rt-btn" data-command="formatBlock" data-value="p" title="Paragraph">P</button>
                    <button type="button" class="rt-btn" data-command="formatBlock" data-value="h3" title="Heading 3">H3</button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="bold" title="Bold"><b>B</b></button>
                    <button type="button" class="rt-btn" data-command="italic" title="Italic"><i>I</i></button>
                    <button type="button" class="rt-btn" data-command="underline" title="Underline"><u>U</u></button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="insertUnorderedList" title="Bullet List">• List</button>
                    <button type="button" class="rt-btn" data-command="insertOrderedList" title="Numbered List">1. List</button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="createLink" title="Link">🔗 Link</button>
                    <button type="button" class="rt-btn rt-btn-source" title="Toggle Source View">&lt;/&gt; Source</button>
                </div>
                <div class="rich-text-canvas" contenteditable="true"><?= $page['content_block_1_content'] ?? '' ?></div>
                <textarea name="content_block_1_content" id="content_block_1_content" class="rich-text-textarea" style="display: none;"><?= htmlspecialchars($page['content_block_1_content'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- SECTION 7: Projects -->
    <div class="card-panel seo-section-card" id="section-projects">
        <div class="seo-section-header" style="justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <span class="seo-step-badge">7</span>
                <h2 class="seo-section-title">Projects</h2>
            </div>
            <div class="projects-counter-badge" id="projectsCounterBadge">
                <span id="selectedProjectsCount"><?= count($selectedProjects) ?></span>/<?= count($products) ?> selected
            </div>
        </div>

        <p style="color: var(--admin-text-muted); font-size: 0.88rem; margin-bottom: 1.25rem;">
            Select which products &amp; project items to showcase on this landing page. Click cards to toggle selection.
        </p>

        <div class="seo-projects-picker-grid" id="projectsPickerGrid">
            <?php foreach ($products as $prod): ?>
                <?php $isSelected = in_array($prod['id'], $selectedProjects); ?>
                <div class="project-picker-card <?= $isSelected ? 'is-selected' : '' ?>" data-id="<?= htmlspecialchars($prod['id']) ?>">
                    <input 
                        type="checkbox" 
                        name="selected_projects[]" 
                        value="<?= htmlspecialchars($prod['id']) ?>" 
                        class="project-picker-checkbox"
                        <?= $isSelected ? 'checked' : '' ?>
                        style="display: none;"
                    >
                    <div class="project-picker-thumb-wrap">
                        <img 
                            src="<?= htmlspecialchars($prod['image'] ?: '/assets/images/01_plain_handloom.png') ?>" 
                            alt="<?= htmlspecialchars($prod['name']) ?>" 
                            class="project-picker-thumb"
                            onerror="this.onerror=null; this.src='/assets/images/01_plain_handloom.png';"
                        >
                        <span class="project-picker-check-badge">✓</span>
                    </div>
                    <div class="project-picker-info">
                        <span class="project-picker-category"><?= htmlspecialchars($prod['category'] ?? 'Coir') ?></span>
                        <h4 class="project-picker-title"><?= htmlspecialchars($prod['name']) ?></h4>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- SECTION 8: Content Block 2 -->
    <div class="card-panel seo-section-card" id="section-content-2">
        <div class="seo-section-header">
            <span class="seo-step-badge">8</span>
            <h2 class="seo-section-title">Content Block 2</h2>
        </div>

        <div class="form-group">
            <label>Rich-text Content</label>
            <div class="rich-text-wrapper" data-editor-id="content_block_2_content">
                <div class="rich-text-toolbar">
                    <button type="button" class="rt-btn" data-command="formatBlock" data-value="p" title="Paragraph">P</button>
                    <button type="button" class="rt-btn" data-command="formatBlock" data-value="h3" title="Heading 3">H3</button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="bold" title="Bold"><b>B</b></button>
                    <button type="button" class="rt-btn" data-command="italic" title="Italic"><i>I</i></button>
                    <button type="button" class="rt-btn" data-command="underline" title="Underline"><u>U</u></button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="insertUnorderedList" title="Bullet List">• List</button>
                    <button type="button" class="rt-btn" data-command="insertOrderedList" title="Numbered List">1. List</button>
                    <span class="rt-sep"></span>
                    <button type="button" class="rt-btn" data-command="createLink" title="Link">🔗 Link</button>
                    <button type="button" class="rt-btn rt-btn-source" title="Toggle Source View">&lt;/&gt; Source</button>
                </div>
                <div class="rich-text-canvas" contenteditable="true"><?= $page['content_block_2_content'] ?? '' ?></div>
                <textarea name="content_block_2_content" id="content_block_2_content" class="rich-text-textarea" style="display: none;"><?= htmlspecialchars($page['content_block_2_content'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- SECTION 9: Testimonials -->
    <div class="card-panel seo-section-card" id="section-testimonials">
        <div class="seo-section-header" style="justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <span class="seo-step-badge">9</span>
                <h2 class="seo-section-title">Testimonials</h2>
            </div>
            <label class="seo-toggle-label">
                <input 
                    type="checkbox" 
                    name="show_testimonials" 
                    value="1" 
                    id="toggleTestimonials"
                    <?= (!empty($page['show_testimonials'])) ? 'checked' : '' ?>
                >
                <span>Enable Testimonials Section</span>
            </label>
        </div>

        <div id="testimonialsContainer" style="<?= empty($page['show_testimonials']) ? 'display: none;' : '' ?>">
            <p style="color: var(--admin-text-muted); font-size: 0.88rem; margin-bottom: 1.25rem;">
                Add customer quotes, designations, and ratings for this page.
            </p>

            <div id="testimonialsList" class="seo-repeater-list">
                <?php foreach ($testimonials as $tIndex => $t): ?>
                    <div class="repeater-item-card" data-index="<?= $tIndex ?>">
                        <div class="repeater-item-header">
                            <span class="repeater-item-num">Testimonial #<?= $tIndex + 1 ?></span>
                            <button type="button" class="btn-danger-icon btn-remove-repeater" title="Delete Testimonial">✕</button>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 100px; gap: 1rem; margin-bottom: 0.75rem;">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" name="testimonials[<?= $tIndex ?>][name]" class="form-control" value="<?= htmlspecialchars($t['name'] ?? '') ?>" placeholder="e.g. Alexander Wright">
                            </div>
                            <div class="form-group">
                                <label>Designation / Company</label>
                                <input type="text" name="testimonials[<?= $tIndex ?>][role]" class="form-control" value="<?= htmlspecialchars($t['role'] ?? '') ?>" placeholder="e.g. Procurement Director, EcoLiving UK">
                            </div>
                            <div class="form-group">
                                <label>Rating</label>
                                <select name="testimonials[<?= $tIndex ?>][rating]" class="form-control">
                                    <option value="5" <?= ($t['rating'] ?? 5) == 5 ? 'selected' : '' ?>>5 ★</option>
                                    <option value="4" <?= ($t['rating'] ?? 5) == 4 ? 'selected' : '' ?>>4 ★</option>
                                    <option value="3" <?= ($t['rating'] ?? 5) == 3 ? 'selected' : '' ?>>3 ★</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Testimonial Text</label>
                            <textarea name="testimonials[<?= $tIndex ?>][text]" class="form-control" rows="2" placeholder="Write the client's review..."><?= htmlspecialchars($t['text'] ?? '') ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" id="btnAddTestimonial" class="btn-secondary btn-sm" style="margin-top: 1rem;">
                + Add Another Testimonial
            </button>
        </div>
    </div>

    <!-- SECTION 10: FAQ -->
    <div class="card-panel seo-section-card" id="section-faq">
        <div class="seo-section-header" style="justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.85rem;">
                <span class="seo-step-badge">10</span>
                <h2 class="seo-section-title">Frequently Asked Questions (FAQ)</h2>
            </div>
            <label class="seo-toggle-label">
                <input 
                    type="checkbox" 
                    name="show_faq" 
                    value="1" 
                    id="toggleFaq"
                    <?= (!empty($page['show_faq'])) ? 'checked' : '' ?>
                >
                <span>Enable FAQ Section</span>
            </label>
        </div>

        <div id="faqContainer" style="<?= empty($page['show_faq']) ? 'display: none;' : '' ?>">
            <p style="color: var(--admin-text-muted); font-size: 0.88rem; margin-bottom: 1.25rem;">
                Provide answers to common buyer questions regarding sizes, materials, MOQ, and international shipping.
            </p>

            <div id="faqList" class="seo-repeater-list">
                <?php foreach ($faqs as $fIndex => $f): ?>
                    <div class="repeater-item-card" data-index="<?= $fIndex ?>">
                        <div class="repeater-item-header">
                            <span class="repeater-item-num">Question #<?= $fIndex + 1 ?></span>
                            <button type="button" class="btn-danger-icon btn-remove-repeater" title="Delete FAQ">✕</button>
                        </div>
                        <div class="form-group" style="margin-bottom: 0.75rem;">
                            <label>Question</label>
                            <input type="text" name="faqs[<?= $fIndex ?>][question]" class="form-control" value="<?= htmlspecialchars($f['question'] ?? '') ?>" placeholder="e.g. What sizes are available for export coir mats?">
                        </div>
                        <div class="form-group">
                            <label>Answer</label>
                            <textarea name="faqs[<?= $fIndex ?>][answer]" class="form-control" rows="2" placeholder="Write a clear, helpful answer..."><?= htmlspecialchars($f['answer'] ?? '') ?></textarea>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" id="btnAddFaq" class="btn-secondary btn-sm" style="margin-top: 1rem;">
                + Add Another Question
            </button>
        </div>
    </div>

    <!-- Bottom Sticky Actions Bar -->
    <div class="seo-sticky-bottom-bar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="font-size: 0.9rem; color: var(--admin-text-muted);">
                Ready to save your custom SEO landing page?
            </span>
        </div>
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="/admin/seo" class="btn-secondary" style="text-decoration: none;">Cancel</a>
            <button type="submit" name="submit_action" value="draft" class="btn-secondary">Save as Draft</button>
            <button type="submit" name="submit_action" value="publish" class="btn-primary" style="padding: 0.75rem 2rem;">
                <?= $isEdit ? 'Update &amp; Publish' : 'Publish Page' ?>
            </button>
        </div>
    </div>

</form>
