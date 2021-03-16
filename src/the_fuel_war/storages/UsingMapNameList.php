<?php


namespace the_fuel_war\storages;


class UsingMapNameList
{
    static array $names = [];

    static function getAll(): array {
        return self::$names;
    }

    static function isExist(string $name): bool {
        return in_array($name, self::$names);
    }

    static function add(string $name): void {
        if (!self::isExist($name)) {
            self::$names[] = $name;
        }
    }

    static function remove(string $name): void {
        if (self::isExist($name)) {
            $index = array_search($name, self::$names);
            unset(self::$names[$index]);
        }
    }
}