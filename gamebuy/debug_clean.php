<?php
function clean_int($val) {
    echo "Input: " . var_export($val, true) . "\n";
    $cleaned = preg_replace('/[^0-9]/', '', (string)$val);
    echo "Regex Result: " . $cleaned . "\n";
    $num = (int)$cleaned;
    echo "Final Int: " . $num . "\n";
    return max(0, $num);
}

echo "--- Test 1: Normal Integer ---\n";
clean_int("10000");

echo "\n--- Test 2: With Dot Thousands ---\n";
clean_int("10.000");

echo "\n--- Test 3: With Comma Decimals ---\n";
clean_int("10000,00");

echo "\n--- Test 4: With Dot Decimal (Standard Float) ---\n";
clean_int("10000.00");

echo "\n--- Test 5: Browser formatted (Indonesian) ---\n";
clean_int("10.000,00");
?>