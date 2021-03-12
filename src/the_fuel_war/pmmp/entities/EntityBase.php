<?php


namespace the_fuel_ward\pmmp\entities;


use the_fuel_ward\DataFolderPath;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\UUID;

class EntityBase extends Human
{
    protected const NAME ="";

    protected string $skinId = "Standard_CustomSlim";
    public string $skinName = "";

    protected $capeData = "";

    protected string $geometryId = "";
    protected string $geometryName = "";

    public $width = 0.6;
    public $height = 1.8;
    public $eyeHeight = 1.5;

    protected $gravity = 0.08;
    protected $drag = 0.02;

    public $scale = 1.0;

    public $defaultHP = 1;
    public $uuid;

    public function __construct(Level $level, CompoundTag $nbt) {
        $this->uuid = UUID::fromRandom();
        $this->initSkin();

        parent::__construct($level, $nbt);
        $this->setRotation($this->yaw, $this->pitch);
        $this->setNameTagAlwaysVisible(true);
        $this->sendSkin();
    }

    public function initEntity(): void {
        parent::initEntity();
        $this->setScale($this->scale);
        $this->setMaxHealth($this->defaultHP);
        $this->setHealth($this->getMaxHealth());
    }

    private function initSkin(): void {
        $this->setSkin(new Skin(
            $this->skinId,
            file_get_contents(DataFolderPath::$skin . $this->skinName . ".skin"),
            $this->capeData,
            $this->geometryId,
            file_get_contents(DataFolderPath::$geometry . $this->geometryName)
        ));
    }
}