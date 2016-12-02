<?php
function getOptions($file)
{
    $options = array(
        "ItemID" => "",
        "ItemName" => "",
        "ItemType" => "",
        "HP" => "",
        "Damage" => "",
        "GivesHealth" => "",
        "InventorySpace" => "",
        "CriticalDamage" => "",
        "CriticalChance" => ""
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
                    $newItem = new Inventory\Item(
                        $options["ItemID"],
                        $options["ItemName"],
                        $options["ItemType"],
                        $options["HP"],
                        $options["Damage"],
                        $options["GivesHealth"],
                        $options["InventorySpace"],
                        $options["CriticalDamage"],
                        $options["CriticalChance"]
                    );
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

function validateName($name)
{
    if (strlen($name) > 15) {
        customError(2, true);
        return false;
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
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
        $answer = readline();
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
            echo "Invalid Input! Must not be longer than 15 characters \n";
            break;
        case 3:
            echo "Invalid Input! Must contain only characters \n";
            break;
        case 4:
            echo "Invalid Input! That is not an option \n";
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

function startGame($itemDir)
{
    $valName = false;
    while ($valName == false) {
        clear();
        $name = ask("What is your name, Brave Traveller? ", 0);
        $valName = validateName($name);
    }
    $Inv = new Inventory\Inventory();
    loadItems($Inv, $itemDir);
    $Story = new Story\Story();
    $Story->loadScenes();
    $Player = new Player\Player($name);
    $Player->loadLevels();
    echo "Welcome ".$Player->getName()."\n";
    $Player->giveXP(100); // Give 100 XP
    $objects = array("Inv" => $Inv, "Story" => $Story, "Player" => $Player);
    return $objects;
}

function nextScene($objects)
{
    $Inv = $objects["Inv"];
    $Story = $objects["Story"];
    $Player = $objects["Player"];
    $sceneObject = $Story->getScene($Player->getCurrentScene());
    $sceneObject = $sceneObject["scene"];
    showScene($Inv, $Story, $Player, $sceneObject);
}

function showScene($Inv, $Story, $Player, Story\Scene $sceneObject)
{
    echo($sceneObject->getText() . "\n\n");
    $options = $sceneObject->getOptionList();
    foreach ($options as $key => $option) {
        $optionText = $option->getOptionText();
        $optionNumber = split('-', $key);
        echo "[$optionNumber[1]] $optionText \n";
    }
    $userChoice = getUserChoice($Player->getName(), count($options));
}

function getUserChoice($userName, $optionCount)
{   
    echo "\033[32mEnter a number that represents an option.\033[0m \n";
    $userChoice = readline("$userName > ");
    if ($userChoice > $optionCount) {
        customError(4, true);
    } elseif (!preg_match("/^[0-9]+$/", $userChoice)) {
        customError(4, true);
    } 
}