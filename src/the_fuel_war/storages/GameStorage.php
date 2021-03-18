<?php

namespace the_fuel_war\storages;


use the_fuel_war\models\Game;
use the_fuel_war\types\GameId;
use the_fuel_war\types\GameType;

class GameStorage
{
    /**
     * @var Game[]
     */
    static array $games = [];

    static function add(Game $game): bool {
        if (self::findById($game->getGameId()) !== null) return false;

        self::$games[] = $game;
        return true;
    }

    static function delete(GameId $gameId): void {

        foreach (self::$games as $key => $game) {
            if ($game->getGameId()->equals($gameId)) unset(self::$games[$key]);
        }

        self::$games = array_values(self::$games);
    }

    static function deleteAll(): void {
        self::$games = [];
    }

    static function findById(?GameId $gameId): ?Game {
        if ($gameId === null) return null;

        foreach (self::$games as $game) {
            if ($game->getGameId()->equals($gameId)) {
                return $game;
            }
        }

        return null;
    }

    static function findOwnerName(string $name): ?Game {
        if ($name === null) return null;

        foreach (self::$games as $game) {
            if ($game->getGameOwnerName() === $name) {
                return $game;
            }
        }

        return null;
    }

    /**
     * @return Game[]
     */
    static function getAll(): array {
        return self::$games;
    }

    /**
     * @param GameType $gameType
     * @return Game[]
     */
    static function findByGameType(GameType $gameType): array {
        $result = [];
        foreach (self::getAll() as $game) {
            if ($game->getGameType()->equals($gameType)) {
                $result[] = $game;
            }
        }

        return $result;
    }
}