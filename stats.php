<?php
header('Content-Type: application/json');

function hostToSlug(string $url): string
{
    $host = parse_url($url, PHP_URL_HOST);
    if (!$host) {
        return '';
    }
    return strtolower(str_replace('.', '-', $host));
}


$pdfUrl = '<PDF_URL>';

$urlSlug = hostToSlug($pdfUrl);

$cacheDir = '/var/cache/bakule-timer';
if (!is_dir($cacheDir)) {
    $cacheDir = sys_get_temp_dir() . '/bakule-timer';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }
}

$pdfCache = $cacheDir . '/' . $urlSlug . '.pdf';
$cacheJson = $cacheDir . '/' . $urlSlug . '-stats_cache.json';
$useStale = isset($_GET['use_stale']) && $_GET['use_stale'] == '1';
$cacheMaxAge = $useStale ? PHP_INT_MAX : 60;

if (file_exists($cacheJson) && time() - filemtime($cacheJson) < $cacheMaxAge) {
    echo file_get_contents($cacheJson);
    exit;
}

if (!file_exists($pdfCache) || time() - filemtime($pdfCache) > $cacheMaxAge) {
    @file_put_contents($pdfCache, @fopen($pdfUrl, 'r'));
}

$tmpTxt = sys_get_temp_dir() . '/' . uniqid($urlSlug . '-pdf_') . '.txt';
@exec(
    'pdftotext ' . escapeshellarg($pdfCache) . ' ' . escapeshellarg($tmpTxt) . ' 2>/dev/null',
    $out, $rc
);
$text = @file_get_contents($tmpTxt) ?: '';
@unlink($tmpTxt);

$wordsWritten = str_word_count($text);

$response = [
    'words_written' => $wordsWritten,
    'fetched_at' => date(DATE_ISO8601),
];

$encoded = json_encode($response);

@file_put_contents($cacheJson, $encoded);

echo $encoded;

