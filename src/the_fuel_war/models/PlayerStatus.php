<?php


namespace the_fuel_war\models;


use the_fuel_war\utilities\TransformTimer;
use the_fuel_war\types\FuelTankId;
use the_fuel_war\types\GameId;
use the_fuel_war\types\PlayerState;
use pocketmine\scheduler\TaskScheduler;

//ゲーム中にしか使わない値を持つ
class PlayerStatus
{
    private string $name;
    private GameId $belongGameId;
    private ?FuelTankId $belongTankId;

    private PlayerState $state;

    private int $bloodTank;
    private TransformTimer $transformTimer;

    public function __construct(string $name, GameId $belongGameId, ?FuelTankId $belongTankId, PlayerState $state, TaskScheduler $taskScheduler) {
        $this->name = $name;
        $this->belongGameId = $belongGameId;
        $this->belongTankId = $belongTankId;
        $this->state = $state;
        $this->bloodTank = 0;

        $this->transformTimer = new TransformTimer($name, $taskScheduler);
    }

    public function addBlood(): bool {
        if ($this->bloodTank >= 5) return false;
        $this->bloodTank++;

        return true;
    }

    public function resetBlood(): void {
        $this->bloodTank = 0;
    }

    public function canTransform(): bool {
        return $this->bloodTank === 5;
    }

    public function startTransformTimer(): bool {
        $this->bloodTank = 0;
        $this->transformTimer->start();

        return true;
    }

    public function stopTransformTimer(): bool {
        $this->transformTimer->stop();

        return true;
    }

    public function nowTransforming(): bool {
        return $this->transformTimer->isProgress();
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return GameId
     */
    public function getBelongGameId(): GameId {
        return $this->belongGameId;
    }

    /**
     * @return PlayerState
     */
    public function getState(): PlayerState {
        return $this->state;
    }

    /**
     * @return int
     */
    public function getBloodTank(): int {
        return $this->bloodTank;
    }

    /**
     * @return FuelTankId|null
     */
    public function getBelongTankId(): ?FuelTankId {
        return $this->belongTankId;
    }
}