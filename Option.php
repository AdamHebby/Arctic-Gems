<?php
namespace Story;

class Option
{
    protected $text;
    protected $opNumber;
    protected $give;
    protected $next;
    protected $reqItems = array();

    public function __construct($next, $text, $give, $opNumber, $reqItems)
    {
        $this->text = $text;
        $this->opNumber = $opNumber;
        $this->reqItems = $reqItems;
        $this->give = $give;
        $this->next = $next;
    }
}
