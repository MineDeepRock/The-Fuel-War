<?php


namespace the_fuel_war\pmmp\entities;


use the_fuel_war\pmmp\items\MedicineKitItem;
use pocketmine\Player;

class MedicineKitOnMapEntity extends ItemOnMapEntity
{
    const NAME = MedicineKitItem::Name;

    public string $skinName = self::NAME;
    public string $geometryId = "geometry." . self::NAME;
    public string $geometryName = self::NAME . ".geo.json";

    public function onAttackedByPlayer(Player $player): void {
        $player->getInventory()->addItem(new MedicineKitItem());
        if ($this->isAlive()) $this->kill();
    }
}