<?php
require_once __DIR__ . '/includes/captcha.php';

$code = generateCaptchaCode(5);

// Coba pakai GD
if (function_exists('imagecreatetruecolor')) {
    $w = 160;
    $h = 50;
    $img = imagecreatetruecolor($w, $h);

    $bg = imagecolorallocate($img, 30, 27, 75);      // primary dark
    $fg = imagecolorallocate($img, 255, 255, 255);
    $noise1 = imagecolorallocate($img, 124, 58, 237);
    $noise2 = imagecolorallocate($img, 59, 130, 246);
    $line = imagecolorallocate($img, 167, 139, 250);

    imagefilledrectangle($img, 0, 0, $w, $h, $bg);

    // Noise lines
    for ($i = 0; $i < 6; $i++) {
        imageline($img, random_int(0, $w), random_int(0, $h), random_int(0, $w), random_int(0, $h), $line);
    }
    // Noise dots
    for ($i = 0; $i < 80; $i++) {
        imagesetpixel($img, random_int(0, $w), random_int(0, $h), ($i % 2 ? $noise1 : $noise2));
    }

    // Draw characters with slight rotation simulation via position jitter
    $x = 18;
    for ($i = 0; $i < strlen($code); $i++) {
        $y = random_int(12, 22);
        imagestring($img, 5, $x, $y, $code[$i], $fg);
        $x += 26;
    }

    header('Content-Type: image/png');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Pragma: no-cache');
    imagepng($img);
    imagedestroy($img);
    exit;
}

// Fallback: SVG CAPTCHA (tanpa GD)
header('Content-Type: image/svg+xml');
header('Cache-Control: no-store, no-cache, must-revalidate');
$chars = str_split($code);
$svg = '<?xml version="1.0" encoding="UTF-8"?>';
$svg .= '<svg xmlns="http://www.w3.org/2000/svg" width="160" height="50" viewBox="0 0 160 50">';
$svg .= '<rect width="160" height="50" fill="#1e1b4b"/>';
$svg .= '<line x1="10" y1="10" x2="150" y2="40" stroke="#a78bfa" stroke-width="1" opacity="0.5"/>';
$svg .= '<line x1="20" y1="45" x2="140" y2="5" stroke="#7c3aed" stroke-width="1" opacity="0.4"/>';
$x = 20;
foreach ($chars as $ch) {
    $y = 28 + rand(-4, 4);
    $rot = rand(-12, 12);
    $svg .= '<text x="'.$x.'" y="'.$y.'" fill="#ffffff" font-family="monospace" font-size="22" font-weight="bold" transform="rotate('.$rot.' '.$x.','.$y.')">'.htmlspecialchars($ch).'</text>';
    $x += 26;
}
$svg .= '</svg>';
echo $svg;
exit;
