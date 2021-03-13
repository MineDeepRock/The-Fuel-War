<?php


namespace the_fuel_war\pmmp\entities;


use the_fuel_war\data\ItemDataOnMap;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;

abstract class ItemOnMapEntity extends EntityBase
{

    public function __construct(Level $level, Position $position) {
        parent::__construct($level, $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $position->getX()),
                new DoubleTag('', $position->getY()),
                new DoubleTag('', $position->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', 0),
                new DoubleTag('', 0),
                new DoubleTag('', 0)
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", 0),
                new FloatTag("", 0)
            ]),
        ]));
    }

    abstract public function onAttackedByPlayer(Player $player): void;
}