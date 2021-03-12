<?php


namespace the_fuel_ward;


class DataFolderPath
{
    static string $map;
    static string $playerData;
    static string $skin;
    static string $geometry;
    static string $playerSkin;
    static string $waitingRoomListJson;

    static function init(string $dataPath, string $resourcePath) {
        self::$map = $dataPath . "maps/";
        if (!file_exists(self::$map)) mkdir(self::$map);

        self::$playerData = $dataPath . "player_data/";
        if (!file_exists(self::$playerData)) mkdir(self::$playerData);

        self::$skin = $resourcePath . "skin/";
        if (!file_exists(self::$skin)) mkdir(self::$skin);

        self::$geometry = $resourcePath . "geometry/";
        if (!file_exists(self::$geometry)) mkdir(self::$geometry);


        self::$playerSkin = $dataPath . "player_skin/";
        if (!file_exists(self::$playerSkin)) mkdir(self::$playerSkin);

        self::$waitingRoomListJson = $dataPath . "waiting_room/data.json";
		if (!file_exists($dataPath . "waiting_room/")) mkdir($dataPath . "waiting_room/");
        if (!file_exists(self::$waitingRoomListJson)) file_put_contents(self::$waitingRoomListJson, json_encode([]));
    }
}