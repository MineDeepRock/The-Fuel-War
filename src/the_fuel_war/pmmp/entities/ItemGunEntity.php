<?php


namespace the_fuel_war\pmmp\entities;


use gun_system\GunSystem;
use pocketmine\item\Arrow;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;

class ItemGunEntity extends EntityBase
{
    const NAME = "Gun";

    public string $skinName = self::NAME;
    public string $geometryId = "geometry." . self::NAME;
    public string $geometryName = self::NAME . ".geo.json";

    private string $gunName;

    public function __construct(Level $level, Position $position, string $gunName) {
        $this->gunName = $gunName;

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

    public function onAttackedByPlayer(Player $player): void {
        $player->getInventory()->addItem(GunSystem::getItemGun($this->gunName));
        $player->getInventory()->addItem(new Arrow());
        if ($this->isAlive()) $this->kill();
    }
}