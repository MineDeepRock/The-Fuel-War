<?php


namespace the_fuel_ward\pmmp\items;


use pocketmine\item\Item;

class FuelItem extends Item
{
    public const ITEM_ID = Item::BLAZE_POWDER;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "ガソリン");
        $this->setCustomName($this->getName());
    }
}