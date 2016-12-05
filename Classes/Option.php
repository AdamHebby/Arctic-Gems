<?php
namespace Story;

class Option
{
    protected $text;
    protected $opNumber;
    protected $give;
    protected $next;
    protected $reqItems = array();
    protected $optionUsed;
    protected $hiddenOption;

    public function __construct($next = null, $text, $give, $opNumber, $reqItems = null, $hiddenOption)
    {
        $this->text = $text;
        $this->opNumber = $opNumber;
        $this->reqItems = $reqItems;
        $this->give = $give;
        $this->next = $next;
        $this->optionUsed = false;
        $this->hiddenOption = $hiddenOption;
    }
    public function isHidden()
    {
        return $this->hiddenOption;
    }
    public function optionUsed()
    {
        $this->optionUsed = true;
    }
    public function getOptionUsed()
    {
        return $this->optionUsed;
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
