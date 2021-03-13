<?php

namespace the_fuel_war\pmmp\entities;


use the_fuel_war\DataFolderPath;
use the_fuel_war\pmmp\items\MedicineKitItem;
use the_fuel_war\pmmp\services\RescueDyingPlayerPMMPService;
use the_fuel_war\storages\GameStorage;
use the_fuel_war\storages\PlayerStatusStorage;
use the_fuel_war\types\GameId;
use the_fuel_war\types\PlayerState;
use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\level\Level;
use pocketmine\level\particle\CriticalParticle;
use pocketmine\level\particle\HappyVillagerParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\TextFormat;
use pocketmine\utils\UUID;

class DyingPlayerEntity extends Human
{
    const NAME = "Dying";
    public $width = 0.6;
    public $height = 0.2;

    public string $skinName = self::NAME;
    protected string $geometryId = "geometry." . self::NAME;
    protected string $geometryName = self::NAME . ".geo.json";

    private TaskScheduler $scheduler;
    private TaskHandler $limitTaskHandler;
    private TaskHandler $rescueTaskHandler;


    private Player $owner;
    private GameId $gameId;
    private bool $isRescued;

    private array $votedPlayerNameList;

    private const RescueRange = 2;
    private const MaxRescueGauge = 5;

    private bool $requireMedicineKit;
    private ?Player $rescuingPlayer;
    private int $rescueGauge;

