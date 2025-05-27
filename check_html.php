<?php
$html = file_get_contents('index.php');

// Check for HTML structure issues
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
$errors = libxml_get_errors();
libxml_clear_errors();

echo "HTML validation errors found: " . count($errors) . "<br>";

foreach ($errors as $error) {
    echo "Line " . $error->line . ": " . $error->message . "<br>";
}
?> 