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
    protected $unlocks;

    public function __construct(
        $next = null,
        $text,
        $give,
        $opNumber,
        $reqItems = null,
        $hiddenOption,
        $unlocks = null
    )
    {
        $this->text         = $text;
        $this->opNumber     = $opNumber;
        $this->reqItems     = $reqItems;
        $this->give         = $give;
        $this->next         = $next;
        $this->optionUsed   = false;
        $this->hiddenOption = $hiddenOption;
        $this->unlocks      = $unlocks;
    }
    public function unlocks()
    {
        if ($this->unlocks != null) {
            return true;
        } else {
            return false;
        }
    }
    public function getUnlock()
    {
        return $this->unlocks;
    }
    public function isHidden()
    {
        return $this->hiddenOption;
    }
    public function setHidden($bool = true)
    {
        return $this->hiddenOption = $bool;
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
        }
        return true;
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
