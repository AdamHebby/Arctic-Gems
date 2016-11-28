<?php 
$itemDir = array("Items/Food/", "Items/Special/", "Items/Tools/");

if ($argv[1] == "convert") {
    foreach ($itemDir as $val) {
        if ($handle = opendir($val)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $contents = file_get_contents($val.$entry);
                    if (substr($entry, -4) == ".txt") {
                        $fileName = substr($entry, 0, -4);
                        unlink($val.$entry);
                        $newFile = fopen($val.$fileName, "w");
                        fwrite($newFile, $contents);
                        fclose($newFile);
                    }
                }
            }
            closedir($handle);
        }
    }
} elseif ($argv[1] == "undo") {
    foreach ($itemDir as $val) {
        if ($handle = opendir($val)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $contents = file_get_contents($val.$entry);
                    if (substr($entry, -4) != ".txt") {
                        $fileName = $entry;
                        unlink($val.$entry);
                        $newFile = fopen($val.$fileName.".txt", "w");
                        fwrite($newFile, $contents);
                        fclose($newFile);
                    }
                }
            }
            closedir($handle);
        }
    }
}