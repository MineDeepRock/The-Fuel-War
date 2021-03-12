<?php


namespace the_fuel_ward\services;


use the_fuel_ward\dao\PlayerDataDAO;
use the_fuel_ward\data\PlayerData;
use the_fuel_ward\storages\PlayerStatusStorage;
use the_fuel_ward\storages\WaitingRoomStorage;
use the_fuel_ward\types\GameId;
use the_fuel_ward\storages\GameStorage;

class FinishGameService
{
    static function execute(GameId $gameId): void {
        $game = GameStorage::findById($gameId);
        if ($game === null) return;

        foreach ($game->getPlayerNameList() as $playerName) {
            PlayerDataDAO::update(new PlayerData($playerName));
            PlayerStatusStorage::delete($playerName);
        }

        $game->finish();
        WaitingRoomStorage::returnWaitingRoom($game->getWaitingRoom());

        GameStorage::delete($gameId);
    }
}