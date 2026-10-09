# Private Application Directory (`app/`)

This directory contains the private core application source code, data models, services, and local JSON storage files for the **Feeble Exports** application.

> [!IMPORTANT]
> **SECURITY REQUIREMENT**: This entire `app/` directory MUST remain outside of the web server's public document root (e.g., `public_html`) in production. Web visitors should only have access to the `public/` directory.

---

## Directory Structure

- `src/`
  - `Controllers/`: HTTP request handlers (Public pages, Forms, and Admin management).
  - `Core/`: Framework foundations (Router, View renderer, Auth, Response, Database loader).
  - `Models/`: Data access models for Products, Blogs, Enquiries, Quotes, Media, and SEO.
  - `Services/`: Integration services such as Supabase S3 Storage.
  - `Views/`: Page templates, partials, and layout files.
- `storage/`: Server-writable JSON storage files (`admin.json`, `blogs.json`, `messages.json`, `products.json`, `quotes.json`, `seo.json`, `uploads.json`).
- `.env`: Production environment variables (Must be created directly on the server).
- `.env.example`: Template for environment setup.

---

## Server Permissions

The `storage/` directory requires write permissions for the PHP process (`chmod 755` or `chmod 775`). Never set `777` permissions.
