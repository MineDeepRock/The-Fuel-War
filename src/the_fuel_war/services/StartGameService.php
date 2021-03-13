<?php


namespace the_fuel_war\services;


use the_fuel_war\types\GameId;
use the_fuel_war\storages\GameStorage;
use pocketmine\scheduler\TaskScheduler;

class StartGameService
{
    static function execute(string $ownerName, GameId $gameId, TaskScheduler $taskScheduler): bool {
        $game = GameStorage::findById($gameId);
        if ($game === null) return false;
        if ($game->getGameOwnerName() !== $ownerName) return false;

        DivideIntoTeamsService::execute($gameId, $taskScheduler);

        $game->start();
        return true;
    }
}