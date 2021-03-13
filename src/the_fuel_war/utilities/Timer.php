<?php


namespace the_fuel_war\utilities;


use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\scheduler\TaskScheduler;

abstract class Timer
{
    private int $initialTime;
    private int $timeLeft;

    private TaskScheduler $scheduler;
    private TaskHandler $handler;

    public function __construct(int $initialTime, int $timeLeft, TaskScheduler $scheduler) {
        $this->timeLeft = $timeLeft;
        $this->initialTime = $initialTime;
        $this->scheduler = $scheduler;
    }

    public function start(): void {
        $this->handler = $this->scheduler->scheduleDelayedRepeatingTask(new ClosureTask(
            function (int $currentTick): void {
                $this->timeLeft += 1;
                $this->onUpdatedTimer();
                if ($this->timeLeft === $this->initialTime) {
                    $this->onFinishedTimer();
                    $this->handler->cancel();
                }
            }
        ), 20, 20);
    }

    abstract public function onUpdatedTimer(): void;
    abstract public function onStoppedTimer(): void;
    abstract public function onFinishedTimer(): void;

    public function stop(): void {
        if ($this->handler !== null) {
            if (!$this->handler->isCancelled()) {
                $this->onStoppedTimer();
                $this->handler->cancel();
            }
        }
    }

    /**
     * @return int
     */
    public function getInitialTime(): int {
        return $this->initialTime;
    }

    /**
     * @return int
     */
    public function getTimeLeft(): int {
        return $this->timeLeft;
    }
}