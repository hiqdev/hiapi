<?php

namespace hiapi\exceptions;

/**
 * Class NotProcessableException
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class NotProcessableException extends \RuntimeException
{
    /**
     * @var float
     */
    protected $progressionMultiplier = 1;
    /**
     * @var int
     */
    private $secondsBeforeRetry;

    /**
     * @var int
     */
    private $maxTries;

    public function maxTries(int $maxTries): NotProcessableException
    {
        $this->maxTries = $maxTries;

        return $this;
    }

    /**
     * @param $delaySeconds
     * @param float $progressionMultiplier
     * @return self
     */
    public function retryProgressively($delaySeconds, $progressionMultiplier): NotProcessableException
    {
        $this->secondsBeforeRetry = $delaySeconds;
        $this->progressionMultiplier = $progressionMultiplier;

        return $this;
    }

    /**
     * @return int
     */
    public function getSecondsBeforeRetry(): ?int
    {
        return $this->secondsBeforeRetry;
    }

    /**
     * @return int
     */
    public function getMaxTries(): ?int
    {
        return $this->maxTries;
    }

    /**
     * @return float
     */
    public function getProgressionMultiplier(): float
    {
        return $this->progressionMultiplier;
    }
}
