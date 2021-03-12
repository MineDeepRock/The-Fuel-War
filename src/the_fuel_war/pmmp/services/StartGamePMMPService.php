<?php


namespace the_fuel_ward\pmmp\services;


use the_fuel_ward\dao\PlayerDataDAO;
use the_fuel_ward\pmmp\items\TransformItem;
use the_fuel_ward\pmmp\scoreboards\GameSettingsScoreboard;
use the_fuel_ward\pmmp\scoreboards\OnGameScoreboard;
use the_fuel_ward\services\StartGameService;
use the_fuel_ward\storages\GameStorage;
use the_fuel_ward\storages\PlayerStatusStorage;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class StartGamePMMPService
{
    static function execute(Player $owner, TaskScheduler $taskScheduler): bool {
        $gameId = PlayerDataDAO::findByName($owner->getName())->getBelongGameId();
        if ($gameId === null) return false;

        $startResult = StartGameService::execute($owner->getName(), $gameId, $taskScheduler);
        if (!$startResult) return false;


        $game = GameStorage::findById($gameId);
        $map = $game->getMap();
        $level = Server::getInstance()->getLevelByName($map->getLevelName());

        SpawnItemPMMPService::execute($map);
        SpawnBloodPackPMMPService::execute($map);

        foreach ($game->getPlayerNameList() as $playerName) {
            //Tankの場所にテレポート
            $playerStatus = PlayerStatusStorage::findByName($playerName);
            if ($playerStatus === null) continue;//TODO:エラー
            $fuelTank = $game->getFuelTankById($playerStatus->getBelongTankId());
            if ($fuelTank === null) continue;//TODO:エラー

            $player = Server::getInstance()->getPlayer($playerName);
            $player->teleport($level->getSpawnLocation());
            $player->teleport($fuelTank->getVector());

            $player->getInventory()->setContents([]);

            //スコアボード
            GameSettingsScoreboard::delete($player);
            OnGameScoreboard::send($player, $game);

            //メッセージ
            $player->sendMessage(TextFormat::GREEN . "燃料を燃料タンクに集めましょう");

            $player->sendTitle(TextFormat::GREEN . "ゲームスタート！！！", "燃料を燃料タンクに集めましょう");

            //TODO:別のアイテムにする
            $player->getInventory()->addItem(new TransformItem());
        }

        return true;
    }
}