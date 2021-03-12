<?php


namespace the_fuel_ward\types;


class FuelTankId
{
    private string $id;

    private function __construct(string $id) {
        $this->id = $id;
    }

    static function asNew(): self {
        return new FuelTankId(uniqid());
    }

    public function __toString() {
        return $this->id;
    }

    public function equals(?FuelTankId $GameId): bool {
        if ($GameId === null)
            return false;

        return $this->id === $GameId->id;
    }
}