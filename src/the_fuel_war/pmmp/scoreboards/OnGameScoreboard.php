<?php


namespace the_fuel_war\pmmp\scoreboards;


use the_fuel_war\models\Game;
use the_fuel_war\storages\PlayerStatusStorage;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_builder\Score;
use scoreboard_builder\Scoreboard;
use scoreboard_builder\ScoreboardSlot;
use scoreboard_builder\ScoreSortType;
use the_fuel_war\types\PlayerState;

class OnGameScoreboard extends Scoreboard
{

    static function create(Game $game): Scoreboard {
        $scores = [
            new Score(TextFormat::RESET . "----------------------"),
            new Score("チーム 1:")
        ];

        $index = 1;
        foreach ($game->getFuelTanks() as $fuelTank) {
            foreach (PlayerStatusStorage::findByBelongTankId($game->getGameId(), $fuelTank->getTankId()) as $status) {
                $playerName = $status->getName();
                if ($status->nowTransforming()) {
                    $bloodGaugeAsString = TextFormat::RED . "NOW TRANSFORMING";

                } else if ($status->canTransform()) {
                    $bloodGaugeAsString = str_repeat(TextFormat::RED . "■", $status->getBloodTank());

                } else if ($status->getBloodTank() === 0) {
                    $bloodGaugeAsString = str_repeat(TextFormat::WHITE . "■", 5);

                } else {
                    $bloodGaugeAsString = str_repeat(TextFormat::RED . "■", $status->getBloodTank());
                    $bloodGaugeAsString .= str_repeat(TextFormat::WHITE . "■", 5 - $status->getBloodTank());
                }

                $scores[] = new Score(">" . $playerName . "[{$status->getState()}]" . $bloodGaugeAsString);
            }

            $index++;
            $scores[] = new Score(TextFormat::RESET . "チーム {$index}:");
        }

        $scores[] = new Score(TextFormat::RESET . "----------------------");

        return parent::__create(ScoreboardSlot::sideBar(), "マップ:{$game->getMap()->getName()}", $scores, ScoreSortType::smallToLarge());
    }

    static function send(Player $player, Game $game) {
        $scoreboard = self::create($game);
        parent::__send($player, $scoreboard);
    }

    static function update(array $players, Game $game) {
        $scoreboard = self::create($game);

        foreach ($players as $player) {
            parent::__update($player, $scoreboard);
        }
    }
}