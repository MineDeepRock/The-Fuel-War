<?php


namespace the_fuel_ward\pmmp\services;


use the_fuel_ward\types\GameId;
use the_fuel_ward\pmmp\scoreboards\GameSettingsScoreboard;
use the_fuel_ward\pmmp\scoreboards\LobbyScoreboard;
use the_fuel_ward\services\JoinGameService;
use the_fuel_ward\storages\GameStorage;
use pocketmine\Player;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;

class JoinGamePMMPService
{
    static function execute(Player $player, GameId $gameId, TaskScheduler $scheduler): void {
        $result = JoinGameService::execute($gameId, $player->getName(), $scheduler);
        if ($result) {
            $player->sendMessage("ゲームに参加しました");
            LobbyScoreboard::delete($player);
            GameSettingsScoreboard::send($player);

            $game = GameStorage::findById($gameId);

            //TODO:待合室がロビー以外を考慮する
            $player->teleport($game->getWaitingRoom()->getVector());

            foreach ($game->getPlayerNameList() as $participantName) {
                $participant = Server::getInstance()->getPlayer($participantName);
                if ($participant->getName() === $player->getName()) continue;

                $participant->sendMessage($player->getName() . "がゲームに参加しました");
            }
        } else {
            $player->sendMessage("ゲームに参加できませんでした");
        }
    }
}