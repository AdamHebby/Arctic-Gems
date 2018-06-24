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

function loadItems(&$Inv, $itemDir)
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

function listItems(&$Inv)
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

function isMappedFunction($input) // I love this :D
{
    $mappedFunction = array("inv" => "showInventory", "menu" => "showMenu");
    if (strlen(trim($input)) > 0) {
        if (!is_numeric($input) && array_key_exists(strtolower(trim($input)), $mappedFunction)) {
            return array(true, $mappedFunction[$input]);
        } else {
            return $input;
        }
    }
    return false;
}

function readCLine($echo = null)
{
    $line = readline($echo);
    $mappedFunction = isMappedFunction($line);
    if (is_array($mappedFunction) && $mappedFunction[0] === true) {
        return $mappedFunction;
    } elseif ($mappedFunction != false) {
        return $line;
    }
}

function getMenuOption($echo = null)
{
    $menuOptions = array("saveGame", "loadGame", "exitGame");
    $line = readline($echo);
    if (strlen(trim($line)) > 0 && is_numeric($line) && array_key_exists(trim($line) - 2, $menuOptions)) {
        if (trim($line) == 1) {
            return null;
        }
        return array(true, $menuOptions[$line - 2]);
    } elseif (!strlen(trim($line)) > 0 || !is_numeric($line)) {
        customError(4, true);
        return null;
    }
}

function saveGame(&$Inv, &$Story, &$Player, &$sceneObject)
{
    $objects = array($Inv, $Story, $Player, $sceneObject);
    $compressed = gzcompress(serialize($objects), 9);
    $date = date('Y-m-d-H-i-s');
    file_put_contents("Files/gamesaves/$date.data", $compressed);
    echo "\033[32mGame Saved!\033[0m \n";
}

function loadGame(&$Inv, &$Story, &$Player, &$sceneObject)
{
    clear();
    echo "\033[32mWhich save would you like to load?\033[0m \n";
    $saveDir = 'Files/gamesaves/';
    $saves = array_diff(scandir($saveDir), array('..', '.'));
    $saves = array_values($saves);
    foreach ($saves as $key => $value) {
        echo $key + 1 . ") $value \n";
    }
    $userChoice = readCLine("{$Player->getName()} >");
    $userChoice--;
    if (isset($saves[$userChoice])) {
        $contents = file_get_contents("Files/gamesaves/{$saves[$userChoice]}");
        $contents = unserialize(gzuncompress($contents));
        $Inv = $contents[0];
        $Story = $contents[1];
        $Player = $contents[2];
        $sceneObject = $contents[3];
        echo "\033[32mGame Loaded!\033[0m \n";
        return;
    }
    return;
}

function exitGame()
{
    exit();
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
        anyKey();
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
    $objects = array("Inv" => &$Inv, "Story" => &$Story, "Player" => &$Player); // & References the original Values
    $Player->StoreReferences($objects);
    return $objects;
}

function nextScene(&$Inv, &$Story, &$Player)
{
    $sceneObject = $Story->getScene($Player->getCurrentScene());
    $sceneObject = $sceneObject["scene"];
    showScene($Inv, $Story, $Player, $sceneObject, true);
}

