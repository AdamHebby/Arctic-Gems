<?php
namespace Story;

class Option
{
    protected $text;
    protected $opNumber;
    protected $give;
    protected $next;
    protected $reqItems = array();

    public function __construct($next = null, $text, $give, $opNumber, $reqItems = null)
    {
        $this->text = $text;
        $this->opNumber = $opNumber;
        $this->reqItems = $reqItems;
        $this->give = $give;
        $this->next = $next;
    }
    public function getOptionText()
    {
        return $this->text;
    }
    public function hasRequiredItems()
    {
        if ($this->reqItems == null) {
            return false;
        } else {
            return true;
        }
    }
    public function getRequiredItems()
    {
        return $this->reqItems;
    }
    public function getGive()
    {
        return $this->give;
    }
    public function getNextScene()
    {
        return $this->next;
    }
    public function getOptionNumber()
    {
        return $this->opNumber;
    }
}
