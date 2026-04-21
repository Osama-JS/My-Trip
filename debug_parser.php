<?php
require 'vendor/autoload.php';
use PhpParser\ParserFactory;

$parser = (new ParserFactory)->createForNewestSupportedVersion();

function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $results[] = $path;
            }
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
        }
    }
    return $results;
}

$files = getDirContents('app');
foreach ($files as $file) {
    try {
        $parser->parse(file_get_contents($file));
    } catch (\Throwable $e) {
        echo "ERROR $file : " . $e->getMessage() . "\n";
    }
}
echo "Done checking all app files.\n";
