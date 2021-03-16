<?php


namespace the_fuel_war\pmmp\utilities;


use pocketmine\level\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use the_fuel_war\pmmp\entities\GameCreationComputer;
use the_fuel_war\pmmp\entities\GameListBulletinBoard;

class SpawnNPC
{
    static function execute(string $name, Location $location): void {
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $location->getX()),
                new DoubleTag('', $location->getY()),
                new DoubleTag('', $location->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', 0),
                new DoubleTag('', 0),
                new DoubleTag('', 0)
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", $location->getYaw()),
                new FloatTag("", 0)
            ]),
        ]);

        switch ($name) {
            case GameListBulletinBoard::NAME:
                $entity = new GameListBulletinBoard($location->getLevel(), $nbt);
                $entity->spawnToAll();
                break;
            case GameCreationComputer::NAME:
                $entity = new GameCreationComputer($location->getLevel(), $nbt);
                $entity->spawnToAll();
                break;
        }
    }
}