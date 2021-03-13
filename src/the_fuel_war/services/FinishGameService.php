<?php


namespace the_fuel_war\services;


use the_fuel_war\dao\PlayerDataDAO;
use the_fuel_war\data\PlayerData;
use the_fuel_war\storages\PlayerStatusStorage;
use the_fuel_war\storages\WaitingRoomStorage;
use the_fuel_war\types\GameId;
use the_fuel_war\storages\GameStorage;

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