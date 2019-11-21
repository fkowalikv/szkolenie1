<?php


namespace Loyalty\Command;


use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class RegisterWallet
{
    /**
     * @var string
     */
    private $walletId;

    /**
     * RegisterWallet constructor.
     * @param UuidInterface $walletId
     */
    public function __construct(UuidInterface $walletId)
    {
        $this->walletId = $walletId->toString();
    }

    public function getWalletId(): UuidInterface
    {
        return Uuid::fromString($this->walletId);
    }
}