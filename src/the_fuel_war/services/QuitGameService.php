<?php


namespace the_fuel_war\services;


use the_fuel_war\dao\PlayerDataDAO;
use the_fuel_war\data\PlayerData;
use the_fuel_war\pmmp\events\UpdatedGameDataEvent;
use the_fuel_war\storages\GameStorage;
use the_fuel_war\storages\PlayerStatusStorage;

class QuitGameService
{
    static function execute(string $playerName): bool {
        $playerData = PlayerDataDAO::findByName($playerName);
        $belongGameId = $playerData->getBelongGameId();
        if ($belongGameId === null) return false;

        PlayerDataDAO::update(new PlayerData($playerName));
        PlayerStatusStorage::delete($playerName);

        $game = GameStorage::findById($belongGameId);

        if ($game !== null) {
            $game->removePlayer($playerName);
            $event = new UpdatedGameDataEvent($belongGameId);
            $event->call();
        }

        return true;
    }
}