<?php

namespace the_fuel_ward\pmmp\listeners;


use the_fuel_ward\dao\PlayerDataDAO;
use the_fuel_ward\data\PlayerData;
use the_fuel_ward\pmmp\entities\BloodPackEntity;
use the_fuel_ward\pmmp\entities\CadaverEntity;
use the_fuel_ward\pmmp\entities\DyingPlayerEntity;
use the_fuel_ward\pmmp\entities\FuelEntity;
use the_fuel_ward\pmmp\entities\FuelTankEntity;
use the_fuel_ward\pmmp\entities\ItemGunEntity;
use the_fuel_ward\pmmp\entities\ItemOnMapEntity;
use the_fuel_ward\pmmp\events\FuelTankBecameFullEvent;
use the_fuel_ward\pmmp\events\UpdatedGameDataEvent;
use the_fuel_ward\pmmp\forms\ConfirmVoteForm;
use the_fuel_ward\pmmp\items\FuelItem;
use the_fuel_ward\pmmp\scoreboards\GameSettingsScoreboard;
use the_fuel_ward\pmmp\scoreboards\OnGameScoreboard;
use the_fuel_ward\pmmp\services\FinishGamePMMPService;
use the_fuel_ward\pmmp\services\SendTeamChatPMMPService;
use the_fuel_ward\services\FinishGameService;
use the_fuel_ward\services\UpdatePlayerStateService;
use the_fuel_ward\storages\GameStorage;
use the_fuel_ward\storages\PlayerStatusStorage;
use the_fuel_ward\types\PlayerState;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;


//TODO:停電機能
class GameListener implements Listener
{
    private TaskScheduler $scheduler;

    public function __construct(TaskScheduler $scheduler) {
        $this->scheduler = $scheduler;
    }

    public function onDamagedFuelTankEntity(EntityDamageEvent $event) {
        $fuelTankEntity = $event->getEntity();
        if (!($fuelTankEntity instanceof FuelTankEntity)) return;
        $event->setCancelled();

        if ($event instanceof EntityDamageByEntityEvent) {

            //Player以外ならキャンセル
            $attacker = $event->getDamager();
            if (!($attacker instanceof Player)) return;

            $attackerData = PlayerDataDAO::findByName($attacker->getName());
            $belongGameId = $attackerData->getBelongGameId();
            $fuelTankBelongGameId = $fuelTankEntity->getBelongGameId();

            //試合に参加していない or 別の試合 ならキャンセル
            if ($belongGameId === null) return;
            if (!($belongGameId->equals($fuelTankBelongGameId))) return;
            $game = GameStorage::findById($belongGameId);

            //燃料を持ってタップしたら、手に持っている分だけタンクに追加
            $itemInHand = $attacker->getInventory()->getItemInHand();
            if ($itemInHand->getId() === FuelItem::ITEM_ID) {
                $fuelTank = $game->getFuelTankById($fuelTankEntity->getTankId());
                if ($fuelTank === null) return;

                //Tankの所有者でないならニセの燃料を追加
                $attackerStatus = PlayerStatusStorage::findByName($attacker->getName());
                $isFakeFuel = $attackerStatus->getBelongTankId()->equals($fuelTank->getTankId());

                $result = $fuelTank->addFuel($itemInHand->getCount(), $isFakeFuel);
                if ($result) {
                    //TODO: マックスを超えた分は消費しないように
                    $attacker->getInventory()->clear($attacker->getInventory()->getHeldItemIndex());
                    $fuelTankEntity->updateTankGauge($fuelTank->getAmountPercentage());

                    if ($isFakeFuel) {
                        $attacker->sendPopup(TextFormat::BLUE . "タンクにニセの燃料を入れました");
                        $attacker->sendMessage(TextFormat::GREEN . "タンクにニセの燃料を入れました");
                    } else {
                        $attacker->sendPopup(TextFormat::GREEN . "タンクに燃料を入れました");
                        $attacker->sendMessage(TextFormat::GREEN . "タンクに燃料を入れました");
                    }
                }
            }
        }
    }

