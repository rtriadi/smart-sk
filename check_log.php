<?php
$file = 'debug_sk_save.txt';
if (!file_exists($file)) {
    echo "Log file not found.\n";
    exit;
}
$size = filesize($file);
echo "Log file size: " . number_format($size / 1024 / 1024, 2) . " MB\n";

// Read last 2KB
$offset = max(0, $size - 2048);
$content = file_get_contents($file, false, null, $offset);
echo "--- Last 2048 bytes ---\n";
echo $content;
echo "\n-----------------------\n";

echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
?>
