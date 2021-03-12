<?php


namespace the_fuel_ward\data;


//一時的なプレイヤーデータは含まない。
use the_fuel_ward\types\GameId;

class PlayerData
{
    private string $name;
    private ?GameId $belongGameId;//BelongGameIdは一時的だけど例外

    //TODO:level,money,etc,,
    public function __construct(string $name, ?GameId $belongGameId = null) {
        $this->name = $name;
        $this->belongGameId = $belongGameId;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return GameId|null
     */
    public function getBelongGameId(): ?GameId {
        return $this->belongGameId;
    }
}