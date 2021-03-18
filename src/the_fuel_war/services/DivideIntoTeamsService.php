<?php


namespace the_fuel_war\services;


use the_fuel_war\models\PlayerStatus;
use the_fuel_war\storages\PlayerStatusStorage;
use the_fuel_war\types\GameId;
use the_fuel_war\storages\GameStorage;
use pocketmine\scheduler\TaskScheduler;
use the_fuel_war\types\GameType;

class DivideIntoTeamsService
{
    static function execute(GameId $gameId, TaskScheduler $scheduler): bool {
        $game = GameStorage::findById($gameId);
        if ($game === null) return false;

        $fuelTanks = array_values($game->getFuelTanks());
        $playerNames = array_values($game->getPlayerNameList());

        //ソロの場合
        if ($game->getGameType()->equals(GameType::Solo())) {
            if (count($playerNames) > count($fuelTanks)) return false;
            foreach ($playerNames as $key => $playerName) {
                $playerStatus = PlayerStatusStorage::findByName($playerName);
                if ($playerStatus === null) ;//TODO:エラー

                $tankId = $fuelTanks[$key]->getTankId();
                $newPlayerStatus = new PlayerStatus($playerStatus->getName(), $playerStatus->getBelongGameId(), $tankId, $playerStatus->getState(), $scheduler);
                PlayerStatusStorage::update($newPlayerStatus);
            }
            //TODO:使用していないタンクを削除する

        } else {
            //指定なしの場合
            if ($game->getGameType()->equals(GameType::Unspecified())) {
                if (!is_int(count($playerNames) / count($fuelTanks))) return false;
                $teamPlayersCount = count($playerNames) / count($fuelTanks);

            //ソロ以上で指定がある場合
            } else {
                $teamPlayersCount = intval(strval($game->getGameType()));
                if (!is_int(count($playerNames) / $teamPlayersCount)) return false;
            }

            foreach ($fuelTanks as $fuelTank) {
                $playersIndexList = array_rand($playerNames, $teamPlayersCount);

                foreach ($playersIndexList as $index) {
                    $playerName = $playerNames[$index];
                    unset($playerNames[$index]);
                    $playerStatus = PlayerStatusStorage::findByName($playerName);
                    if ($playerStatus === null) ;//TODO:エラー

                    $newPlayerStatus = new PlayerStatus($playerStatus->getName(), $playerStatus->getBelongGameId(), $fuelTank->getTankId(), $playerStatus->getState(), $scheduler);
                    PlayerStatusStorage::update($newPlayerStatus);
                    //TODO:使用していないタンクを削除する
                }
            }
        }

        if (count($playerNames) > count($fuelTanks)) {

        } else {

        }

        return true;
    }
}