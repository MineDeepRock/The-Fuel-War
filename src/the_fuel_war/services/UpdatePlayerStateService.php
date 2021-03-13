<?php


namespace the_fuel_war\services;


use the_fuel_war\models\PlayerStatus;
use the_fuel_war\storages\PlayerStatusStorage;
use the_fuel_war\types\PlayerState;
use pocketmine\scheduler\TaskScheduler;

class UpdatePlayerStateService
{
    static function execute(string $playerName, PlayerState $playerState, TaskScheduler $taskScheduler): void {
        $playerStatus = PlayerStatusStorage::findByName($playerName);
        if ($playerStatus === null) return;

        PlayerStatusStorage::update(new PlayerStatus($playerName, $playerStatus->getBelongGameId(), $playerStatus->getBelongTankId(), $playerState, $taskScheduler));
    }
}