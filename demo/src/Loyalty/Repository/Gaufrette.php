<?php


namespace Loyalty\Repository;


use Gaufrette\Filesystem;
use Loyalty\Wallet;
use Loyalty\WalletRepository;
use Ramsey\Uuid\UuidInterface;

class Gaufrette implements WalletRepository
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * Gaufrette constructor.
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function get(UuidInterface $id): Wallet
    {
        return unserialize(
            $this->filesystem->read($id->toString() . '/wallet.data')
        );
    }

    public function save(Wallet $wallet): void
    {
        $this->filesystem->write(
            $wallet->getId() . '/wallet.data',
            serialize($wallet),
            true
        );
    }
}