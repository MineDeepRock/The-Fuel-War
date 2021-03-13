<?php


namespace the_fuel_war\pmmp\items;


use the_fuel_war\pmmp\services\TransformToWolfPMMPService;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use slot_menu_system\pmmp\items\SlotMenuElementItem;
use slot_menu_system\SlotMenuElement;

class TransformItem extends SlotMenuElementItem
{
    const ItemId = ItemIds::BOOK;
    const Name = "人狼に変身";

    public function __construct() {
        parent::__construct(new SlotMenuElement(self::ItemId, self::Name, function (Player $player) {
            TransformToWolfPMMPService::execute($player);
        }), self::ItemId, 0, self::Name);
    }
}