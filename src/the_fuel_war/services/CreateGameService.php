<?php

namespace the_fuel_war\services;


use the_fuel_war\dao\MapDAO;
use the_fuel_war\dao\PlayerDataDAO;
use the_fuel_war\models\Game;
use the_fuel_war\storages\GameStorage;
use the_fuel_war\storages\UsingMapNameList;
use the_fuel_war\storages\WaitingRoomStorage;
use pocketmine\scheduler\TaskScheduler;
use the_fuel_war\types\GameType;

class CreateGameService
{
    static function execute(string $gameOwnerName, string $mapName, GameType $gameType, int $maxPlayers, bool $canRespawn, TaskScheduler $scheduler): bool {
        $ownerData = PlayerDataDAO::findByName($gameOwnerName);
        if ($ownerData->getBelongGameId() !== null) return false;

        $waitingRoom = WaitingRoomStorage::useRandomAvailableRoom();
        if ($waitingRoom === null) return false;

        $map = MapDAO::findByName($mapName);
        if ($map === null) return false;
        if (count($map->getFuelTankMapDataList()) > $maxPlayers) return false;

        if (UsingMapNameList::isExist($mapName)) return false;
        UsingMapNameList::add($mapName);

        $game = new Game($gameOwnerName, $map, $gameType, $maxPlayers, $canRespawn, $waitingRoom, $scheduler);
        return GameStorage::add($game);
    }
}