<?php


namespace the_fuel_ward\pmmp\events;


use the_fuel_ward\types\FuelTankId;
use the_fuel_ward\types\GameId;
use pocketmine\event\Event;

class FuelTankBecameFullEvent extends Event
{
    private FuelTankId $tankId;
    private GameId $belongGameId;

    public function __construct(GameId $belongGameId, FuelTankId $tankId) {
        $this->tankId = $tankId;
        $this->belongGameId = $belongGameId;
    }

    /**
     * @return FuelTankId
     */
    public function getTankId(): FuelTankId {
        return $this->tankId;
    }

    /**
     * @return GameId
     */
    public function getBelongGameId(): GameId {
        return $this->belongGameId;
    }
}