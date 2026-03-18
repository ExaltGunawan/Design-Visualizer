<?php
$images = [
    'bantal_tidur_base.png',
    'bantal_tidur_shadow.png',
    'sofa_base.png',
    'sofa_shadow.png',
    'lemari_base.png',
    'lemari_shadow.png',
    'patterns/batik_blue.jpg',
    'patterns/polka_red.jpg',
    'patterns/stripe_gray.jpg',
    'patterns/oak_wood.jpg',
    'patterns/marble.jpg'
];

$publicPath = __DIR__ . '/storage/app/public/';
@mkdir($publicPath . 'patterns', 0777, true);

foreach($images as $path) {
    $filepath = $publicPath . $path;
    if (!file_exists($filepath)) {
        $im = imagecreatetruecolor(400, 400);
        
        // Buat warna latar belakang (abu-abu terang)
        $bg = imagecolorallocate($im, 220, 220, 220);
        imagefill($im, 0, 0, $bg);
        
        // Tambahkan teks sebagai penanda (Placeholder)
        $textColor = imagecolorallocate($im, 50, 50, 50);
        imagestring($im, 5, 20, 190, basename($path), $textColor);
        
        if (strpos($path, '.png') !== false) {
            // Untuk file PNG, simpan tranparansi
            imagecolortransparent($im, $bg);
        }
        imagepng($im, $filepath);
        imagedestroy($im);
        echo "Created: $filepath\n";
    }
}
echo "Dummy images generated successfully!\n";
