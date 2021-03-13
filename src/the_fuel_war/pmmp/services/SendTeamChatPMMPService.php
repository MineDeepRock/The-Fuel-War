<?php


namespace the_fuel_war\pmmp\services;


use the_fuel_war\storages\PlayerStatusStorage;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class SendTeamChatPMMPService
{
    static function execute(Player $sender, string $text): void {
        $senderStatus = PlayerStatusStorage::findByName($sender->getName());
        if ($senderStatus === null) return;

        foreach (PlayerStatusStorage::findByBelongTankId($senderStatus->getBelongGameId(), $senderStatus->getBelongTankId()) as $name) {
            $player = Server::getInstance()->getPlayer($name);
            if ($player === null) continue;

            $player->sendMessage(TextFormat::BOLD . "チームチャット:[{$sender->getName()}]" . $text);
        }
    }
}