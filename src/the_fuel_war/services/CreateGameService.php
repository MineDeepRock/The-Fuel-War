<?php

namespace the_fuel_ward\services;


use the_fuel_ward\dao\MapDAO;
use the_fuel_ward\dao\PlayerDataDAO;
use the_fuel_ward\models\Game;
use the_fuel_ward\storages\GameStorage;
use the_fuel_ward\storages\WaitingRoomStorage;
use pocketmine\scheduler\TaskScheduler;

class CreateGameService
{
    static function execute(string $gameOwnerName, string $mapName, int $maxPlayers, TaskScheduler $scheduler): bool {
        $ownerData = PlayerDataDAO::findByName($gameOwnerName);
        if ($ownerData->getBelongGameId() !== null) return false;

        $waitingRoom = WaitingRoomStorage::useRandomAvailableRoom();
        if ($waitingRoom === null) return false;

        $map = MapDAO::findByName($mapName);
        if (count($map->getFuelTankMapDataList()) > $maxPlayers) return false;

        $game = new Game($gameOwnerName, $map, $maxPlayers, $waitingRoom, $scheduler);
        $result = GameStorage::add($game);

        if (!$result) return false;


        //オーナーも参加させる
        JoinGameService::execute($game->getGameId(), $game->getGameOwnerName(), $scheduler);
        return true;
    }
}