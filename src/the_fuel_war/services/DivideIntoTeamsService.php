<?php


namespace the_fuel_ward\services;


use the_fuel_ward\models\PlayerStatus;
use the_fuel_ward\storages\PlayerStatusStorage;
use the_fuel_ward\types\GameId;
use the_fuel_ward\storages\GameStorage;
use pocketmine\scheduler\TaskScheduler;

class DivideIntoTeamsService
{
    static function execute(GameId $gameId, TaskScheduler $scheduler): bool {
        $game = GameStorage::findById($gameId);
        if ($game === null) return false;

        $fuelTanks = array_values($game->getFuelTanks());
        $playerNames = array_values($game->getPlayerNameList());

        if (count($playerNames) > count($fuelTanks)) {
            if (!is_int(count($playerNames) / count($fuelTanks))) return false;
            $teamPlayersCount = count($playerNames) / count($fuelTanks);

            foreach ($fuelTanks as $fuelTank) {
                $playersIndexList = array_rand($playerNames, $teamPlayersCount);

                foreach ($playersIndexList as $index) {
                    $playerName = $playerNames[$index];
                    unset($playerNames[$index]);
                    $playerStatus = PlayerStatusStorage::findByName($playerName);
                    if ($playerStatus === null) ;//TODO:エラー

                    $newPlayerStatus = new PlayerStatus($playerStatus->getName(), $playerStatus->getBelongGameId(), $fuelTank->getTankId(), $playerStatus->getState(), $scheduler);
                    PlayerStatusStorage::update($newPlayerStatus);
                }
            }
        } else {
            foreach ($playerNames as $key => $playerName) {
                $playerStatus = PlayerStatusStorage::findByName($playerName);
                if ($playerStatus === null) ;//TODO:エラー

                $tankId = $fuelTanks[$key]->getTankId();
                $newPlayerStatus = new PlayerStatus($playerStatus->getName(), $playerStatus->getBelongGameId(), $tankId, $playerStatus->getState(), $scheduler);
                PlayerStatusStorage::update($newPlayerStatus);
            }
        }

        return true;
    }
}