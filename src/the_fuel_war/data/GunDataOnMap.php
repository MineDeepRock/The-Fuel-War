<?php


namespace the_fuel_ward\data;


use the_fuel_ward\pmmp\entities\ItemGunEntity;
use pocketmine\entity\Entity;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

class GunDataOnMap
{
    private string $name;
    private Vector3 $vector;

    public function __construct(string $name, Vector3 $vector3) {
        $this->name = $name;
        $this->vector = $vector3;
    }

    public function getAsEntity(Level $level): Entity {
        return new ItemGunEntity($level, Position::fromObject($this->vector, $level), $this->name);
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