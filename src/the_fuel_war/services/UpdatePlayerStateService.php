<?php


namespace the_fuel_ward\services;


use the_fuel_ward\models\PlayerStatus;
use the_fuel_ward\storages\PlayerStatusStorage;
use the_fuel_ward\types\PlayerState;
use pocketmine\scheduler\TaskScheduler;

class UpdatePlayerStateService
{
    static function execute(string $playerName, PlayerState $playerState, TaskScheduler $taskScheduler): void {
        $playerStatus = PlayerStatusStorage::findByName($playerName);
        if ($playerStatus === null) return;

        PlayerStatusStorage::update(new PlayerStatus($playerName, $playerStatus->getBelongGameId(), $playerStatus->getBelongTankId(), $playerState, $taskScheduler));
    }
}