    public function onDamagedFuelEntity(EntityDamageEvent $event) {
        $fuelEntity = $event->getEntity();
        if (!($fuelEntity instanceof FuelEntity)) return;

        if ($event instanceof EntityDamageByEntityEvent) {

            //Player以外ならキャンセル
            $attacker = $event->getDamager();
            if (!($attacker instanceof Player)) return;

            $attackerData = PlayerDataDAO::findByName($attacker->getName());
            $belongGameId = $attackerData->getBelongGameId();

            //試合に参加していない ならキャンセル
            if ($belongGameId === null) return;

            $attacker->getInventory()->addItem(new FuelItem());
        }
    }


    public function onFuelTankBecameFull(FuelTankBecameFullEvent $event): void {
        $gameId = $event->getBelongGameId();

        FinishGameService::execute($gameId);
        FinishGamePMMPService::execute($gameId);
    }

    public function onGamePlayerDeath(PlayerDeathEvent $event): void {
        $player = $event->getPlayer();
        $playerData = PlayerDataDAO::findByName($player->getName());
        if (!$this->belongGameIsInProgress($playerData)) return;
        $game = GameStorage::findById($playerData->getBelongGameId());

        //スコアボード更新
        $gamePlayers = [];
        foreach ($game->getPlayerNameList() as $name) {
            $gamePlayer = Server::getInstance()->getPlayer($name);
            if ($gamePlayer === null) return;
            $gamePlayers[] = $gamePlayer;
        }
        OnGameScoreboard::update($gamePlayers, $game);

        //変身中のプレイヤーに殺された場合
        $cause = $player->getLastDamageCause();
        if (!($cause instanceof EntityDamageByEntityEvent)) return;
        $killer = $cause->getDamager();
        if (!($killer instanceof Player)) return;

        $killerStatus = PlayerStatusStorage::findByName($killer->getName());
        if ($killerStatus->nowTransforming()) {
            $dyingPlayerEntity = new DyingPlayerEntity($player->getLevel(), $playerData->getBelongGameId(), $player, true, $this->scheduler);

        } else {
            $dyingPlayerEntity = new DyingPlayerEntity($player->getLevel(), $playerData->getBelongGameId(), $player, false, $this->scheduler);

        }

        $player->setSpawn($player->getPosition());
        $dyingPlayerEntity->spawnToAll();

        //全員死んだら引き分けになる
        if (count(PlayerStatusStorage::getAlivePlayers($game->getGameId())) === 0) {
            FinishGameService::execute($game->getGameId());
            FinishGamePMMPService::execute($game->getGameId());
        }

        //他のチームが全滅したら
        $allOtherTeamsDead = true;
        foreach (PlayerStatusStorage::getPlayers($game->getGameId()) as $status) {
            if ($killerStatus->getBelongTankId()->equals($status->getBelongTankId())) continue;
            if ($status->getState()->equals(PlayerState::Alive())) $allOtherTeamsDead = false;
        }

        if ($allOtherTeamsDead) {
            FinishGameService::execute($game->getGameId());
            FinishGamePMMPService::execute($game->getGameId(), $killerStatus->getBelongTankId());
        }
    }

    public function onGamePlayerRespawn(PlayerRespawnEvent $event): void {
        $player = $event->getPlayer();
        $playerData = PlayerDataDAO::findByName($player->getName());
        if (!$this->belongGameIsInProgress($playerData)) return;

        $player->setGamemode(Player::SPECTATOR);
        $player->setImmobile(true);
        UpdatePlayerStateService::execute($player->getName(), PlayerState::Dying(), $this->scheduler);
    }

    public function onTapDyingPlayerEntity(EntityDamageByEntityEvent $event) {
        $dyingPlayerEntity = $event->getEntity();
        $attacker = $event->getDamager();
        if (!($attacker instanceof Player)) return;
        if (!($dyingPlayerEntity instanceof DyingPlayerEntity)) return;
        $event->setCancelled();

        //瀕死状態のエンティティとタップした人の確認

        //持ち主がオフライン
        $dyingPlayerEntityOwner = $dyingPlayerEntity->getOwner();
        if (!$dyingPlayerEntityOwner->isOnline()) return;

        //進行中のゲームに参加しているか
        $dyingPlayerEntityOwnerData = PlayerDataDAO::findByName($dyingPlayerEntityOwner->getName());
        $dyingPlayerEntityOwnerGameId = $dyingPlayerEntityOwnerData->getBelongGameId();
        $attackerData = PlayerDataDAO::findByName($attacker->getName());
        $attackerGameId = $attackerData->getBelongGameId();

        if (!$this->belongGameIsInProgress($attackerData)) return;
        if (!$this->belongGameIsInProgress($dyingPlayerEntityOwnerData)) return;

        //同じゲームに属しているか
        if (!$attackerGameId->equals($dyingPlayerEntityOwnerGameId)) return;

        //生存者しか投票できない
        $attackerStatus = PlayerStatusStorage::findByName($attacker->getName());
        if ($attackerStatus === null) return;
        if (!$attackerStatus->getState()->equals(PlayerState::Alive())) return;

        $attacker->sendForm(new ConfirmVoteForm($dyingPlayerEntity));
    }

