<?php


namespace the_fuel_ward\services;


use the_fuel_ward\dao\PlayerDataDAO;
use the_fuel_ward\data\PlayerData;
use the_fuel_ward\pmmp\events\UpdatedGameDataEvent;
use the_fuel_ward\storages\GameStorage;
use the_fuel_ward\storages\PlayerStatusStorage;

class QuitGameService
{
    static function execute(string $playerName): bool {
        $playerData = PlayerDataDAO::findByName($playerName);
        $belongGameId = $playerData->getBelongGameId();
        if ($belongGameId === null) return false;

        PlayerDataDAO::update(new PlayerData($playerName));
        $game = GameStorage::findById($belongGameId);
        $game->removePlayer($playerName);

        PlayerStatusStorage::delete($playerName);

        $event = new UpdatedGameDataEvent($belongGameId);
        $event->call();
        return true;
    }
}