<?php


namespace the_fuel_ward\pmmp\items;


use pocketmine\item\Item;

class MedicineKitItem extends Item
{
    const Name = "MedicineKit";
    public const ITEM_ID = Item::BRICK;

    public function __construct() {
        parent::__construct(self::ITEM_ID, 0, "医療キット");
        $this->setCustomName($this->getName());
    }
}