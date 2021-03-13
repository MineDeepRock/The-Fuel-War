<?php


namespace the_fuel_war\types;


class PlayerState
{
    private string $text;

    private function __construct(string $text) {
        $this->text = $text;
    }

    static function Alive(): PlayerState {
        return new self("Alive");
    }

    static function Dying(): PlayerState {
        return new self("Dying");
    }

    static function Dead(): PlayerState {
        return new self("Dead");
    }

    static function Escaped(): PlayerState {
        return new self("Escaped");
    }

    public function equals(?self $playerStateOnGame): bool {
        if ($playerStateOnGame === null)
            return false;

        return $this->text === $playerStateOnGame->text;
    }
}