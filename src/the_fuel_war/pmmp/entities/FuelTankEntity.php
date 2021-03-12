<?php


namespace the_fuel_ward\pmmp\entities;


use the_fuel_ward\types\FuelTankId;
use the_fuel_ward\types\GameId;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\utils\TextFormat;

class FuelTankEntity extends EntityBase
{
    private GameId $belongGameId;
    private FuelTankId $fuelTankId;

    const NAME = "FuelTank";

    public string $skinName = self::NAME;
    public string $geometryId = "geometry." . self::NAME;
    public string $geometryName = self::NAME . ".geo.json";

    public function __construct(Level $level, Position $position, GameId $belongGameId, FuelTankId $fuelTankId) {
        $this->belongGameId = $belongGameId;
        $this->fuelTankId = $fuelTankId;

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

        $this->setNameTagAlwaysVisible();
        $this->updateTankGauge(0.0);
    }

    /**
     * @return FuelTankId
     */
    public function getTankId(): FuelTankId {
        return $this->fuelTankId;
    }

    /**
     * @return GameId
     */
    public function getBelongGameId(): GameId {
        return $this->belongGameId;
    }

    public function updateTankGauge(float $percentage): void {
        if ($percentage >= 1) {
            $this->setNameTag(str_repeat(TextFormat::GREEN . "■", 30));
        } else if ($percentage <= 0) {
            $this->setNameTag(str_repeat(TextFormat::WHITE . "■", 30));
        } else {
            $fuelGauge = str_repeat(TextFormat::GREEN . "■", floor($percentage * 30));
            $blankGauge = str_repeat(TextFormat::WHITE . "■", 30 - floor($percentage * 30));

            $this->setNameTag($fuelGauge . $blankGauge);
        }
    }
}