<?php


namespace the_fuel_ward\pmmp\services;


use the_fuel_ward\pmmp\entities\CadaverEntity;
use the_fuel_ward\services\UpdatePlayerStateService;
use the_fuel_ward\types\PlayerState;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;

class RescueCadaverEntityPMMPService
{
    static function execute(CadaverEntity $cadaverEntity, TaskScheduler $scheduler): void {
        $player = $cadaverEntity->getOwner();
        if (!$player->isOnline()) return;

        $player->setGamemode(Player::ADVENTURE);
        $player->teleport($cadaverEntity);
        UpdatePlayerStateService::execute($player->getName(), PlayerState::Alive(), $scheduler);

        if ($cadaverEntity->isAlive()) $cadaverEntity->kill();
    }
}