    public function onDyingPlayerEntityDeath(EntityDeathEvent $event) {
        $event->setDrops([]);
        $entity = $event->getEntity();

        if (!($entity instanceof DyingPlayerEntity)) return;
        if ($entity->isRescued()) return;

        //本当の死
        $owner = $entity->getOwner();
        if (!$owner->isOnline()) return;

        $ownerData = PlayerDataDAO::findByName($owner->getName());
        $belongGameId = $ownerData->getBelongGameId();
        if ($belongGameId === null) return;
        $owner->setGamemode(Player::SPECTATOR);
        $owner->setImmobile(false);

        $game = GameStorage::findById($belongGameId);
        UpdatePlayerStateService::execute($owner->getName(), PlayerState::Dead(), $this->scheduler);

        $cadaverEntity = new CadaverEntity($owner->getLevel(), $owner);
        $cadaverEntity->spawnToAll();
    }

    public function onTapCadaverEntity(EntityDamageByEntityEvent $event) {
        $cadaverEntity = $event->getEntity();
        $attacker = $event->getDamager();
        if (!($attacker instanceof Player)) return;
        if (!($cadaverEntity instanceof CadaverEntity)) return;
        $event->setCancelled();
    }

    public function onTapBloodPackEntity(EntityDamageByEntityEvent $event) {
        $bloodPackEntity = $event->getEntity();
        $attacker = $event->getDamager();
        if (!($attacker instanceof Player)) return;
        if (!($bloodPackEntity instanceof BloodPackEntity)) return;
        $event->setCancelled();
        $bloodPackEntity->onAttackedByPlayer($attacker);
    }

    public function onTapItemOnMapEntity(EntityDamageByEntityEvent $event): void {
        $entity = $event->getEntity();
        $attacker = $event->getDamager();
        if (!($attacker instanceof Player)) return;
        if (($entity instanceof ItemOnMapEntity) or ($entity instanceof ItemGunEntity)) {
            $event->setCancelled();
            $entity->onAttackedByPlayer($attacker);
        }
    }

    private function belongGameIsInProgress(PlayerData $playerData): bool {
        $gameId = $playerData->getBelongGameId();

        $game = GameStorage::findById($gameId);
        if ($game === null) return false;
        if (!$game->isStarted()) return false;
        if ($game->isFinished()) return false;

        return true;
    }

    //TODO : イベントいらないかも
    public function onUpdatedGameData(UpdatedGameDataEvent $event) {
        $game = GameStorage::findById($event->getGameId());
        if ($game === null) return;

        foreach ($game->getPlayerNameList() as $playerName) {
            $player = Server::getInstance()->getPlayer($playerName);
            GameSettingsScoreboard::update($player);
        }
    }

    public function onChat(PlayerChatEvent $event) {
        $sender = $event->getPlayer();
        if (substr($event->getMessage(), 0, 1) === "!") {
            SendTeamChatPMMPService::execute($sender, $event->getMessage());
        }

        $senderStatus = PlayerStatusStorage::findByName($sender->getName());
        if ($senderStatus === null) return;

        $game = GameStorage::findById($senderStatus->getBelongGameId());
        if ($game === null) return;

        foreach ($game->getPlayerNameList() as $name) {
            $gamePlayer = Server::getInstance()->getPlayer($name);
            if ($gamePlayer === null) continue;
            if (!$gamePlayer->isOnline()) continue;

            $gamePlayer->sendMessage("[{$sender->getName()}]" . $event->getMessage());
        }
    }
}