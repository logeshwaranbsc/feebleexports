# Feeble Exports - Hostinger Deployment & Server Configuration Guide

This document provides step-by-step instructions for deploying the **Feeble Exports** MVC PHP web application to **Hostinger Shared Hosting** (or cPanel/VPS environments) while isolating private source code outside the web root directory for optimal security.

---

## 1. Directory & Deployment Architecture

To ensure strict web security, sensitive code (`app/src/`), database/JSON storage (`app/storage/`), and environment secrets (`app/.env`) must be placed in a private application folder outside `public_html`. Only static assets and the entry script (`public/index.php`) should reside inside the public web root.

```text
/home/u123456789/                # Hostinger User Home Directory (Outside Web Root)
│
├── app/                         # Private Application Directory
│   ├── src/                     # Controllers, Core, Models, Services, Views
│   ├── storage/                 # Data JSON files (admin, blogs, messages, products, quotes, seo, uploads)
│   ├── .env                     # Production environment configuration (DB credentials & Supabase keys)
│   ├── .env.example             # Safe configuration template
│   └── README.md                # Private directory security reference
│
└── public_html/                 # PUBLIC WEB ROOT (Domain DocumentRoot points here)
    ├── index.php               # Front Controller entry point
    ├── .htaccess               # Apache URL Rewriting and Security Rules
    ├── .user.ini               # Custom PHP configuration (Upload limits & memory)
    ├── assets/
    │   └── images/             # Public website images and media
    ├── css/                    # Stylesheets (style.css, admin.css)
    ├── js/                     # Client-side JavaScript (app.js, admin.js)
    └── uploads/                # Public fallback media upload directory
```

---

## 2. Step-by-Step Hostinger Shared Hosting Deployment

### Step 1: Upload Application Directories to Hostinger
1. Log in to your Hostinger hPanel and navigate to **File Manager** (or connect via SFTP / FTP).
2. Create a new directory named `app` in your home folder (outside `public_html`):
   - Local directory: `feebleexports/app/`
   - Server target: `/home/USERNAME/app/` (or `/home/u123456789/app/`)
   - Upload all contents of `app/` (`src/`, `storage/`, `.env.example`, `README.md`) into this private folder.
3. Upload all contents of local `feebleexports/public/` directly into Hostinger's `public_html/`:
   - `public/index.php` -> `public_html/index.php`
   - `public/.htaccess` -> `public_html/.htaccess`
   - `public/.user.ini` -> `public_html/.user.ini`
   - `public/css/` -> `public_html/css/`
   - `public/js/` -> `public_html/js/`
   - `public/assets/` -> `public_html/assets/`
   - `public/uploads/` -> `public_html/uploads/`

> [!WARNING]
> Do NOT upload `.git/`, local `.env`, or `scratch/` to the public web server.

---

### Step 2: Configure Application Path in `public_html/index.php`

`public_html/index.php` automatically detects `app/` if it is located at `../app` relative to `public_html`.

If your Hostinger setup places `app` in a custom folder outside `public_html` (e.g. `/home/u123456789/feebleexports_app`), update line 8 of `public_html/index.php` or set the `APP_PATH` environment variable:

```php
// Option A: Set via environment variable in .htaccess or server config:
// SetEnv APP_PATH /home/u123456789/app

// Option B: Hardcode server path if needed in index.php:
define('BASE_PATH', '/home/u123456789/app');
```

---

### Step 3: Create Production Environment Configuration (`app/.env`)

1. In Hostinger File Manager, navigate to the private `app/` directory (outside `public_html`).
2. Create a new file named `.env` (or copy `.env.example` to `.env`).
3. Fill in your production credentials:

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_PATH=/home/u123456789/app

# Database Connection (MySQL / PostgreSQL / Supabase)
DB_HOST=localhost
DB_PORT=3306
DB_NAME=u123456789_feeble
DB_USER=u123456789_feebleuser
DB_PASS=YOUR_SECURE_PASSWORD