    public function __construct(Level $level, GameId $gameId, Player $owner, bool $requireMedicineKit, TaskScheduler $scheduler) {
        $this->owner = $owner;
        $this->gameId = $gameId;
        $this->votedPlayerNameList = [];
        $this->scheduler = $scheduler;
        $this->isRescued = false;
        $this->rescuingPlayer = null;
        $this->rescueGauge = 0;
        $this->requireMedicineKit = $requireMedicineKit;

        $game = GameStorage::findById($gameId);
        if ($game === null) return;//TODO:エラー

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

        $nameTag = "";
        foreach ($game->getPlayerNameList() as $name) {
            if ($name !== $owner->getName()) {
                $nameTag .= $name . TextFormat::WHITE . ":□ \n";
            }
        }
        $this->setNameTag($nameTag);
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

    public function spawnToAll(): void {

        $this->limitTaskHandler = $this->scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick): void {
                if ($this->isAlive()) $this->kill();
            }
        ), 20 * 15);


        $this->rescueTaskHandler = $this->scheduler->scheduleDelayedTask(new ClosureTask(
            function (int $currentTick): void {
                if ($this->rescuingPlayer === null) {
                    $this->rescueGauge = 0;
                    $this->findRescuingPlayer();

                } else if (!$this->rescuingPlayer->isOnline()) {
                    $this->rescueGauge = 0;
                    $this->findRescuingPlayer();

                } else {
                    //距離が適正
                    if ($this->distance($this->rescuingPlayer) <= self::RescueRange) {

                        //医療キットが必要なのに持っていない
                        if ($this->requireMedicineKit) {
                            $itemInHand = $this->rescuingPlayer->getInventory()->getItemInHand();
                            if (!($itemInHand->getId() === MedicineKitItem::ITEM_ID)) {
                                $this->rescueGauge = 0;
                                $this->rescuingPlayer = null;
                            }
                        }

                        //医療キットが必要ない or　医療キットが必要かつ所持
                        $this->rescueGauge++;
                        if ($this->rescueGauge === self::MaxRescueGauge) {
                            $this->isRescued = true;
                            RescueDyingPlayerPMMPService::execute($this, $this->scheduler);

                            //TODO:全部は使用しないように
                            if ($this->requireMedicineKit) $this->rescuingPlayer->getInventory()->remove(new MedicineKitItem());
                        }

                        //距離が不適
                    } else {
                        $this->rescueGauge = 0;
                        $this->rescuingPlayer = null;

                    }
                }

                $this->sendCircleParticle();
            }
        ), 20 * 1);

        parent::spawnToAll();
    }

    private function findRescuingPlayer() {
        if ($this->owner === null) return;
        if (!$this->owner->isOnline()) return;
        $ownerStatus = PlayerStatusStorage::findByName($this->owner->getName());
        if ($ownerStatus === null) return;
        if ($ownerStatus->getBelongTankId() == null) return;


        foreach ($this->getLevel()->getPlayers() as $player) {
            if ($player->isSneaking() and $player->distance($this) <= 2) {
                $playerStatus = PlayerStatusStorage::findByName($player->getName());
                if ($playerStatus === null) continue;
                if (!$ownerStatus->getBelongTankId()->equals($playerStatus->getBelongTankId())) continue;


                if ($this->requireMedicineKit) {
                    if ($player->getInventory()->contains(new MedicineKitItem())) {
                        $player->sendMessage("蘇生しています");
                        $player->sendPopup("蘇生しています");
                        $this->rescuingPlayer = $player;
                        break;
                    } else {
                        $player->sendMessage("医療キットが必要です");
                        $player->sendPopup("医療キットが必要です");
                        continue;
                    }
                } else {
                    $this->rescuingPlayer = $player;
                    break;
                }
            }
        }
    }

    private function sendCircleParticle() {
        for ($degree = 0; $degree < 360; $degree += 10) {
            $center = $this->getPosition();

            $x = self::RescueRange * sin(deg2rad($degree));
            $z = self::RescueRange * cos(deg2rad($degree));

            $pos = $center->add($x, 1, $z);
            if ($this->rescuingPlayer === null) {
                $this->getLevel()->addParticle(new CriticalParticle($pos));

            } else if (!$this->rescuingPlayer->isOnline()) {
                $this->getLevel()->addParticle(new CriticalParticle($pos));

            } else {
                if ($degree <= floor($this->rescueGauge / self::MaxRescueGauge * 360)) {
                    $this->getLevel()->addParticle(new HappyVillagerParticle($pos));

                } else {
                    $this->getLevel()->addParticle(new HappyVillagerParticle($pos));

                }
            }
        }
    }

    /**
     * @return bool
     */
    public function isRequireMedicineKit(): bool {
        return $this->requireMedicineKit;
    }

    protected function onDeath(): void {
        $this->limitTaskHandler->cancel();
        $this->rescueTaskHandler->cancel();

        parent::onDeath();
    }


    /**
     * @return Player
     */
    public function getOwner(): Player {
        return $this->owner;
    }

    /**
     * @return array
     */
    public function getVotedPlayerNameList(): array {
        return $this->votedPlayerNameList;
    }

    public function vote(string $playerName): bool {
        $game = GameStorage::findById($this->gameId);
        if ($game === null) return false;
        if (!$game->isStarted()) return false;
        if ($game->isFinished()) return false;

        if (!$this->isAlive()) return false;

        //生存者しか投票できない(フォームを開いてるときに殺されたときのために、ここでも条件分岐を挟む)
        $playerStatus = PlayerStatusStorage::findByName($playerName);
        if ($playerStatus === null) return false;
        if (!$playerStatus->getState()->equals(PlayerState::Alive())) return false;

        if (in_array($playerName, $this->votedPlayerNameList)) return false;
        $this->votedPlayerNameList[] = $playerName;

        //ネームタグの更新
        $nameTag = "";
        foreach ($game->getPlayerNameList() as $name) {
            if ($name !== $this->owner->getName()) {
                if (in_array($name, $this->votedPlayerNameList)) {
                    $nameTag .= $name . TextFormat::GREEN . ":■ \n";

                } else {
                    $nameTag .= $name . TextFormat::WHITE . ":□ \n";

                }
            }
        }
        $this->setNameTag($nameTag);

        $playersCanVoteCount =
            count(PlayerStatusStorage::getAlivePlayers($game->getGameId())) +
            count(PlayerStatusStorage::getDyingPlayers($game->getGameId()));

        $isMajority = $playersCanVoteCount - count($this->votedPlayerNameList) * 2 <= 0;

        if ($isMajority) $this->kill();
        return true;
    }

    /**
     * @return bool
     */
    public function isRescued(): bool {
        return $this->isRescued;
    }
}