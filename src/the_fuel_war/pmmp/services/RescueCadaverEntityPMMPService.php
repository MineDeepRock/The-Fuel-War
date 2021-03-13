<?php


namespace the_fuel_war\pmmp\services;


use the_fuel_war\pmmp\entities\CadaverEntity;
use the_fuel_war\services\UpdatePlayerStateService;
use the_fuel_war\types\PlayerState;
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