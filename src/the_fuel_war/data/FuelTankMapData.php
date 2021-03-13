<?php


namespace the_fuel_war\data;


use pocketmine\math\Vector3;

class FuelTankMapData
{
    private int $capacity;
    private Vector3 $vector;


    public function __construct(int $capacity, Vector3 $vector) {
        $this->capacity = $capacity;
        $this->vector = $vector;
    }

    /**
     * @return int
     */
    public function getCapacity(): int {
        return $this->capacity;
    }

    /**
     * @return Vector3
     */
    public function getVector(): Vector3 {
        return $this->vector;
    }
}