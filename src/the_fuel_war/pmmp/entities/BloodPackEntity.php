<?php


namespace the_fuel_ward\pmmp\entities;


use the_fuel_ward\dao\PlayerDataDAO;
use the_fuel_ward\DataFolderPath;
use the_fuel_ward\pmmp\scoreboards\OnGameScoreboard;
use the_fuel_ward\storages\GameStorage;
use the_fuel_ward\storages\PlayerStatusStorage;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\Server;

class BloodPackEntity extends EntityBase
{
    const NAME = "BloodPack";
    public string $skinName = self::NAME;
    protected string $geometryId = "geometry." . self::NAME;
    protected string $geometryName = self::NAME . ".geo.json";

    private int $leftOfBlood;

    public function __construct(Level $level, Vector3 $vector3) {
        $this->leftOfBlood = 2;

        $nbt = new CompoundTag('', [
            'Pos' => new ListTag('Pos', [
                new DoubleTag('', $vector3->getX()),
                new DoubleTag('', $vector3->getY()),
                new DoubleTag('', $vector3->getZ())
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
        ]);
        parent::__construct($level, $nbt);
    }

    public function onAttackedByPlayer(Player $player): void {
        if ($this->leftOfBlood === 0) return;

        $playerData = PlayerDataDAO::findByName($player->getName());
        $game = GameStorage::findById($playerData->getBelongGameId());
        if ($game === null) return;
        if (!$game->isStarted() or $game->isFinished()) return;

        $status = PlayerStatusStorage::findByName($player->getName());
        $result = $status->addBlood();
        if (!$result) return;

        $this->leftOfBlood--;
        if ($this->leftOfBlood === 1) {
            $this->skinName = "HalfBloodPack";
        } else if ($this->leftOfBlood === 0) {
            $this->skinName = "EmptyBloodPack";
        }

        $this->setSkin(new Skin(
            "Standard_CustomSlim",
            file_get_contents(DataFolderPath::$skin . $this->skinName . ".skin"),
            "",
            $this->geometryId,
            file_get_contents(DataFolderPath::$geometry . $this->geometryName)
        ));

        $this->sendSkin();


        $player->sendMessage("血液を取得しました");
        $gamePlayers = [];
        foreach ($game->getPlayerNameList() as $name) {
            $gamePlayer = Server::getInstance()->getPlayer($name);
            if ($gamePlayer === null) return;
            $gamePlayers[] = $gamePlayer;
        }

        OnGameScoreboard::update($gamePlayers, $game);
    }
}