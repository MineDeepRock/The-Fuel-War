<?php


namespace the_fuel_war\data;


use the_fuel_war\pmmp\entities\MedicineKitOnMapEntity;
use the_fuel_war\pmmp\items\MedicineKitItem;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class ItemDataOnMap
{
    private string $name;
    private Vector3 $vector;

    public function __construct(string $name, Vector3 $vector3) {
        $this->name = $name;
        $this->vector = $vector3;
    }

    static function getItem(string $name): ?Item {
        switch ($name) {
            case MedicineKitItem::Name:
                return new MedicineKitItem();
        }

        return null;
    }

    public function getAsEntity(Level $level): ?Entity {
        switch ($this->name) {
            case MedicineKitItem::Name:
                return new MedicineKitOnMapEntity($level, Position::fromObject($this->vector, $level));
        }

        return null;
    }

    /**
     * @return Vector3
     */
    public function getVector(): Vector3 {
        return $this->vector;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }
}