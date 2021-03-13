<?php


namespace the_fuel_war\pmmp\entities;


use the_fuel_war\DataFolderPath;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\utils\UUID;

class CadaverEntity extends Human
{
    const NAME = "Cadaver";
    public $width = 0.6;
    public $height = 0.2;

    protected string $geometryId = "geometry." . self::NAME;
    protected string $geometryName = self::NAME . ".geo.json";

    private Player $owner;


    public function __construct(Level $level, Player $owner) {
        $this->owner = $owner;
        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $owner->getX()),
                new DoubleTag('', $owner->getY()),
                new DoubleTag('', $owner->getZ())
            ]),
            'Motion' => new ListTag('Motion', [
                new DoubleTag('', 0),
                new DoubleTag('', 0),
                new DoubleTag('', 0)
            ]),
            'Rotation' => new ListTag('Rotation', [
                new FloatTag("", $owner->getYaw()),
                new FloatTag("", 0)
            ]),
        ]);
        $this->uuid = UUID::fromRandom();
        $this->initSkin($owner);

        parent::__construct($level, $nbt);
        $this->setRotation($this->yaw, $this->pitch);
        $this->setNameTagAlwaysVisible(false);
        $this->sendSkin();
    }

    private function initSkin(Player $player): void {
        $this->setSkin(new Skin(
            "Standard_CustomSlim",
            $player->getSkin()->getSkinData(),
            "",
            $this->geometryId,
            file_get_contents(DataFolderPath::$geometry . $this->geometryName)
        ));
    }

    /**
     * @return Player
     */
    public function getOwner(): Player {
        return $this->owner;
    }
}