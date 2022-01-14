<?php

    declare(strict_types=1);
    $invalidFiles = jsonStyleCheck('.');

    if (!empty($invalidFiles)) {
        foreach ($invalidFiles as $invalidFile) {
            echo $invalidFile . PHP_EOL;
        }
        exit(1);
    }

    function jsonStyleCheck(string $dir)
    {
        $invalidFiles = [];
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                if (is_dir($dir . '/' . $file)) {
                    $invalidFiles = array_merge($invalidFiles, jsonStyleCheck($dir . '/' . $file));
                } else {
                    if (fnmatch('*.json', $dir . '/' . $file)) {
                        $invalidFile = checkContentInFile($dir . '/' . $file);
                        if ($invalidFile !== false) {
                            $invalidFiles[] = $invalidFile;
                        }
                    }
                }
            }
        }
        return $invalidFiles;
    }

    function checkContentInFile(string $dir)
    {
        $fileOriginal = file_get_contents($dir);

        // Normalize line endings
        $fileOriginal = str_replace("\r\n", "\n", $fileOriginal);
        $fileOriginal = str_replace("\r", "\n", $fileOriginal);

        // Ignore line break at the end of the file
        $fileOriginal = rtrim($fileOriginal, "\n");

        // Reformat JSON using PHP
        $fileCompare = json_encode(json_decode($fileOriginal), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION);

        if ($fileOriginal == $fileCompare) {
            return false;
        }
        return $dir;
    }
