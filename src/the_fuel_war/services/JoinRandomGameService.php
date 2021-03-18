<?php


namespace the_fuel_war\services;


use pocketmine\scheduler\TaskScheduler;
use the_fuel_war\models\Game;
use the_fuel_war\storages\GameStorage;
use the_fuel_war\types\GameType;

class JoinRandomGameService
{
    static function execute(string $playerName, GameType $gameType, TaskScheduler $taskScheduler): bool {
        $games = [];
        foreach (GameStorage::findByGameType($gameType) as $soloGame) {
            if ($soloGame->canJoin($playerName)) $games[] = $soloGame;
        }
        if (count($games) <= 0) return false;

        usort($games, array('GameManager', 'sortByNumberOfPlayerCanJoin'));

        return JoinGameService::execute($games[0]->getGameId(), $playerName, $taskScheduler);
    }

    //1 2 3 4 5, few to many
    static private function sortByNumberOfPlayerCanJoin(Game $a, Game $b) {
        return $a->getMaxPlayers() - count($a->getPlayerNameList()) < $b->getMaxPlayers() - count($b->getPlayerNameList()) ? 1 : -1;
    }
}