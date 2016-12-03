<?php
namespace Story;

class Scene
{
    protected $type;
    protected $name;
    protected $id;
    protected $visited;
    protected $hidden;
    public function __construct($id, $name, $text, $give, $giveXP, $optionObjArr, $type)
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
    public function getOptionList()
    {
        return $this->optionObjArr;
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
