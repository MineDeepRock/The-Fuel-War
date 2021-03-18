<?php


namespace the_fuel_war\pmmp\entities;


use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use the_fuel_war\types\GameType;

class GameListBulletinBoard extends EntityBase
{
    const NAME = "GameListBulletinBoard";
    public string $skinName = self::NAME;
    public string $geometryId = "geometry." . self::NAME;
    public string $geometryName = self::NAME . ".geo.json";
    private GameType  $gameType;

    public function __construct(Level $level, CompoundTag $nbt) {
        $gameType = GameType::fromString($nbt->getString("GameType"));
        if ($gameType !== null) {
            $this->gameType = $gameType;
        } else {
            //TODO:エラー
        }

        parent::__construct($level, $nbt);
        $this->setNameTag("ゲーム一覧");
        $this->setNameTagAlwaysVisible(true);
    }

    /**
     * @return GameType
     */
    public function getGameType(): GameType {
        return $this->gameType;
    }
}