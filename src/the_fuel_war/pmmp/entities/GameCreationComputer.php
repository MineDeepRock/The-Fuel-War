<?php


namespace the_fuel_war\pmmp\entities;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class GameCreationComputer extends EntityBase
{
    const NAME = "GameCreationComputer";
    public string $skinName = self::NAME;
    public string $geometryId = "geometry." . self::NAME;
    public string $geometryName = self::NAME . ".geo.json";

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->setNameTag("ゲームを作成");
        $this->setNameTagAlwaysVisible(true);
    }
}