<?php


namespace the_fuel_war\storages;


use the_fuel_war\data\WaitingRoom;
use the_fuel_war\DataFolderPath;
use the_fuel_war\dto\WaitingRoomDTO;
use pocketmine\math\Vector3;

//TODO:ここでファイル操作を行っているのはおかしい
class WaitingRoomStorage
{
    /**
     * @var WaitingRoom[]
     */
    static array $waitingRoomList = [];

    //TODO:注意
    static function loadAllWaitingRooms(): void {
        $data = json_decode(file_get_contents(DataFolderPath::$waitingRoomListJson), true);
        foreach ($data as $vectorAsJson) {
            self::$waitingRoomList[] = WaitingRoomDTO::decode($vectorAsJson);
        }
    }

    //TODO:注意
    static function delete(WaitingRoom $target): void {
        $newWaitingRoomList = [];
        foreach (self::$waitingRoomList as $waitingRoom) {
            if (!$waitingRoom->getVector()->equals($target->getVector())) {
                $newWaitingRoomList[] = $waitingRoom;
            }
        }

        self::$waitingRoomList = $newWaitingRoomList;
        self::save();
    }

    //TODO:注意
    static private function save(): void {
        $json = [];
        foreach (self::$waitingRoomList as $waitingRoom) {
            $json[] = WaitingRoomDTO::encode($waitingRoom);
        }

        file_put_contents(DataFolderPath::$waitingRoomListJson, json_encode($json));
    }

    static function add(WaitingRoom $waitingRoom): bool {
        if (self::findByVector($waitingRoom->getVector()) !== null) return false;

        self::$waitingRoomList[] = $waitingRoom;
        self::save();
        return true;
    }


    static function getAll(): array {
        return self::$waitingRoomList;
    }

    static function findByVector(?Vector3 $vector3): ?WaitingRoom {
        if ($vector3 === null) return null;

        foreach (self::$waitingRoomList as $waitingRoom) {
            if ($waitingRoom->getVector()->equals($vector3)) {
                return $waitingRoom;
            }
        }

        return null;
    }

    static function useRandomAvailableRoom(): ?WaitingRoom {
        foreach (self::$waitingRoomList as $key => $waitingRoom) {
            if ($waitingRoom->isAvailable()) {
                self::$waitingRoomList[$key] = new WaitingRoom($waitingRoom->getVector(), false);
                return $waitingRoom;
            }
        }

        return null;
    }

    static function returnWaitingRoom(WaitingRoom $target): void {
        foreach (self::$waitingRoomList as $key => $waitingRoom) {
            if ($waitingRoom->getVector()->equals($target->getVector())) {
                self::$waitingRoomList[$key] = new WaitingRoom($waitingRoom->getVector(), true);
            }
        }
    }
}