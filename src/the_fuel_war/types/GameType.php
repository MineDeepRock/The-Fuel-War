<?php


namespace the_fuel_war\types;


class GameType
{
    private string $text;

    private function __construct(string $text) {
        $this->text = $text;
    }

    static function Solo(): GameType {
        return new self("1");
    }

    static function TwoPlayers(): GameType {
        return new self("2");
    }

    static function Unspecified(): GameType {
        return new self("指定なし");
    }

    public function equals(?self $gameType): bool {
        if ($gameType === null)
            return false;

        return $this->text === $gameType->text;
    }

    static function fromString(string $text): ?GameType {
        switch ($text) {
            case self::Solo():
                return self::Solo();
            case self::TwoPlayers():
                return self::TwoPlayers();
            case self::Unspecified():
                return self::Unspecified();
        }

        return null;
    }

    public function __toString() {
        return $this->text;
    }

}