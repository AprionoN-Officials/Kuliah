<?php
$path = __DIR__ . '/aset/images/game_1767923795_7325.jpg';
echo "Checking path: $path\n";
if (file_exists($path)) {
    echo "File EXISTS!";
} else {
    echo "File MISSING!";
}
?>