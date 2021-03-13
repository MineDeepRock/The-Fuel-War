<?php

namespace the_fuel_war\services;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\dao\PlayerDataDAO;
use the_fuel_war\models\Game;
use the_fuel_war\storages\GameStorage;
use the_fuel_war\storages\WaitingRoomStorage;
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
        return GameStorage::add($game);
    }
}