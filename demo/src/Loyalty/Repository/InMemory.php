<?php

namespace Loyalty\Repository;

use Loyalty\Wallet;
use Loyalty\WalletRepository;
use Ramsey\Uuid\UuidInterface;

class InMemory implements WalletRepository
{
    private $wallets = [];

    public function get(UuidInterface $id): Wallet
    {
        // if not exist;

        return $this->wallets[$id->toString()];
    }

    public function save(Wallet $wallet): void
    {
        $this->wallets[$wallet->getId()->toString()] = $wallet;
    }
}