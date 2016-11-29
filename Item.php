<?php
namespace Inventory;

class Item
{
    protected $type;
    protected $name;
    protected $id;
    protected $health;
    protected $carry;
    protected $hp;
    protected $damage;
    protected $crit;
    public function __construct($name, $id, $hp, $type, $crit, $carry, $health, $damage)
    {
        $this->id = $id;
        $this->hp = $hp;
        $this->type = $type;
        $this->name = $name;
        $this->crit = $crit;
        $this->carry = $carry;
        $this->health = $health;
        $this->damage = $damage;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getHp()
    {
        return $this->hp;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getCrit()
    {
        return $this->crit;
    }
    public function getCarry()
    {
        return $this->carry;
    }
    public function getHealth()
    {
        return $this->health;
    }
    public function getDamage()
    {
        return $this->damage;
    }
    public function setId($input)
    {
        $this->id = $input;
    }
    public function setHp($input)
    {
        $this->hp = $input;
    }
    public function setType($input)
    {
        $this->type = $input;
    }
    public function setName($input)
    {
        $this->name = $input;
    }
    public function setCrit($input)
    {
        $this->crit = $input;
    }
    public function setCarry($input)
    {
        $this->carry = $input;
    }
    public function setHealth($input)
    {
        $this->health = $input;
    }
    public function setDamage($input)
    {
        $this->damage = $input;
    }
}
