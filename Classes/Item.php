<?php
namespace Inventory;

class Item
{
    protected $id;
    protected $name;
    protected $type;
    protected $hp;
    protected $damage;
    protected $givesHealth;
    protected $inventorySpace;
    protected $critDamage;
    protected $critChance;

    public function __construct(
        $id,
        $name,
        $type,
        $hp = 0,
        $damage = null,
        $givesHealth = null,
        $inventorySpace = 1,
        $critDamage = null,
        $critChance = null
    )
    {
        $this->id             = $id;
        $this->name           = $name;
        $this->type           = $type;
        $this->hp             = $hp;
        $this->damage         = $damage;
        $this->givesHealth    = $givesHealth;
        $this->inventorySpace = $inventorySpace;
        $this->critDamage     = $critDamage;
        $this->critChance     = $critChance;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getType()
    {
        return $this->type;
    }
    public function getHp()
    {
        return $this->hp;
    }
    public function getDamage()
    {
        return $this->damage;
    }
    public function getGivesHealth()
    {
        return $this->givesHealth;
    }
    public function getInventorySpace()
    {
        return $this->inventorySpace;
    }
    public function getCritDamage()
    {
        return $this->critDamage;
    }
    public function getCritChance()
    {
        return $this->critChance;
    }
    public function setId($input)
    {
        $this->id = $input;
    }
    public function setName($input)
    {
        $this->name = $input;
    }
    public function setType($input)
    {
        $this->type = $input;
    }
    public function setHp($input)
    {
        $this->hp = $input;
    }
    public function setDamage($input)
    {
        $this->damage = $input;
    }
    public function setGivesHealth($input)
    {
        $this->givesHealth = $input;
    }
    public function setInventorySpace($input)
    {
        $this->inventorySpace = $input;
    }
    public function setCritDamage($input)
    {
        $this->critDamage = $input;
    }
    public function setCritChance($input)
    {
        $this->critChance = $input;
    }
}