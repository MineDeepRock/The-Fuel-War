<?php


namespace the_fuel_war\services;


use the_fuel_war\dao\PlayerDataDAO;
use the_fuel_war\models\PlayerStatus;
use the_fuel_war\pmmp\events\UpdatedGameDataEvent;
use the_fuel_war\storages\PlayerStatusStorage;
use the_fuel_war\types\GameId;
use the_fuel_war\data\PlayerData;
use the_fuel_war\storages\GameStorage;
use the_fuel_war\types\PlayerState;
use pocketmine\scheduler\TaskScheduler;

class JoinGameService
{
    static function execute(GameId $gameId, string $playerName, TaskScheduler $scheduler): bool {
        $game = GameStorage::findById($gameId);
        if ($game === null) return false;

        //PlayerData更新
        $result = $game->addPlayer($playerName);
        if (!$result) return false;

        $status = new PlayerStatus($playerName, $gameId, null, PlayerState::Alive(), $scheduler);
        PlayerStatusStorage::add($status);

        PlayerDataDAO::update(new PlayerData($playerName, $gameId));

        $event = new UpdatedGameDataEvent($gameId);
        $event->call();
        return true;
    }
}