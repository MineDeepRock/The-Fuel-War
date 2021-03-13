<?php


namespace the_fuel_war\pmmp\scoreboards;


use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_builder\Score;
use scoreboard_builder\Scoreboard;
use scoreboard_builder\ScoreboardSlot;
use scoreboard_builder\ScoreSortType;

class LobbyScoreboard extends Scoreboard
{

    static function create(Player $player): Scoreboard {

        //TODO:所持金とかの表示
        $scores = [
            new Score(TextFormat::RESET . "----------------------"),
            new Score(TextFormat::BOLD . TextFormat::YELLOW . "プレイヤー情報:"),
            new Score(TextFormat::BOLD . "> レベル:0"),
            new Score(TextFormat::BOLD . "> 所持金:10000"),
            new Score("----------------------"),
        ];
        return parent::__create(ScoreboardSlot::sideBar(), "Lobby", $scores, ScoreSortType::smallToLarge());
    }

    static function send(Player $player) {
        $scoreboard = self::create($player);
        parent::__send($player, $scoreboard);
    }

    static function update(Player $player) {
        $scoreboard = self::create($player);
        parent::__update($player, $scoreboard);
    }
}