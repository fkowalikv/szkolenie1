<?php

namespace Loyalty\Repository;

use Loyalty\Wallet;
use Loyalty\WalletRepository;
use Ramsey\Uuid\UuidInterface;

class File implements WalletRepository
{
    private $path;

    /**
     * File constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    public function get(UuidInterface $id): Wallet
    {
        // if not exist;

        return unserialize(
            file_get_contents($this->path . '/wallet_' . $id->toString() . '.data')
        );
    }

    public function save(Wallet $wallet): void
    {
        file_put_contents(
            $this->path . '/wallet_' . $wallet->getId()->toString() . '.data',
            serialize($wallet)
        );
    }
}