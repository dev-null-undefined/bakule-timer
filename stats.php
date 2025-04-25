<?php
header('Content-Type: application/json');

$cacheDir = '/var/cache/bakule-timer';
if (!is_dir($cacheDir)) {
    $cacheDir = sys_get_temp_dir() . '/bakule-timer';
    if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0777, true);
    }
}

$pdfUrl = 'https://bc.zde.uzdil.cz/main.pdf';
$pdfCache = $cacheDir . '/main.pdf';
$cacheJson = $cacheDir . '/stats_cache.json';
$useStale = isset($_GET['use_stale']) && $_GET['use_stale'] == '1';
$cacheMaxAge = $useStale ? PHP_INT_MAX : 60;

if (file_exists($cacheJson) && time() - filemtime($cacheJson) < $cacheMaxAge) {
    echo file_get_contents($cacheJson);
    exit;
}

if (!file_exists($pdfCache) || time() - filemtime($pdfCache) > $cacheMaxAge) {
    @file_put_contents($pdfCache, @fopen($pdfUrl, 'r'));
}

$tmpTxt = sys_get_temp_dir() . '/' . uniqid('pdf_') . '.txt';
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

