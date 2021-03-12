<?php


namespace the_fuel_ward\storages;


use the_fuel_ward\models\Game;
use the_fuel_ward\models\PlayerStatus;
use the_fuel_ward\types\FuelTankId;
use the_fuel_ward\types\GameId;
use the_fuel_ward\types\PlayerState;

class PlayerStatusStorage
{
    /**
     * @var PlayerStatus[]
     */
    static array $playerStatusList = [];

    static function add(PlayerStatus $playerStatus): bool {
        if (self::findByName($playerStatus->getName()) !== null) return false;

        self::$playerStatusList[] = $playerStatus;
        return true;
    }

    static function delete(string $name): void {

        foreach (self::$playerStatusList as $key => $playerStatus) {
            if ($playerStatus->getName() === $name) unset(self::$playerStatusList[$key]);
        }

        self::$playerStatusList = array_values(self::$playerStatusList);
    }

    static function deleteAll(): void {
        self::$playerStatusList = [];
    }

    static function findByName(string $name): ?PlayerStatus {
        if ($name === null) return null;

        foreach (self::$playerStatusList as $playerStatus) {
            if ($playerStatus->getName() === $name) {
                return $playerStatus;
            }
        }

        return null;
    }

    static function update(PlayerStatus $playerStatus) {
        self::delete($playerStatus->getName());
        self::add($playerStatus);
    }

    /**
     * @return PlayerStatus[]
     */
    static function getAll(): array {
        return self::$playerStatusList;
    }

    /**
     * @param GameId $gameId
     * @return PlayerStatus[]
     */
    static function getPlayers(GameId $gameId): array {
        $result = [];

        foreach (self::$playerStatusList as $playerStatus) {
            if ($playerStatus->getBelongGameId()->equals($gameId)) {
                $result[] = $playerStatus;
            }
        }

        return $result;
    }

    /**
     * @param GameId $gameId
     * @param PlayerState $targetState
     * @return PlayerStatus[]
     */
    private static function getPlayersByState(GameId $gameId, PlayerState $targetState): array {
        $result = [];

        foreach (self::$playerStatusList as $playerStatus) {
            if ($playerStatus->getBelongGameId()->equals($gameId)) {
                if ($playerStatus->getState()->equals($targetState)) {
                    $result[] = $playerStatus;
                }
            }
        }

        return $result;
    }

    /**
     * @param GameId $gameId
     * @return PlayerStatus[]
     */
    static function getAlivePlayers(GameId $gameId): array {
        return self::getPlayersByState($gameId, PlayerState::Alive());
    }

    /**
     * @param GameId $gameId
     * @return PlayerStatus[]
     */
    static function getDyingPlayers(GameId $gameId): array {
        return self::getPlayersByState($gameId, PlayerState::Dying());

    }

    /**
     * @param GameId $gameId
     * @return PlayerStatus[]
     */
    static function getDeadPlayers(GameId $gameId): array {
        return self::getPlayersByState($gameId, PlayerState::Dead());
    }

    /**
     * @param GameId $gameId
     * @return PlayerStatus[]
     */
    static function getEscapedPlayers(GameId $gameId): array {
        return self::getPlayersByState($gameId, PlayerState::Escaped());
    }

    /**
     * @param GameId $gameId
     * @param FuelTankId $tankId
     * @return PlayerStatus[]
     */
    static function findByBelongTankId(GameId $gameId, ?FuelTankId $tankId): array {
        $result = [];
        if ($tankId === null) return $result;

        foreach (self::getPlayers($gameId) as $status) {
            if ($status->getBelongTankId() === null) continue;
            if ($status->getBelongTankId()->equals($tankId)) {
                $result[] = $status;
            }
        }

        return $result;
    }
}