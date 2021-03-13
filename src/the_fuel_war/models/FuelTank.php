<?php


namespace the_fuel_war\models;


use the_fuel_war\pmmp\events\FuelTankBecameFullEvent;
use the_fuel_war\types\FuelTankId;
use the_fuel_war\types\GameId;
use pocketmine\math\Vector3;

class FuelTank
{
    private GameId $belongGameId;
    private FuelTankId $tankId;

    private int $capacity;
    private int $storageAmount;

    private int $fakeStorageAmount;

    private Vector3 $vector;

    public function __construct(GameId $gameId, int $capacity, Vector3 $vector) {
        $this->belongGameId = $gameId;
        $this->tankId = FuelTankId::asNew();
        $this->capacity = $capacity <= 1 ? 1 : $capacity;
        $this->storageAmount = 0;
        $this->fakeStorageAmount = 0;
        $this->vector = $vector;
    }

    //TODO:余った分を返すように
    public function addFuel(int $fuelCount, bool $isFake = false): bool {
        if ($this->storageAmount >= $this->capacity) return false;

        $this->storageAmount += $fuelCount;
        if ($isFake) $this->fakeStorageAmount += $fuelCount;

        if ($this->storageAmount >= $this->capacity) {
            $this->storageAmount = $this->capacity;

            if ($this->fakeStorageAmount === 0) {
                $event = new FuelTankBecameFullEvent($this->belongGameId, $this->tankId);
                $event->call();
            } else {
                $this->storageAmount -= $this->fakeStorageAmount;
            }
        }

        return true;
    }

    public function reduce(int $value): void {
        $this->storageAmount -= $value;

        if ($this->storageAmount < 0) $this->storageAmount = 0;
    }

    /**
     * @return FuelTankId
     */
    public function getTankId(): FuelTankId {
        return $this->tankId;
    }

    /**
     * @return int
     */
    public function getCapacity(): int {
        return $this->capacity;
    }

    /**
     * @return int
     */
    public function getStorageAmount(): int {
        return $this->storageAmount;
    }

    public function getAmountPercentage(): float {
        if ($this->storageAmount === 0) return 0;
        return $this->storageAmount / $this->capacity;
    }

    public function isFull(): bool {
        return $this->storageAmount === $this->capacity;
    }

    /**
     * @return Vector3
     */
    public function getVector(): Vector3 {
        return $this->vector;
    }
}