<?php
$dosya = "iploglari.txt";

if (file_exists($dosya)) {
    if (unlink($dosya)) {
        echo "success"; // Dosya başarıyla silindi
    } else {
        echo "error"; // Dosya silinemedi
    }
} else {
    echo "not_found"; // Dosya bulunamadı
}
?>
