<?php


namespace the_fuel_ward\pmmp\services;


use bossbar_system\BossBar;
use the_fuel_ward\pmmp\scoreboards\LobbyScoreboard;
use the_fuel_ward\pmmp\scoreboards\OnGameScoreboard;
use the_fuel_ward\storages\PlayerStatusStorage;
use the_fuel_ward\types\FuelTankId;
use the_fuel_ward\types\GameId;
use the_fuel_ward\storages\GameStorage;
use pocketmine\Server;

class FinishGamePMMPService
{
    //勝敗
    static function execute(GameId $gameId, ?FuelTankId $winFuelTankId = null): void {
        $game = GameStorage::findById($gameId);
        if ($game === null) return;

        if ($winFuelTankId == null) {
            foreach ($game->getFuelTanks() as $fuelTank) {
                if ($fuelTank->isFull()) {
                    $winFuelTankId = $fuelTank->getTankId();
                }
            }
        }

        //メッセージの送信+ロビーへ送還
        foreach (PlayerStatusStorage::getPlayers($gameId) as $status) {
            $player = Server::getInstance()->getPlayer($status->getName());
            if ($player === null) continue;
            if ($status->getBelongTankId() === null) continue;

            //TODO:勝利したチームの紹介
            if ($winFuelTankId === null) {
                $message = "引き分け";

            } else if ($status->getBelongTankId()->equals($winFuelTankId)) {
                $message = "勝利";

            } else {
                $message = "負け";

            }

            $player->sendMessage($message);
            //スコアボードを削除
            OnGameScoreboard::delete($player);

            //ボスバーを削除
            $bossBars = BossBar::getBelongings($player);
            foreach ($bossBars as $bossBar) $bossBar->remove();

            $level = Server::getInstance()->getLevelByName("lobby");
            $player->teleport($level->getSpawnLocation());
            $player->getInventory()->setContents([]);
            LobbyScoreboard::send($player);
        }
    }
}