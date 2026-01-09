<?php
$dir = __DIR__ . '/../aset/images/';
echo "Raw Dir: " . $dir . "\n";
echo "Real Path: " . realpath($dir) . "\n";

// Try to write a test file there
$test_file = rtrim($dir, '/\\') . DIRECTORY_SEPARATOR . 'test_write.txt';
if (file_put_contents($test_file, 'test')) {
    echo "Write Success to: $test_file\n";
    // Check where it is
    echo "File created at: " . realpath($test_file) . "\n";
    unlink($test_file);
} else {
    echo "Write Failed!\n";
}
?>