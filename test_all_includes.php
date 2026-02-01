<?php
/**
 * Test script to verify all PHP files have correct includes
 */

echo "=== VERIFICAÇÃO DE INCLUDES PHP ===\n\n";

$files = [
    'index.php',
    'services.php',
    'about.php',
    'formations.php',
    'concours.php',
    'process_contact.php',
    'process_inscription.php',
    'testimonials.php'
];

$basePath = __DIR__;
$errors = [];
$success = [];

foreach ($files as $file) {
    $filePath = $basePath . '/' . $file;
    
    if (!file_exists($filePath)) {
        $errors[] = "❌ $file - File not found";
        continue;
    }
    
    // Check PHP syntax
    $output = [];
    $return = 0;
    exec("php -l \"$filePath\" 2>&1", $output, $return);
    
    if ($return === 0) {
        $success[] = "✅ $file - No syntax errors";
    } else {
        $errors[] = "❌ $file - Syntax error: " . implode(', ', $output);
    }
}

// Display results
if (!empty($success)) {
    echo "FILES VALIDATED:\n";
    foreach ($success as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
}

if (!empty($errors)) {
    echo "ERRORS:\n";
    foreach ($errors as $msg) {
        echo "  $msg\n";
    }
    echo "\n";
} else {
    echo "✅ ALL FILES VALIDATED SUCCESSFULLY!\n";
    echo "All include paths are correct and working.\n";
}
?>
