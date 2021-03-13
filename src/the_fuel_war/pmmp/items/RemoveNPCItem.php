<?php


namespace the_fuel_war\pmmp\items;


use pocketmine\item\Item;

class RemoveNPCItem extends Item
{
    const Name = "RemoveNPC";
    public const ITEM_ID = Item::STICK;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "NPC殺すソード");
        $this->setCustomName($this->getName());
    }
}