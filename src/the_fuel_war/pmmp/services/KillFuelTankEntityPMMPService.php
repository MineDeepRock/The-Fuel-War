<?php


namespace the_fuel_war\pmmp\services;


use pocketmine\level\Level;
use the_fuel_war\pmmp\entities\FuelTankEntity;
use the_fuel_war\types\FuelTankId;

class KillFuelTankEntityPMMPService
{
    static function execute(Level $level, FuelTankId $tankId): void {
        foreach ($level->getEntities() as $entity) {
            if ($entity instanceof FuelTankEntity) {
                if ($entity->getTankId()->equals($tankId)) $entity->kill();
            }
        }
    }
}