# Supabase S3 Storage Credentials (Optional - if using Supabase S3 bucket)
SUPABASE_URL=https://YOUR_SUPABASE_PROJECT_REF.supabase.co
SUPABASE_S3_ENDPOINT=https://YOUR_SUPABASE_PROJECT_REF.supabase.co/storage/v1/s3
SUPABASE_S3_REGION=us-east-1
SUPABASE_S3_ACCESS_KEY_ID=YOUR_SUPABASE_S3_ACCESS_KEY_ID
SUPABASE_S3_SECRET_ACCESS_KEY=YOUR_SUPABASE_S3_SECRET_ACCESS_KEY
SUPABASE_S3_BUCKET=feebleexports
SUPABASE_SERVICE_ROLE_KEY=YOUR_SUPABASE_SERVICE_ROLE_KEY
```

> [!IMPORTANT]
> Keep `app/.env` private and never expose it inside `public_html`.

---

### Step 4: Database Setup (If Using MySQL / PostgreSQL)

1. In Hostinger hPanel, go to **Databases** -> **MySQL Databases**.
2. Create a new database name, database user, and secure password.
3. Open **phpMyAdmin**, select your new database, and click **Import**.
4. Upload and execute `schema.sql` to initialize tables (`admin_users`, `products`, `blogs`, `quotes`, `messages`, `media`, `seo`).
5. Update `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` in `app/.env`.

---

### Step 5: File Permissions Configuration

Ensure proper write permissions for PHP to read/write JSON storage and handle uploads:

- Set `app/storage/` directory and files to `755` (or `775` if required by host).
- Set `public_html/uploads/` directory to `755` (or `775`).

```bash
chmod -R 755 /home/u123456789/app/storage
chmod -R 755 /home/u123456789/public_html/uploads
```

> [!CAUTION]
> Never use `777` permissions on a live production web server.

---

### Step 6: Enable Free SSL Certificate

1. In Hostinger hPanel, go to **Security** -> **SSL**.
2. Click **Install SSL** for your domain.
3. Verify HTTPS redirect in `public_html/.htaccess` if needed:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

---

## 3. Verification & Testing Checklist

After completing deployment, perform the following verification checks:

1. **Home Page & Public Routes**:
   - Access `https://yourdomain.com/` and confirm hero banner, products, and footer load correctly.
   - Verify pages: `/discover`, `/products`, `/our-story`, `/contact`.
2. **Static Asset Resolution**:
   - Inspect browser console (F12) to ensure CSS (`style.css`), JavaScript (`app.js`), and images (`/assets/images/...`) load cleanly (HTTP 200).
3. **Form Submissions**:
   - Submit a test quote request on `/contact` or product quote modal. Confirm success notice appears and data persists in `app/storage/quotes.json` (or database).
4. **Admin Login & Dashboard**:
   - Access `https://yourdomain.com/admin/login`.
   - Log in with credentials (`admin@feebleexports.com` / `admin123` or your updated password).
   - Test admin modules: Enquiries, Products, Blogs, SEO Settings, Profile, Media Uploads.
5. **Media Upload Persistence**:
   - Upload a test product image in Admin -> Media Uploads.
   - Verify image streams to Supabase S3 or saves into `public_html/uploads/` with a valid preview URL.

---

## 4. Troubleshooting Guide

### HTTP 500 Internal Server Error
- **Cause**: Syntax error, missing `.env` file, incorrect `BASE_PATH`, or invalid `.htaccess` directive.
- **Fix**: Check Hostinger **File Manager -> logs -> error_log** or PHP error logs. Verify `public_html/index.php` path resolution.

### HTTP 404 Page Not Found on Subpages
- **Cause**: Apache `mod_rewrite` is disabled or `public_html/.htaccess` is missing.
- **Fix**: Ensure `public_html/.htaccess` exists with the rewrite rules active.

### Storage Read/Write Errors
- **Cause**: Incorrect folder permissions on `app/storage/`.
- **Fix**: Run `chmod -R 755 app/storage` in SSH or use Hostinger File Manager to grant write permission to PHP.

### Supabase / S3 Upload Failures
- **Cause**: Incorrect S3 endpoint, invalid access keys, or missing CORS / bucket configuration in Supabase Dashboard.
- **Fix**: Verify `SUPABASE_S3_ENDPOINT`, `SUPABASE_S3_ACCESS_KEY_ID`, and `SUPABASE_S3_SECRET_ACCESS_KEY` in `app/.env`. Check local fallback saves in `public_html/uploads/`.
