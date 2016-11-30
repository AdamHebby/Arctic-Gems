<?php
$itemDir = array("Items/Food/", "Items/Special/", "Items/Tools/");

spl_autoload_register(function ($class_name) {
    $class = end(explode("\\",$class_name));
    include $class . '.php';
});
require('Functions.php');

$objects = startGame($itemDir);
$Player = $objects["Player"];
$Inv = $objects["Inv"];
$Story = $objects["Story"];

while ($objects["Player"]->continuePlaying()) {
    // Keep Playing
    nextScene($objects);
    $objects["Player"]->continuePlaying(false);
}

// echo "Thanks for Playing ".$objects["Player"]->getName()."\n";
