<?php
$itemDir = array("Items/Food/", "Items/Special/", "Items/Tools/");

require('Inventory.php');
require('Item.php');
require('Story.php');
require('Option.php');
require('Scene.php');
require('Player.php');
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

echo "Thanks for Playing ".$objects["Player"]->getName()."\n";