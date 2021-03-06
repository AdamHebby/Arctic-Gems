<?php
namespace Player;

class Player
{
    protected $name;
    protected $health = 100;
    protected $xp = 0;
    protected $level = 1;
    protected $maxCarry = 100;
    protected $levels = array();
    protected $continuePlaying = true;
    protected $currentScene = "SCENE_INTRO";
    protected $objectReferences;

    public function __construct($name)
    {
        $this->name = $name;
        $this->continuePlaying = true;
    }
    public function getReferences()
    {
        return $this->objectReferences;
    }
    public function StoreReferences($objects)
    {
        $this->objectReferences = $objects;
    }
    public function loadLevels()
    {
        $handle = fopen("Files/levels.txt", "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $obj = explode(":", $line);
                $val = $obj[1];
                $obj = $obj[0];
                $this->levels["$obj"] = trim($val);
            }
            fclose($handle);
        }
    }
    public function giveXP($amount)
    {
        $this->xp += $amount;
        for ($i=$this->level; $i < count($this->levels); $i++) {
            if ($this->xp < $this->levels["$i"]) {
                $this->level = ($i - 1);
                break;
            } else if ($this->xp == $this->levels["$i"]) {
                $this->level = $i;
                break;
            } // else carry on
        }
    }
    public function continuePlaying($input = NULL)
    {
        if ($input === NULL) {
            return $this->continuePlaying;
        } else {
            $this->continuePlaying = false;
        }
    }
    public function getCurrentScene()
    {
        return $this->currentScene;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getHealth()
    {
        return $this->health;
    }
    public function getXP()
    {
        return $this->xp;
    }
    public function getLevel()
    {
        return $this->level;
    }
    public function getMaxCarry()
    {
        return $this->maxCarry;
    }
    public function setCurrentScene($scene)
    {
        $this->currentScene = $scene;
    }
}
