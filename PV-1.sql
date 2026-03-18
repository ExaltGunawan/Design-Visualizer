-- 1. Tabel untuk menyimpan kategori (misal: Ruang Tamu, Kamar Tidur)
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Tabel untuk menyimpan barang (misal: Bantal, Sofa, Lemari)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    -- File PNG bantal yang bagian tengahnya transparan
    base_image VARCHAR(255) NOT NULL, 
    -- File PNG transparan yang HANYA berisi bayangan/shadow
    shadow_overlay VARCHAR(255) NOT NULL, 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- 3. Tabel untuk menyimpan motif kain/corak
CREATE TABLE patterns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    -- File gambar kain (harus seamless)
    file_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Tabel untuk pilihan Grid/Skala yang tersedia
CREATE TABLE grid_presets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(50) NOT NULL, -- Misal: 'Kecil (4x4)', 'Besar (1x1)'
    scale_value DECIMAL(5,2) NOT NULL, -- Nilai untuk Fabric.js (misal: 0.25 atau 1.00)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Tabel untuk menyimpan hasil desain user (Opsional, jika ada fitur 'Simpan')
CREATE TABLE user_designs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    pattern_id INT,
    grid_preset_id INT,
    result_image VARCHAR(255), -- Path hasil export canvas
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (pattern_id) REFERENCES patterns(id),
    FOREIGN KEY (grid_preset_id) REFERENCES grid_presets(id)
);

-- DATA AWAL (CONTOH)
INSERT INTO categories (name) VALUES ('Soft Furnishing'), ('Storage');

INSERT INTO products (category_id, name, base_image, shadow_overlay) 
VALUES (1, 'Bantal Sofa', 'bantal_base.png', 'bantal_shadow.png');

INSERT INTO patterns (name, file_path) 
VALUES ('Batik Megamendung', 'batik_blue.jpg'), ('Polkadot Red', 'polka_red.jpg');

INSERT INTO grid_presets (label, scale_value) 
VALUES ('1x1 (Original)', 1.00), ('2x2 (Medium)', 0.50), ('4x4 (Small)', 0.25);