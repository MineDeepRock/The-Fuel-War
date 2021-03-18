<?php


namespace the_fuel_war\pmmp\services;


use bossbar_system\BossBar;
use the_fuel_war\pmmp\scoreboards\LobbyScoreboard;
use the_fuel_war\pmmp\scoreboards\OnGameScoreboard;
use the_fuel_war\storages\PlayerStatusStorage;
use the_fuel_war\types\FuelTankId;
use the_fuel_war\types\GameId;
use the_fuel_war\storages\GameStorage;
use pocketmine\Server;

class FinishGamePMMPService
{
    //勝敗
    static function execute(GameId $gameId, ?FuelTankId $winFuelTankId = null): void {
        $game = GameStorage::findById($gameId);
        if ($game === null) return;
        $winPlayers = [];

        if ($winFuelTankId === null) {
            foreach ($game->getFuelTanks() as $fuelTank) {
                if ($fuelTank->isFull()) {
                    $winFuelTankId = $fuelTank->getTankId();
                    $winPlayers = PlayerStatusStorage::findByBelongTankId($gameId, $fuelTank->getTankId());
                }
            }
        }

        $winPlayersAsString = "--勝利したチーム--";
        foreach ($winPlayers as $winPlayer) {
            $winPlayersAsString .= $winPlayer->getName() . "\n";
        }
        $winPlayersAsString .= "---------------";

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
            $player->sendMessage($winPlayersAsString);
            //スコアボードを削除
            OnGameScoreboard::delete($player);

            //ボスバーを削除
            $bossBars = BossBar::getBelongings($player);
            foreach ($bossBars as $bossBar) $bossBar->remove();

            $level = Server::getInstance()->getLevelByName("lobby");
            $player->teleport($level->getSpawnLocation());
            $player->getInventory()->setContents([]);
            LobbyScoreboard::send($player);

            CleanMapPMMPService::execute($game->getMap());
        }
    }
}