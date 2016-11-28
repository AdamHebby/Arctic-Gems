<?php
$itemDir = array("Items/Food/", "Items/Special/", "Items/Tools/");

require('Inventory.php');
require('Item.php');
require('Story.php');
require('Option.php');
require('Scene.php');
require('Player.php');

function getOptions($file)
{
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
        while (($line = fgets($handle)) !== false) {
            $obj = explode(":", $line);
            $val = $obj[1];
            $obj = $obj[0];
            $options[$obj] = trim($val);
        }

        fclose($handle);
    } else {
       
    }
    return $options;
}

function loadItems($Inv, $itemDir)
{
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

function listItems($Inv)
{
    foreach ($Inv as $key => $value) {
        echo($value['item']->getId()." ".$value['item']->getName()."\n");
    }
}

function startGame($itemDir)
{
    $valName = false;
    while ($valName == false) {
        clear();
        $name = ask("What is your name, Brave Traveller? ", 0);
        $valName = validateName($name);
    }

    $Inv = new Inventory();
    loadItems($Inv, $itemDir);
    $Story = new Story();
    $Story->loadScenes();
    $Player = new Player($name);
    $Player->loadLevels();
    echo "Welcome ".$Player->getName()." \n";
    $Player->giveXP(100); // Give 100 XP
}

function validateName($name)
{
    if (strlen($name) > 10) {
        customError(2, true);
        return false;
    } elseif (!ctype_alpha($name)) {
        customError(3, true);
        return false;
    } else {
        return true;
    }
}

function ask($input, $inline)
{
    if ($inline == 1) {
        $answer = readline($input);
    } else {
        echo $input . "\n";
        $answer = readline();
    }
    if (trim($answer) == "") {
        customError(1, true);
        ask($input, $inline);
    }
    return $answer;
}

function customError($n, $rl)
{
    switch ($n) {
        case 1:
            echo "Invalid Input! \n";
            break;
        case 2:
            echo "Invalid Input! Must not be longer than 10 characters \n";
            break;
        case 3:
            echo "Invalid Input! Must contain only characters \n";
            break;
        default:
            break;
    }
    if ($rl) {
        readline();
    }
}

function clear()
{
    if (strtoupper(PHP_OS) === "LINUX" || strtoupper(PHP_OS) === "CYGWIN") {
        system('clear');
    } else {
        system('cls');
    }
}

startGame($itemDir);
