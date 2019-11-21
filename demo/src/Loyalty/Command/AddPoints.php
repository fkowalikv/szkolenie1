<?php


namespace Loyalty\Command;


use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AddPoints
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
     * RegisterWallet constructor.
     * @param UuidInterface $walletId
     * @param int $points
     */
    public function __construct(UuidInterface $walletId, int $points)
    {
        $this->walletId = $walletId->toString();
        $this->points = $points;
    }

    public function getWalletId(): UuidInterface
    {
        return Uuid::fromString($this->walletId);
    }

    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }
}