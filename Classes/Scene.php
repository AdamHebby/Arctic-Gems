<?php
namespace Story;

class Scene
{
    protected $type;
    protected $name;
    protected $id;
    protected $visited;
    protected $hidden;
    protected $firstText;
    public function __construct($id, $name, $text, $firstText = null, $give, $giveXP, $optionObjArr, $type)
    {
        $this->id = $id;
        $this->name = $name;
        $this->text = $text;
        $this->give = $give;
        $this->giveXP = $giveXP;
        $this->optionObjArr = $optionObjArr;
        $this->type = $type;
        $this->visited = false;
        $this->hidden = false;
        $this->firstText = $firstText;
    }
    public function isHidden()
    {
        return $this->hidden;
    }
    public function setVisited()
    {
        $this->visited = true;
    }
    public function getVisited()
    {
        return $this->visited;
    }
    public function hasGiveItemOnLoad()
    {
        if ($this->give != null) {
            return true;
        } else {
            return false;
        }
    }
    public function hasGiveXPOnLoad()
    {
        if ($this->giveXP != null) {
            return true;
        } else {
            return false;
        }
    }
    public function getGive()
    {
        return $this->give;
    }
    public function getGiveXP()
    {
        return $this->giveXP;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getText()
    {
        return $this->text;
    }
    public function getFirstText()
    {
        return $this->firstText;
    }
    public function getOptionObj($option = null)
    {
        if ($option == null) {
            return $this->optionObjArr;
        } elseif (isset($this->optionObjArr[$option])) {
            return $this->optionObjArr[$option];
        }
    }
    public function setId($input)
    {
        $this->id = $input;
    }
    public function setType($input)
    {
        $this->type = $input;
    }
    public function setName($input)
    {
        $this->name = $input;
    }
}
