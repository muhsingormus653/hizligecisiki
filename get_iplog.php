<?php
// Dosya adını ve yolu belirtin
$dosya = "iploglari.txt";

// Fonksiyonla ziyaretçinin durumunu belirle
function getVisitorStatus($currentTimestamp, $lastVisitTimestamp) {
    $timeDifference = $currentTimestamp - $lastVisitTimestamp;
    if ($timeDifference <= 30000) { // 30 saniyeden kısa bir süre içinde ise "Online"
        return "Online";
    } else {
        return "Offline";
    }
}

if (file_exists($dosya)) {
    $satirlar = file($dosya, FILE_IGNORE_NEW_LINES);

    // Satırları ters sırada işle
    $satirlar = array_reverse($satirlar);

    foreach ($satirlar as $line) {
        // Satırı parçala
        list($line_number, $ip, $userAgent, $country, $current_date_time, $language, $bilgi) = explode(" - ", $line);

        // Şu anki zamanı al
        $currentTimestamp = time();

        // Ziyaretçinin son etkinlik zamanını al
        $lastVisitTimestamp = strtotime($current_date_time);

        // Ziyaretçinin durumunu hesapla
        $status = getVisitorStatus($currentTimestamp, $lastVisitTimestamp);

        // Tabloya ekle ve HTML olarak geri döndür
        echo "<tr>
                <td>$line_number</td>
                <td>$ip</td>
                <td>$userAgent</td>
                <td>$country</td>
                <td>$current_date_time</td>
                <td>$language</td>
                <td>$bilgi</td>
              </tr>";
    }
} else {
   
}

?>
