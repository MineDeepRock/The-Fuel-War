<?php


namespace the_fuel_ward\pmmp\scoreboards;


use the_fuel_ward\dao\PlayerDataDAO;
use the_fuel_ward\storages\GameStorage;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use scoreboard_builder\Score;
use scoreboard_builder\Scoreboard;
use scoreboard_builder\ScoreboardSlot;
use scoreboard_builder\ScoreSortType;

class GameSettingsScoreboard extends Scoreboard
{

    static function create(Player $player): Scoreboard {
        $playerData = PlayerDataDAO::findByName($player->getName());
        $game = GameStorage::findById($playerData->getBelongGameId());
        if ($game === null) {
            //TODO:error
        }

        $playerCount = count($game->getPlayerNameList()) . "/" . $game->getMaxPlayers();

        $scores = [
            new Score(TextFormat::RESET . "----------------------"),
            new Score(TextFormat::BOLD . TextFormat::YELLOW . "ゲーム情報:"),
            new Score(TextFormat::BOLD . "> 主催者:{$game->getGameOwnerName()}"),
            new Score(TextFormat::BOLD . "> プレイヤー数:{$playerCount}"),
            new Score(TextFormat::BOLD . "> マップ:{$game->getMap()->getName()}"),
            new Score("----------------------"),
        ];
        return parent::__create(ScoreboardSlot::sideBar(), "the_fuel_ward", $scores, ScoreSortType::smallToLarge());
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