-- ============================================================
-- FEEBLE EXPORTS - SUPABASE DATABASE SCHEMA & INITIAL SEED DATA
-- Company: FEEBLE EXPORTS (Namakkal, Tamil Nadu, India)
-- Run this SQL directly in your Supabase SQL Editor.
-- ============================================================

-- 1. ADMIN USERS TABLE
CREATE TABLE IF NOT EXISTS admin_users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'Proprietress',
    location VARCHAR(150) DEFAULT 'Namakkal, Tamil Nadu',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Seed Default Admin User (Password: admin123)
INSERT INTO admin_users (username, name, email, password, role, location)
VALUES (
    'admin',
    'Kavimayil Venkatachalam',
    'admin@feebleexports.com',
    '$2y$12$HnIFTm6QSP8Y3tfV7AvekuuAUmOlKVDh7ILHpSYkEO/JvcCsO5chO',
    'Proprietress',
    'Namakkal, Tamil Nadu'
)
ON CONFLICT (username) DO UPDATE 
SET name = EXCLUDED.name, email = EXCLUDED.email;


-- 2. PRODUCTS TABLE
CREATE TABLE IF NOT EXISTS products (
    id VARCHAR(100) PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(100) NOT NULL,
    category_slug VARCHAR(100) NOT NULL,
    image VARCHAR(255),
    description TEXT,
    price NUMERIC(10,2) DEFAULT NULL,
    offer_price NUMERIC(10,2) DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE products ADD COLUMN IF NOT EXISTS price NUMERIC(10,2) DEFAULT NULL;
ALTER TABLE products ADD COLUMN IF NOT EXISTS offer_price NUMERIC(10,2) DEFAULT NULL;

-- Seed Products Catalog
INSERT INTO products (id, name, category, category_slug, image, description, price, offer_price, status)
VALUES 
('plain-handloom', 'Plain and Handloom', 'Plain & Handloom', 'plain-handloom', '/assets/images/01_plain_handloom.png', 'Classic hand-woven natural coir mat made from high quality 100% natural coconut fibers.', 250.00, 199.00, 'active'),
('tufted', 'Tufted', 'Tufted', 'tufted', '/assets/images/02_tufted_pattern.png', 'Durable cut-pile tufted coir bristles designed for heavy scraping and soil absorption.', 350.00, 299.00, 'active'),
('creel-rod', 'Creel and Rod', 'Creel & Rod', 'creel-rod', '/assets/images/03_creel_and_rod.png', 'Heavy-duty steel or wood rod reinforced coir matting built for extreme foot traffic.', 450.00, 399.00, 'active'),
('rope-braided', 'Rope and Braided', 'Rope & Braided', 'rope-braided', '/assets/images/04_rope_braided_round.png', 'Intricately hand-braided natural coir rope mats featuring rich artisanal patterns.', 300.00, NULL, 'active'),
('pvc-backed', 'PVC - Backed', 'Backed Mats', 'backed-mats', '/assets/images/05_pvc_backed.png', 'Non-slip PVC vinyl backing bonded with dense coir fibers for zero displacement.', 280.00, 220.00, 'active'),
('rubber-backed', 'Rubber-Backed', 'Backed Mats', 'backed-mats', '/assets/images/06_rubber_backed_stack.png', 'Heavy molded rubber frame and base for outdoors weather resistance.', 320.00, 260.00, 'active'),
('latex-backed', 'Latex-Backed', 'Backed Mats', 'backed-mats', '/assets/images/07_latex_backed.png', 'Eco-friendly natural latex spray backing for flexible anti-skid protection.', 290.00, NULL, 'active'),
('printed-logo', 'Printed and Logo', 'Printed & Logo', 'printed-logo', '/assets/images/08_printed_logo_border.png', 'Custom stencilled and screen-printed mats featuring welcome motifs or corporate logos.', 400.00, 349.00, 'active'),
('bleached-coloured', 'Bleached or Coloured', 'Printed & Logo', 'printed-logo', '/assets/images/09_bleached_coloured_stack.png', 'Sun-bleached blonde coir yarn or AZO-free dyed rich color coir mats.', 270.00, 219.00, 'active'),
('coir-carpet', 'Coir Carpet', 'Carpet & Rolls', 'carpet-rolls', '/assets/images/10_coir_carpet_roll.png', 'High-end coir runner mats and floor carpets for hallways and eco-interiors.', 850.00, 699.00, 'active'),
('entryways-rope-knot', 'Entryways Coir Rope Knot Doormat', 'Rope & Braided', 'rope-braided', '/assets/images/11_entryway_coir_rope.png', 'Thick braided coir rope woven into timeless knot designs for luxury entrances.', 500.00, 420.00, 'active'),
('coir-mats-rolls', 'Coir Mat Rolls', 'Carpet & Rolls', 'carpet-rolls', '/assets/images/12_coir_mat_rolls.png', 'Master roll stock coir matting available for bulk commercial custom cutting.', 1200.00, NULL, 'active'),
('colours-and-designs', 'Colours and Designs', 'Printed & Logo', 'printed-logo', '/assets/images/13_colours_and_designs.png', 'Vibrant geometric, striped, and multi-color coir fiber combinations.', 380.00, 319.00, 'active')
ON CONFLICT (id) DO NOTHING;


-- 3. BLOGS TABLE
CREATE TABLE IF NOT EXISTS blogs (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    category VARCHAR(100) DEFAULT 'Coir Industry',
    author VARCHAR(100) DEFAULT 'Kavimayil Venkatachalam',
    image VARCHAR(255),
    status VARCHAR(20) DEFAULT 'published',
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Seed Blog Posts
INSERT INTO blogs (id, title, slug, excerpt, content, category, author, image, status, meta_title, meta_description)
VALUES
('BLOG-1', 'Global Eco-Friendly Matting Trends 2026', 'global-eco-friendly-matting-trends-2026', 'How natural coir fibre products are replacing synthetic rubber door mats in European and North American markets.', 'Natural coir fibre products harvested from coconut husks in Tamil Nadu are witnessing unprecedented global demand. Importers in North America and Europe are prioritizing biodegradable, plastic-free entrance matting solutions to fulfill strict environmental compliance standards.', 'Global Trade', 'Kavimayil Venkatachalam', '/assets/images/01_plain_handloom.png', 'published', 'Global Eco-Friendly Matting Trends 2026 | FEEBLE EXPORTS', 'Explore global market demand for eco-friendly coir products exported from Tamil Nadu, India.'),
('BLOG-2', 'The Craft of Handloom Coir Weaving in Namakkal', 'craft-of-handloom-coir-weaving-namakkal', 'Inside FEEBLE EXPORTS artisanal weaving process producing high durability coir products for international buyers.', 'Coir mat making in Namakkal blends generational artisanal skill with modern quality benchmarks. From raw golden fiber extraction to hand-cut finish, every mat offers supreme scraping power and longevity.', 'Craft & Manufacturing', 'Kavimayil Venkatachalam', '/assets/images/04_rope_braided_round.png', 'published', 'Handloom Coir Weaving in Namakkal | FEEBLE EXPORTS', 'Discover the craftsmanship and export quality standards of FEEBLE EXPORTS coir products.')
ON CONFLICT (id) DO NOTHING;


-- 4. CONTACT MESSAGES TABLE
CREATE TABLE IF NOT EXISTS contact_messages (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    country VARCHAR(100),
    message TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'new',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);


-- 5. QUOTE REQUESTS TABLE
CREATE TABLE IF NOT EXISTS quote_requests (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    country VARCHAR(100),
    product VARCHAR(150) NOT NULL,
    quantity VARCHAR(50) DEFAULT '100',
    custom_size VARCHAR(100),
    notes TEXT,
    status VARCHAR(20) DEFAULT 'new',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);


-- 6. SEO SETTINGS TABLE
CREATE TABLE IF NOT EXISTS seo_settings (
    page_key VARCHAR(50) PRIMARY KEY,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    og_title VARCHAR(255),
    og_description TEXT,
    og_image VARCHAR(255),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Seed SEO Default Settings
INSERT INTO seo_settings (page_key, meta_title, meta_description, meta_keywords, og_title, og_description, og_image)
VALUES
('home', 'FEEBLE EXPORTS - Sustainable Coir Mats for a Greener Tomorrow', 'Leading manufacturer & exporter of natural coir mats, tufted mats, PVC backed mats, and coconut fibre products from Namakkal, Tamil Nadu, India.', 'coir exporter India, natural coir mats, Namakkal coir exporter, eco friendly doormats', 'FEEBLE EXPORTS - Sustainable Coir Exporter', 'Premium natural coir mats and eco-friendly coconut fibre products crafted in Tamil Nadu, India.', '/assets/images/01_plain_handloom.png'),
('discover', 'Discover Us - FEEBLE EXPORTS', 'Learn about FEEBLE EXPORTS, our sustainability commitment, coir processing, and global export destinations.', 'coir sustainability, global coir export, Namakkal export manufacturing', 'Discover FEEBLE EXPORTS', 'Our vision and sustainable coir production process in Namakkal, Tamil Nadu.', '/assets/images/04_rope_braided_round.png'),
('products', 'Coir Mats Collection - FEEBLE EXPORTS', 'Explore our complete product catalog: Plain Handloom, Tufted, Creel & Rod, PVC Backed, Rubber Backed, and Coir Carpets.', 'coir mat collection, tufted coir, pvc backed coir, coir carpet rolls', 'Coir Products Catalog | FEEBLE EXPORTS', 'Wide collection of sustainable natural coir door mats and floor coverings.', '/assets/images/02_tufted_pattern.png'),
('story', 'Our Story - A Simple Mat A Brighter Tomorrow | FEEBLE EXPORTS', 'Read the journey of FEEBLE EXPORTS under the leadership of Kavimayil Venkatachalam in Namakkal, Tamil Nadu.', 'FEEBLE EXPORTS story, Kavimayil Venkatachalam, Namakkal coir history', 'Our Story - FEEBLE EXPORTS', 'From traditional handloom weaving to global export standards.', '/assets/images/03_creel_and_rod.png'),
('contact', 'Contact Us & Get a Quote - FEEBLE EXPORTS', 'Reach out to FEEBLE EXPORTS in Namakkal, Tamil Nadu for export inquiries, custom size orders, and wholesale quotes.', 'contact FEEBLE EXPORTS, coir quote request, coir exporter contact Namakkal', 'Contact FEEBLE EXPORTS', 'Get in touch with our export team for coir products quotes and inquiries.', '/assets/images/05_pvc_backed.png'),
('blogs', 'Blog & Export Insights - FEEBLE EXPORTS', 'Industry insights, eco-friendly matting trends, and coir manufacturing updates from FEEBLE EXPORTS.', 'coir blog, coir export trends, coconut fibre insights', 'FEEBLE EXPORTS Blog & News', 'Stay updated with eco-friendly product trends and export news.', '/assets/images/01_plain_handloom.png')
ON CONFLICT (page_key) DO NOTHING;


-- 7. UPLOADS TABLE
CREATE TABLE IF NOT EXISTS uploads (
    id VARCHAR(100) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    url TEXT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    path VARCHAR(255),
    size BIGINT DEFAULT 0,
    mime_type VARCHAR(100),
    storage VARCHAR(50) DEFAULT 'Supabase S3',
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

