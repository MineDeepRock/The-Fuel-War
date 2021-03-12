<?php


namespace the_fuel_ward\pmmp\services;


use bossbar_system\BossBar;
use the_fuel_ward\pmmp\scoreboards\LobbyScoreboard;
use the_fuel_ward\services\QuitGameService;
use pocketmine\Player;
use pocketmine\Server;

class QuitGamePMMPService
{
    static function execute(Player $player): void {
        if (!$player->isOnline()) return;

        $result = QuitGameService::execute($player->getName());
        if ($result) {
            $player->sendMessage("ゲームから抜けました");

            $level = Server::getInstance()->getLevelByName("lobby");
            $player->teleport($level->getSpawnLocation());
            $player->getInventory()->setContents([]);
            $bossBars = BossBar::getBelongings($player);
            foreach ($bossBars as $bossBar) $bossBar->remove();
            LobbyScoreboard::send($player);
        } else {
            $player->sendMessage("ゲームから抜けることができませんでした");
        }
    }
}