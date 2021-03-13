<?php


namespace the_fuel_war\pmmp\events;


use the_fuel_war\types\FuelTankId;
use the_fuel_war\types\GameId;
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