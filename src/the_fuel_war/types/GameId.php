<?php


namespace the_fuel_ward\types;


class GameId
{
    private string $id;

    public function __construct(string $id) {
        $this->id = $id;
    }

    static function asNew(): self {
        return new GameId(uniqid());
    }
    public function __toString() {
        return $this->id;
    }

    public function equals(?GameId $GameId): bool {
        if ($GameId === null)
            return false;

        return $this->id === $GameId->id;
    }
}