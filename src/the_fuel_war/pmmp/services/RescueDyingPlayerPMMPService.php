<?php


namespace the_fuel_ward\pmmp\services;


use the_fuel_ward\pmmp\entities\DyingPlayerEntity;
use the_fuel_ward\services\UpdatePlayerStateService;
use the_fuel_ward\types\PlayerState;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class RescueDyingPlayerPMMPService
{
    static function execute(DyingPlayerEntity $dyingPlayerEntity, TaskScheduler $taskScheduler): void {
        $player = $dyingPlayerEntity->getOwner();
        if (!$player->isOnline()) return;

        $player->setGamemode(Player::ADVENTURE);
        $player->teleport($dyingPlayerEntity);
        UpdatePlayerStateService::execute($player->getName(), PlayerState::Alive(), $taskScheduler);

        if ($dyingPlayerEntity->isAlive()) $dyingPlayerEntity->kill();
    }
}