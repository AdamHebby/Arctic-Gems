<?php
$genID = "ITEM_001";
$itemDir = array("Items/Food/", "Items/Special/", "Items/Tools/");

require('Inventory.php');
require('Item.php');
require('Story.php');
require('Option.php');
require('Scene.php');
require('Player.php');

function getOptions($file)
{
    global $genID;
    $options = array(
        "name" => "",
        "id" => "",
        "hp" => "",
        "type" => "",
        "crit" => "",
        "carry" => "",
        "health" => "",
        "damage" => ""
        );
    $handle = fopen($file, "r");
    if ($handle) {
        $options["id"] = (string)$genID;
        while (($line = fgets($handle)) !== false) {
            $obj = explode(":", $line);
            $val = $obj[1];
            $obj = $obj[0];
            $options[$obj] = trim($val);
        }

        fclose($handle);
        $genID++;
    } else {
       
    }
    return $options;
}

function loadItems()
{
    global $Inv;
    global $itemDir;

    foreach ($itemDir as $val) {
        if ($handle = opendir($val)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $options = array();
                    $options = getOptions($val . $entry);
                    $entry = substr($entry, 0, -4);
                    $newItem = new Item($options["name"], $options["id"], $options["hp"], $options["type"], $options["crit"], $options["carry"], $options["health"], $options["damage"]);
                    $Inv->addItem($newItem);
                }
            }
            closedir($handle);
        }
    }
}

function showMenu()
{
    echo "\n1) Add an Item \n2) Remove an Item \n3) List Items \n4) Exit Application\n";
    $option = readline('$ ');
    switch ($option) {
        case 1:
            giveItem();
            break;
        case 2:
            removeItem();
            break;
        case 3:
            listItems();
            break;
        case 4:
            exit();
            break;
        default:
            break;
    }
}

function listItems()
{
    global $Inv;
    foreach ($Inv as $key => $value) {
        echo($value['item']->getId()." ".$value['item']->getName()."\n");
    }
}

function startGame()
{

}

function clear()
{
    if (strtoupper(PHP_OS) === "LINUX" || strtoupper(PHP_OS) === "CYGWIN") {
        system('clear');
    } else {
        system('cls');
    }
}

// --- Load Classes --- // 
$Inv = new Inventory();
loadItems();
// print_r($Inv);
$Story = new Story();
$Story->loadScenes();
// print_r($Story);
$Player = new Player("Adam");
$Player->loadLevels();
// print_r($Player);

echo "Welcome ".$Player->getName()." \n";
$Player->giveXP(100); // Give 100 XP

$Inv->showPlayerItems();

// showMenu();

