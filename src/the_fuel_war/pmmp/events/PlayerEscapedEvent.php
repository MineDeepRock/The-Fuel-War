<?php


namespace the_fuel_war\pmmp\events;


use the_fuel_war\types\GameId;
use pocketmine\event\Event;
use pocketmine\Player;

class PlayerEscapedEvent extends Event
{
    private Player $player;
    private GameId $gameId;

    public function __construct(Player $player, GameId $gameId) {
        $this->player = $player;
        $this->gameId = $gameId;
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return GameId
     */
    public function getGameId(): GameId {
        return $this->gameId;
    }
}