function showScene(&$Inv, &$Story, &$Player, Story\Scene &$sceneObject, $showText)
{
    if ($showText) {
        clear();
        echo($sceneObject->getText() . "\n\n");
        if ($sceneObject->getVisited() == false && $sceneObject->getFirstText() != null) {
            echo($sceneObject->getFirstText() . "\n\n");
        }
    }
    if ($sceneObject->hasGiveItemOnLoad() && $sceneObject->getVisited() == false) {
        givePlayerItems($Inv, $Player, $sceneObject->getGive());
    }
    if ($sceneObject->hasGiveXPOnLoad() && $sceneObject->getVisited() == false) {
        givePlayerXP($Player, $sceneObject->getGiveXP());
    }
    $sceneObject->setVisited();
    $options = $sceneObject->getOptionObj();
    showUserOptions($options);
    $userChoice = false;
    while ($userChoice == false) {
        $userChoice = getUserChoice($Inv, $Story, $Player, $sceneObject, count($options));
    }
    $chosenOption = $options["op-".$userChoice];
    $parsed = parseUserChoice($Inv, $Player, $chosenOption, $sceneObject);
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
            $optionNumber = explode('-', $key); // Explode works in php7, split doesnt
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
            echo "  $itemName ($itemQty) \n";
        }
    }
    anyKey();
    echo "\n";
}
function parseUserChoice(&$Inv, &$Player, &$chosenOption, &$sceneObject)
{
    if ($chosenOption->hasRequiredItems() == false) {
        $nextScene = $chosenOption->getNextScene();
        if ($nextScene != null) {
            $Player->setCurrentScene($nextScene);
        }
        parseOptionImpact($Inv, $Player, $chosenOption, $sceneObject);
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
            parseOptionImpact($Inv, $Player, $chosenOption, $sceneObject);
            return true;
        }
    }
}

function parseOptionImpact(&$Inv, &$Player, &$chosenOption, &$sceneObject)
{
    if ($chosenOption->getGive() != null && !$chosenOption->getOptionUsed()) {
        givePlayerItems($Inv, $Player, $chosenOption->getGive());
        $chosenOption->optionUsed();
        anyKey();
    } elseif ($chosenOption->getGive() != null && $chosenOption->getOptionUsed()) {
        echo "There is nothing else here\n";
        anyKey();
    }
    if ($chosenOption->unlocks() === true && !$chosenOption->getOptionUsed()) {
        $chosenOption->optionUsed();
        unlockStoryLine($Inv, $Player, $chosenOption, $sceneObject);
    } elseif ($chosenOption->unlocks() === true && $chosenOption->getOptionUsed()) {
        echo "You see nothing new\n";
        anyKey();
    }
}

function unlockStoryLine(&$Inv, &$Player, $chosenOption, &$sceneObject)
{
    $unlock = $chosenOption->getUnlock();
    if (isset($unlock["new-option"])) {
        echo $unlock["text"] . "\n";
        $optionObj = $sceneObject->getOptionObj($unlock["new-option"]);
        $optionObj->setHidden(false);
        echo "\e[33mUnlocked New Option! \e[39m\n";
        anyKey();
    }
    if (isset($unlock["new-scene"])) {

    }
    if (isset($unlock["new-item"])) {

    }
}

function givePlayerItems(&$Inv, &$Player, $giveItems)
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

function getUserChoice(&$Inv, &$Story, &$Player, &$sceneObject, $optionCount) 
{
    echo "\033[32mEnter a number that represents an option.\033[0m \n";
    $userChoice = readCLine("{$Player->getName()} >");
    if (is_array($userChoice) && $userChoice[0] == true) {
        $userChoice[1]($Inv, $Story, $Player, $sceneObject);
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

function showInventory(&$Inv)
{
    clear();
    echo "\t Inventory \n";
    $Inv->showPlayerItems();
    anyKey();
}
function showMenu(&$Inv, &$Story, &$Player, &$sceneObject)
{
    clear();
    echo "\t Menu \n";
    echo "1) Return \n2) Save Game \n3) Load Game \n4) Exit Game\n";
    echo "\033[32mEnter a number that represents an option.\033[0m \n";
    $option = getMenuOption();
    if (is_array($option) && $option[0] === true) {
        // RUN USER SELECTED FUNCTION
        $option[1]($Inv, $Story, $Player, $sceneObject);
        anyKey();
    }
}
function givePlayerXP($Player, $amount)
{
    $Player->giveXP($amount); // Give XP
    echo "\e[33mXP Given! Amount: $amount New XP: {$Player->getXP()} ";
    echo "Lvl: {$Player->getLevel()} \e[39m\n";
}
