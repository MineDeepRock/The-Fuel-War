<?php


namespace the_fuel_ward\pmmp;


use pocketmine\inventory\Inventory;
use pocketmine\Player;

class PlayerInventoryContentsStorage
{
    /**
     * @var array[]
     */
    static private array $inventories;

    static function save(string $playerName, array $contents): void {
        self::$inventories[$playerName] = $contents;
    }

    static function delete(string $playerName): void {
        if (array_key_exists($playerName, self::$inventories)) {
            unset(self::$inventories[$playerName]);
        }
    }

    static function get(string $playerName): ?array {
        if (array_key_exists($playerName, self::$inventories)) {
            $inventory = self::$inventories[$playerName];
            unset(self::$inventories[$playerName]);
            return $inventory;
        }

        return null;
    }
}