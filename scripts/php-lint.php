#!/usr/bin/env php
<?php
// scripts/php-lint.php
// Lint staged PHP files (preferred) or all PHP files if none staged.
// Returns 0 when OK, 1 when any syntax errors found.

function run($cmd, &$output = null, &$exit = null) {
    exec($cmd, $output, $exit);
    return [$output, $exit];
}

// Get list of staged files
$staged = [];
list($out, $exit) = run('git diff --cached --name-only --diff-filter=ACM');

// If the diff invocation fails on some Windows/git setups, fall back to parsing `git status --porcelain`
if ($exit === 0 && !empty($out)) {
    $staged = $out;
} else {
    $staged = [];
    list($statusOut, $statusExit) = run('git status --porcelain');
    if ($statusExit === 0 && !empty($statusOut)) {
        foreach ($statusOut as $line) {
            // porcelain format: XY <path>  or R  <from> -> <to>
            // We accept added/modified/copied/renamed files
            $trim = rtrim($line);
            if (strlen($trim) < 4) continue;
            $code = substr($trim, 0, 2);
            if (preg_match('/[ACMRUDT ]/', $code)) {
                // Extract file path
                $path = trim(substr($trim, 3));
                // Handle rename format: 'from -> to'
                if (strpos($path, '->') !== false) {
                    $parts = preg_split('/->/', $path);
                    $path = trim($parts[1]);
                }
                $staged[] = $path;
            }
        }
    }
}

$files = [];
if (!empty($staged)) {
    foreach ($staged as $f) {
        if (preg_match('/\.php$/i', $f) && file_exists($f)) {
            $files[] = $f;
        }
    }
} else {
    // Fallback: scan repository for php files
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/..'));
    foreach ($rii as $file) {
        if ($file->isDir()) continue;
        $path = $file->getPathname();
        if (preg_match('/\.php$/i', $path)) {
            // ignore vendor or node_modules if present
            if (strpos($path, DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR) !== false) continue;
            $files[] = $path;
        }
    }
}

if (empty($files)) {
    echo "No PHP files to lint.\n";
    exit(0);
}

$failures = [];
foreach ($files as $f) {
    // Use php -l to check syntax
    $cmd = 'php -l "' . str_replace('"', '\"', $f) . '" 2>&1';
    $out = [];
    $exit = 0;
    exec($cmd, $out, $exit);
    if ($exit !== 0) {
        $failures[$f] = $out;
    }
}

if (!empty($failures)) {
    echo "PHP lint failed for the following files:\n\n";
    foreach ($failures as $file => $out) {
        echo "File: $file\n";
        echo implode("\n", $out) . "\n\n";
    }
    echo "Commit aborted. Fix syntax errors before committing.\n";
    exit(1);
}

echo "PHP lint passed: no syntax errors found.\n";
exit(0);
