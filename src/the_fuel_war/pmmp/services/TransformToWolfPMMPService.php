<?php


namespace the_fuel_ward\pmmp\services;


use the_fuel_ward\DataFolderPath;
use the_fuel_ward\pmmp\PlayerInventoryContentsStorage;
use the_fuel_ward\pmmp\scoreboards\OnGameScoreboard;
use the_fuel_ward\storages\GameStorage;
use the_fuel_ward\storages\PlayerStatusStorage;
use pocketmine\entity\Attribute;
use pocketmine\entity\Skin;
use pocketmine\Player;
use pocketmine\Server;

class TransformToWolfPMMPService
{
    static function execute(Player $player): void {
        $playerStatus = PlayerStatusStorage::findByName($player->getName());
        if ($playerStatus === null) return;
        if ($playerStatus->canTransform()) {
            $game = GameStorage::findById($playerStatus->getBelongGameId());
            if ($game === null) return;//TODO:エラー

            $player->getAttributeMap()->getAttribute(Attribute::MOVEMENT_SPEED)->setValue(0.30);
            $player->setScale(1.3);
            $player->setSkin(new Skin("Standard_CustomSlim", file_get_contents(DataFolderPath::$skin . "wolf.skin")));
            $player->sendSkin();
            $playerStatus->startTransformTimer();

            //スコアボード更新
            $gamePlayers = [];
            foreach ($game->getPlayerNameList() as $name) {
                $gamePlayer = Server::getInstance()->getPlayer($name);
                if ($gamePlayer === null) return;
                $gamePlayers[] = $gamePlayer;
            }
            OnGameScoreboard::update($gamePlayers, $game);

            //インベントリを保存しクリア
            PlayerInventoryContentsStorage::save($player->getName(), $player->getInventory()->getContents());
            $player->getInventory()->setContents([]);
        }
    }
}