<?php

$url = "https://bc.dev-null.me/stats.php";
$json = file_get_contents($url);
if ($json === false) {
    http_response_code(500);
    echo "# Error: failed to fetch data\n";
    exit;
}

$data = json_decode($json, true);
if ($data === null || !isset($data['words_written'])) {
    http_response_code(500);
    echo "# Error: invalid JSON or missing keys\n";
    exit;
}

header('Content-Type: text/plain; version=0.0.4');

echo "# HELP words_written Total number of words written\n";
echo "# TYPE words_written gauge\n";
echo 'words_written{author="martin.uzdil"} ' . $data['words_written'] . "\n";

