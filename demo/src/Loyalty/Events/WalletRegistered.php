<?php

namespace Loyalty\Events;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class WalletRegistered extends Event {
    /**
     * @var string
     */
    private $walletId;

    /**
     * WalletRegistered constructor.
     * @param UuidInterface $walletId
     */
    public function __construct(UuidInterface $walletId)
    {
        parent::__construct();

        $this->walletId = $walletId->toString();
    }

    public function getWalletId(): UuidInterface
    {
        return Uuid::fromString($this->walletId);
    }
}