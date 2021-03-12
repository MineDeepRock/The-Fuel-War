<?php


namespace the_fuel_ward\dto;


use the_fuel_ward\types\GameId;
use the_fuel_ward\data\PlayerData;

class PlayerDataDTO
{
    static function decode(array $json): PlayerData {
        $gameId = $json["belong_game_id"] === null ? null : new GameId($json["belong_game_id"]);
        return new PlayerData($json["name"], $gameId);
    }

    static function encode(PlayerData $playerData): array {
        $gameId = $playerData->getBelongGameId() === null ? null : strval($playerData->getBelongGameId());

        return [
            "name" => $playerData->getName(),
            "belong_game_id" => $gameId,
        ];
    }
}