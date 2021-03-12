<?php


namespace the_fuel_ward\pmmp\entities;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;

class GameListBulletinBoard extends EntityBase
{
    const NAME = "GameListBulletinBoard";
    public string $skinName = self::NAME;
    public string $geometryId = "geometry." . self::NAME;
    public string $geometryName = self::NAME . ".geo.json";

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
        $this->setNameTag("ゲーム一覧");
        $this->setNameTagAlwaysVisible(true);
    }
}