<?php

namespace the_fuel_war;

use pocketmine\block\Block;
use pocketmine\level\Position;
use pocketmine\scheduler\ClosureTask;
use the_fuel_war\dao\PlayerDataDAO;
use the_fuel_war\data\PlayerData;
use the_fuel_war\pmmp\entities\BloodPackEntity;
use the_fuel_war\pmmp\entities\CadaverEntity;
use the_fuel_war\pmmp\entities\DyingPlayerEntity;
use the_fuel_war\pmmp\entities\FuelEntity;
use the_fuel_war\pmmp\entities\FuelTankEntity;
use the_fuel_war\pmmp\entities\GameCreationComputer;
use the_fuel_war\pmmp\entities\GameListBulletinBoard;
use the_fuel_war\pmmp\entities\ItemGunEntity;
use the_fuel_war\pmmp\entities\MedicineKitOnMapEntity;
use the_fuel_war\pmmp\forms\CreateGameForm;
use the_fuel_war\pmmp\forms\GameListForm;
use the_fuel_war\pmmp\forms\GameSettingForm;
use the_fuel_war\pmmp\forms\MainMapForm;
use the_fuel_war\pmmp\forms\SpawnNPCForm;
use the_fuel_war\pmmp\forms\WaitingRoomListForm;
use the_fuel_war\pmmp\forms\WorldListForm;
use the_fuel_war\pmmp\items\RemoveNPCItem;
use the_fuel_war\pmmp\listeners\GameListener;
use the_fuel_war\pmmp\scoreboards\LobbyScoreboard;
use the_fuel_war\services\QuitGameService;
use the_fuel_war\storages\GameStorage;
use the_fuel_war\pmmp\utilities\GetWorldNameList;
use the_fuel_war\pmmp\utilities\SavePlayerSkin;
use the_fuel_war\storages\WaitingRoomStorage;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

class Main extends PluginBase implements Listener
{
    public function onEnable() {
        Entity::registerEntity(BloodPackEntity::class, true, ['BloodPack']);
        Entity::registerEntity(CadaverEntity::class, true, ['Cadaver']);
        Entity::registerEntity(DyingPlayerEntity::class, true, ['Dying']);
        Entity::registerEntity(FuelEntity::class, true, ['Fuel']);
        Entity::registerEntity(FuelTankEntity::class, true, ['FuelTank']);
        Entity::registerEntity(ItemGunEntity::class, true, ['Gun']);
        Entity::registerEntity(MedicineKitOnMapEntity::class, true, ['MedicineKit']);

        DataFolderPath::init($this->getDataFolder(), $this->getFile() . "resources/");
        WaitingRoomStorage::loadAllWaitingRooms();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getPluginManager()->registerEvents(new GameListener($this->getScheduler()), $this);

        foreach (GetWorldNameList::execute() as $worldName) {
            Server::getInstance()->loadLevel($worldName);
        }
    }

    public function onJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $lobbyLevel = Server::getInstance()->getLevelByName("lobby");
        $player->setImmobile(false);
        $player->setGamemode(Player::ADVENTURE);
        $player->teleport($lobbyLevel->getSpawnLocation());

        $playerName = $player->getName();
        SavePlayerSkin::execute($player);

        if (PlayerDataDAO::findByName($playerName) === null) {
            $playerData = new PlayerData($playerName);
            PlayerDataDAO::save($playerData);
        }

        LobbyScoreboard::send($player);

        $pk = new GameRulesChangedPacket();
        $pk->gameRules["doImmediateRespawn"] = [1, true];
        $player->sendDataPacket($pk);
    }

    public function onQuit(PlayerQuitEvent $event) {
        $playerName = $event->getPlayer()->getName();

        QuitGameService::execute($playerName);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if ($label === "create") {
                $sender->sendForm(new CreateGameForm($this->getScheduler()));
                return true;
            }
            if ($label === "setting") {
                $playerData = PlayerDataDAO::findByName($sender->getName());
                $game = GameStorage::findById($playerData->getBelongGameId());

                if ($game === null) {
                    $sender->sendMessage("ゲームに参加していないか、ゲームのオーナではありません");
                } else {
                    $sender->sendForm(new GameSettingForm($this->getScheduler()));
                }

                return true;
            }
            if ($label === "map") {
                $sender->sendForm(new MainMapForm());
                return true;
            }
            if ($label === "gamelist") {
                $sender->sendForm(new GameListForm($sender, $this->getScheduler()));
                return true;
            }
            if ($label === "room") {
                $sender->sendForm(new WaitingRoomListForm());
                return true;
            }
            if ($label === "npc") {
                $sender->sendForm(new SpawnNPCForm());
                return true;
            }
            if ($label === "removenpc") {
                $sender->getInventory()->addItem(Item::get(RemoveNPCItem::ITEM_ID));
                return true;
            }
            if ($label === "worldlist") {
                $sender->sendForm(new WorldListForm());
                return true;
            }
        }

        return false;
    }

    public function onTapGameListBulletinBoard(EntityDamageByEntityEvent $event) {
        $gameListBulletinBoard = $event->getEntity();
        $attacker = $event->getDamager();
        if (!($attacker instanceof Player)) return;
        if (!($gameListBulletinBoard instanceof GameListBulletinBoard)) return;
        if ($attacker->getInventory()->getItemInHand()->getId() === RemoveNPCItem::ITEM_ID) $gameListBulletinBoard->kill();
        $event->setCancelled();

        $attacker->sendForm(new GameListForm($attacker, $this->getScheduler()));
    }

    public function onTapGameCreationComputer(EntityDamageByEntityEvent $event) {
        $GameCreationComputer = $event->getEntity();
        $attacker = $event->getDamager();
        if (!($attacker instanceof Player)) return;
        if (!($GameCreationComputer instanceof GameCreationComputer)) return;
        if ($attacker->getInventory()->getItemInHand()->getId() === RemoveNPCItem::ITEM_ID) $GameCreationComputer->kill();
        $event->setCancelled();

        $attacker->sendForm(new CreateGameForm($this->getScheduler()));
    }

    public function onExhaust(PlayerExhaustEvent $event) {
        $event->setCancelled();
    }
}