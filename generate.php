<?php

/**
 * PHP File Generator CLI
 * Usage: php generate.php nama-file
 * Example: php generate.php user/profile
 */

// Warna untuk terminal
define('COLOR_GREEN', "\033[32m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_RED', "\033[31m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_RESET', "\033[0m");

function createDirectory($path)
{
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        echo COLOR_GREEN . "✓ Folder created: " . COLOR_RESET . $path . "\n";
        return true;
    }
    return false;
}

function createPageFile($filePath, $fileName)
{
    $content = <<<'PHP'
<?php
/**
 * Page: {fileName}
 * Created: {date}
 */
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{title}</h4>
            </div>
            <div class="card-body">
                <p>Content for {fileName}</p>
            </div>
        </div>
    </div>
</div>
PHP;

    $title = ucwords(str_replace(['-', '_'], ' ', basename($fileName)));
    $content = str_replace('{fileName}', $fileName, $content);
    $content = str_replace('{title}', $title, $content);
    $content = str_replace('{date}', date('Y-m-d H:i:s'), $content);

    file_put_contents($filePath, $content);
    echo COLOR_GREEN . "✓ Page created: " . COLOR_RESET . $filePath . "\n";
}

function createActionFile($filePath, $fileName)
{
    $content = <<<'PHP'
<?php
/**
 * Action: {fileName}
 * Created: {date}
 */

session_start();
include '../../functions/sanitasi.php';
include '../../config.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    // $data = sani($_POST['field_name']);
    
    // Process your logic here
    
    // Redirect back with message
    $_SESSION['message'] = 'Action completed successfully!';
    $_SESSION['message_type'] = 'success';
    
    header('Location: ../../index.php?hal={redirect}');
    exit;
} else {
    // If accessed directly, redirect to homepage
    header('Location: ../../index.php');
    exit;
}
PHP;

    $redirect = str_replace('/', '_', $fileName);
    $content = str_replace('{fileName}', $fileName, $content);
    $content = str_replace('{redirect}', $redirect, $content);
    $content = str_replace('{date}', date('Y-m-d H:i:s'), $content);

    file_put_contents($filePath, $content);
    echo COLOR_GREEN . "✓ Action created: " . COLOR_RESET . $filePath . "\n";
}

// Main execution
if ($argc < 2) {
    echo COLOR_RED . "Error: " . COLOR_RESET . "Please provide a file name\n";
    echo COLOR_YELLOW . "Usage: " . COLOR_RESET . "php generate.php nama-file\n";
    echo COLOR_YELLOW . "Example: " . COLOR_RESET . "php generate.php user/profile\n";
    exit(1);
}

$fileName = $argv[1];
$basePath = __DIR__;

echo COLOR_BLUE . "\n=== PHP File Generator ===" . COLOR_RESET . "\n";
echo "Generating files for: " . COLOR_YELLOW . $fileName . COLOR_RESET . "\n\n";

// Process pages folder
$pagesPath = $basePath . '/pages/';
if (strpos($fileName, '/') !== false) {
    // Ada folder, buat folder dulu
    $pathParts = explode('/', $fileName);
    $file = array_pop($pathParts);
    $folderPath = $pagesPath . implode('/', $pathParts);

    createDirectory($folderPath);
    $pageFile = $folderPath . '/' . $file . '.php';
} else {
    $pageFile = $pagesPath . $fileName . '.php';
}

// Check if file already exists
if (file_exists($pageFile)) {
    echo COLOR_RED . "✗ Page file already exists: " . COLOR_RESET . $pageFile . "\n";
} else {
    createPageFile($pageFile, $fileName);
}

// Process actions folder
$actionsPath = $basePath . '/actions/pages/';
if (strpos($fileName, '/') !== false) {
    // Ada folder, buat folder dulu
    $pathParts = explode('/', $fileName);
    $file = array_pop($pathParts);
    $folderPath = $actionsPath . implode('/', $pathParts);

    createDirectory($folderPath);
    $actionFile = $folderPath . '/' . $file . '.php';
} else {
    $actionFile = $actionsPath . $fileName . '.php';
}

// Check if file already exists
if (file_exists($actionFile)) {
    echo COLOR_RED . "✗ Action file already exists: " . COLOR_RESET . $actionFile . "\n";
} else {
    createActionFile($actionFile, $fileName);
}

echo "\n" . COLOR_GREEN . "✓ Generation completed!" . COLOR_RESET . "\n";
echo COLOR_YELLOW . "Page URL: " . COLOR_RESET . "index.php?hal=" . str_replace('/', '_', $fileName) . "\n";
echo COLOR_YELLOW . "Action URL: " . COLOR_RESET . "actions/index.php?hal=" . str_replace('/', '_', $fileName) . "\n\n";
