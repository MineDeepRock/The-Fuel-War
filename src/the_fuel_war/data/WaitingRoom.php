<?php


namespace the_fuel_war\data;


use pocketmine\math\Vector3;

class WaitingRoom
{
    private Vector3 $vector;
    private bool $isAvailable;

    public function __construct(Vector3 $vector, bool $isAvailable) {
        $this->vector = $vector;
        $this->isAvailable = $isAvailable;
    }

    /**
     * @return Vector3
     */
    public function getVector(): Vector3 {
        return $this->vector;
    }

    /**
     * @return bool
     */
    public function isAvailable(): bool {
        return $this->isAvailable;
    }
}