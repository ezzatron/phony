<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = basename($path);

if (false !== strpos($file, '.')) {
    return false;
}

$content = @file_get_contents(__DIR__ . '/../../web/' . $path . '.html');

if (false === $content) {
    return false;
}

echo $content;
