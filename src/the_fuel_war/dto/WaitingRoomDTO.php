<?php


namespace the_fuel_war\dto;


use the_fuel_war\data\WaitingRoom;
use pocketmine\math\Vector3;

class WaitingRoomDTO
{
    static function decode(array $json): WaitingRoom {
        $vector = new Vector3($json["x"], $json["y"], $json["z"]);

        return new WaitingRoom($vector, true);
    }

    static function encode(WaitingRoom $waitingRoom): array {
        return [
            "x" => $waitingRoom->getVector()->getX(),
            "y" => $waitingRoom->getVector()->getY(),
            "z" => $waitingRoom->getVector()->getZ(),
        ];
    }
}