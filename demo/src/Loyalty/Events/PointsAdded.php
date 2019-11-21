<?php

namespace Loyalty\Events;

class PointsAdded extends Event
{
    /**
     * @var string
     */
    private $walletId;

    /**
     * @var int
     */
    private $points;

    /**
     * PointsAdded constructor.
     * @param string $walletId
     * @param int $points
     */
    public function __construct(string $walletId, int $points)
    {
        parent::__construct();

        $this->walletId = $walletId;
        $this->points = $points;
    }

    /**
     * @return string
     */
    public function getWalletId(): string
    {
        return $this->walletId;
    }

    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }
}