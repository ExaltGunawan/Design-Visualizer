-- 1. Tabel Kategori
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel Barang
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    base_image VARCHAR(255) NOT NULL,
    shadow_overlay VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- 3. Tabel Motif
CREATE TABLE patterns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Tabel Master Grid
CREATE TABLE grid_presets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL, -- 'Kecil (4x4)', 'Besar (1x1)', 'Custom 3x2'
    colss INT DEFAULT 1,
    rowss INT DEFAULT 1,
    scale_value DECIMAL(5,2) NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Tabel Penghubung (Kunci agar tiap barang punya pilihan grid beda-beda)
CREATE TABLE product_grid (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    grid_preset_id INT,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (grid_preset_id) REFERENCES grid_presets(id) ON DELETE CASCADE
);

-- DATA AWAL
-- 1. Insert Kategori
INSERT INTO categories (name) VALUES 
('Bedroom'), 
('Living Room'), 
('Storage');

-- 2. Insert Barang (Aset Dummy)
-- base_image = area transparan, shadow_overlay = layer bayangan
INSERT INTO products (category_id, name, base_image, shadow_overlay) VALUES 
(1, 'Bantal Tidur', 'bantal_tidur_base.png', 'bantal_tidur_shadow.png'),
(2, 'Sofa Minimalis', 'sofa_base.png', 'sofa_shadow.png'),
(3, 'Lemari Dua Pintu', 'lemari_base.png', 'lemari_shadow.png');

-- 3. Insert Motif Kain
INSERT INTO patterns (name, file_path) VALUES 
('Batik Megamendung', 'patterns/batik_blue.jpg'),
('Polkadot Retro', 'patterns/polka_red.jpg'),
('Minimalist Stripe', 'patterns/stripe_gray.jpg'),
('Wood Texture', 'patterns/oak_wood.jpg'),
('Marble White', 'patterns/marble.jpg');

-- 4. Insert Grid Presets (Master pilihan grid)
INSERT INTO grid_presets (label, colss, rowss, scale_value) VALUES 
('1x1 Full', 1, 1, 1.00),
('2x1 Horizontal', 2, 1, 0.50),
('2x2 Medium', 2, 2, 0.50),
('3x2 Grid', 3, 2, 0.33),
('4x4 Small', 4, 4, 0.25);

-- 5. Menghubungkan Grid ke Barang (Product_Grid)
-- Bantal Tidur (ID 1) hanya bisa: 1x1, 2x2, dan 4x4
INSERT INTO product_grid (product_id, grid_preset_id) VALUES (1, 1), (1, 3), (1, 5);

-- Sofa Minimalis (ID 2) hanya bisa: 1x1 dan 2x1
INSERT INTO product_grid (product_id, grid_preset_id) VALUES (2, 1), (2, 2);

-- Lemari Dua Pintu (ID 3) hanya bisa: 1x1 dan 3x2
INSERT INTO product_grid (product_id, grid_preset_id) VALUES (3, 1), (3, 4);