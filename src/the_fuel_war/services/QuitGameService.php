<?php


namespace the_fuel_war\services;


use pocketmine\Server;
use the_fuel_war\dao\PlayerDataDAO;
use the_fuel_war\data\PlayerData;
use the_fuel_war\pmmp\events\UpdatedGameDataEvent;
use the_fuel_war\pmmp\scoreboards\OnGameScoreboard;
use the_fuel_war\pmmp\services\KillFuelTankEntityPMMPService;
use the_fuel_war\storages\GameStorage;
use the_fuel_war\storages\PlayerStatusStorage;

class QuitGameService
{
    static function execute(string $playerName): bool {
        $playerData = PlayerDataDAO::findByName($playerName);
        $belongGameId = $playerData->getBelongGameId();
        if ($belongGameId === null) return false;

        $tankId = PlayerStatusStorage::findByName($playerName)->getBelongTankId();

        PlayerDataDAO::update(new PlayerData($playerName));
        PlayerStatusStorage::delete($playerName);

        $game = GameStorage::findById($belongGameId);

        if ($game !== null) {
            $game->removePlayer($playerName);

            if ($game->isStarted()) {
                $teammates = PlayerStatusStorage::findByBelongTankId($game->getGameId(), $tankId);
                if (count($teammates) === 0) {
                    $level = Server::getInstance()->getLevelByName($game->getMap()->getLevelName());
                    KillFuelTankEntityPMMPService::execute($level, $tankId);
                }

                $gamePlayers = [];
                foreach ($game->getPlayerNameList() as $name) {
                    $gamePlayer = Server::getInstance()->getPlayer($name);
                    if ($gamePlayer === null) continue;
                    $gamePlayers[] = $gamePlayer;
                }
                OnGameScoreboard::update($gamePlayers, $game);
            } else {
                $event = new UpdatedGameDataEvent($belongGameId);
                $event->call();
            }
        }

        return true;
    }
}