<?php


namespace the_fuel_ward\pmmp\events;


use the_fuel_ward\types\GameId;
use pocketmine\event\Event;

class UpdatedGameDataEvent extends Event
{
    private GameId $gameId;

    public function __construct(GameId $gameId) {
        $this->gameId = $gameId;
    }

    /**
     * @return GameId
     */
    public function getGameId(): GameId {
        return $this->gameId;
    }
}