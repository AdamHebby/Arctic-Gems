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

function anyKey()
{
    echo "\n\e[33mPress [ANY] key to continue.\e[39m \n";
    $term = `stty -g`;
    system("stty -icanon");
    fread(STDIN, 1);
    system("stty sane");
}

function mappedKeys() // I love this :D
{
    $mappedKeys = array("e" => "showInventory", "m" => "showMenu");
    system("stty -icanon");
    $input = fread(STDIN, 1);
    system("stty sane");
    if (strlen(trim($input)) > 0) {
        echo chr(8); // Backspace
        if (!is_numeric($input) && array_key_exists(strtolower(trim($input)), $mappedKeys)) {
            return array(true, $mappedKeys[$input]);
        } else {
            return $input;
        }
    }
    return false;
}

function readCLine($echo = null)
{
    $key = mappedKeys();
    if ($key[0] === true) {
        return $key;
    } elseif ($key != false){
        $line = readline($echo . $key);
        if (strlen($line) == 0 && $line == null) {
            return $key;
        } else {
            return $line;
        }
    } else {
        return null;
    }
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
    $Story->loadScenes($Inv);
    $Player = new Player\Player($name);
    $Player->loadLevels();
    clear();
    $Player->giveXP(100); // Give 100 XP
    $objects = array("Inv" => $Inv, "Story" => $Story, "Player" => $Player);
    return $objects;
}

function nextScene($Inv, $Story, $Player)
{
    $sceneObject = $Story->getScene($Player->getCurrentScene());
    $sceneObject = $sceneObject["scene"];
    showScene($Inv, $Story, $Player, $sceneObject, true);
}

function showScene($Inv, $Story, $Player, Story\Scene $sceneObject, $showText)
{
    if ($showText) {
        clear();
        echo($sceneObject->getText() . "\n\n");
    }
    if ($sceneObject->hasGiveItemOnLoad() && $sceneObject->getVisited() == false) {
        givePlayerItems($Inv, $Player, $sceneObject->getGive());
        $sceneObject->setVisited();
    }
    if ($sceneObject->hasGiveXPOnLoad() && $sceneObject->getVisited() == false) {
        givePlayerXP($Player, $sceneObject->getGiveXP());
        $sceneObject->setVisited();
    }

    $options = $sceneObject->getOptionList();
    showUserOptions($options);
    $userChoice = false;
    while ($userChoice == false) {
        $userChoice = getUserChoice($Inv, $Story, $Player, $sceneObject, $Player->getName(), count($options));
    }
    $chosenOption = $options["op-".$userChoice];
    $parsed = parseUserChoice($Inv, $Player, $chosenOption);
    if ($parsed == true) {
        nextScene($Inv, $Story, $Player);
    } else {
        showScene($Inv, $Story, $Player, $sceneObject, true);
    }
}

function showUserOptions($options)
{
    foreach ($options as $key => $option) {
        if ($option->isHidden() == false) {
            $optionText = $option->getOptionText();
            $optionNumber = split('-', $key);
            echo "[$optionNumber[1]] $optionText \n";
        }
    }
}

function denyRequiredItems($reqItems, $neededItems)
{
    echo "\033[31mYou do not have the correct items to go here\033[0m \nYou need: \n";
    foreach ($reqItems as $item) {
        $itemName = $item["item"]->getName();
        $itemId = $item["item"]->getId();
        $itemQty = $item["qty"];
        if (isset($neededItems[$itemId])) {
            $itemQty = $neededItems[$itemId]["qtyNeeded"];
        }
        echo "  $itemName ($itemQty) \n";
    }
    anyKey();
    echo "\n";
}
function parseUserChoice($Inv, $Player, $chosenOption)
{
    if ($chosenOption->hasRequiredItems() == false) {
        $nextScene = $chosenOption->getNextScene();
        if ($nextScene != null) {
            $Player->setCurrentScene($nextScene);
        }
        parseOptionImpact($Inv, $Player, $chosenOption);
        return true;
    } else {
        $reqItems = $chosenOption->getRequiredItems();
        foreach ($reqItems as $item) {
            $itemOwnedCount = $Inv->getItemCount($item["item"]);
            $itemId = $item["item"]->getId();
            if ($itemOwnedCount < $item["qty"]) {
                $needed = $item["qty"] - $itemOwnedCount;
                $lacking = array("id" => $itemId, "qtyNeeded" => $needed);
                $neededItems[$itemId] = $lacking;
            }
        }
        if (isset($neededItems)) {
            denyRequiredItems($reqItems, $neededItems);
            return false;
        } else {
            $nextScene = $chosenOption->getNextScene();
            if ($nextScene != null) {
                $Player->setCurrentScene($nextScene);
            }
            parseOptionImpact($Inv, $Player, $chosenOption);
            return true;
        }
    }
}

function parseOptionImpact($Inv, $Player, $chosenOption)
{
    if ($chosenOption->getGive() != null && $chosenOption->getOptionUsed() == false) {
        givePlayerItems($Inv, $Player, $chosenOption->getGive());
        $chosenOption->optionUsed();
        readline();
    } elseif ($chosenOption->getGive() != null && $chosenOption->getOptionUsed() == true) {
        echo "There is nothing else here\n";
        readline();
    }
}

function givePlayerItems($Inv, $Player, $giveItems)
{
    foreach ($giveItems as $giveId => $giveOptions) {
        $count = $giveOptions["count"];
        $itemObj = $Inv->getItemByID($giveId);
        $itemName = $itemObj->getName();
        echo $giveOptions["text"] . "\n\n";
        echo "\e[33mItem Found! +$count '$itemName'\e[39m\n";
        $Inv->updateItem($itemObj, $count);
    }
    echo "\n";
}

function getUserChoice($Inv, $Story, $Player, $sceneObject, $userName, $optionCount)
{   
    echo "\033[32mEnter a number that represents an option.\033[0m \n";
    echo "$userName >";
    $userChoice = readCLine();
    if (is_array($userChoice) && $userChoice[0] == true) {
        $userChoice[1]($Inv, $Story, $Player);
        showScene($Inv, $Story, $Player, $sceneObject, true);
    }
    if ($userChoice > $optionCount) {
        customError(4, true);
        return false;
    } elseif (!preg_match("/^[0-9]+$/", $userChoice)) {
        customError(4, true);
        return false;
    }
    return $userChoice;
}

function showInventory($Inv)
{
    clear();
    echo "\t Inventory \n";
    $Inv->showPlayerItems();
    anyKey();
}