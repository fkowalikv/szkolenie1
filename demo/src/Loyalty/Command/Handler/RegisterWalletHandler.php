<?php

namespace Loyalty\Command\Handler;

use Loyalty\Command\RegisterWallet;
use Loyalty\Repository\InMemory;
use Loyalty\Wallet;
use Loyalty\WalletRepository;

class RegisterWalletHandler
{
    /**
     * @var WalletRepository
     */
    private $repository;

    /**
     * RegisterWalletHandler constructor.
     * @param WalletRepository $repository
     */
    public function __construct(WalletRepository $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(RegisterWallet $command)
    {
        $wallet = Wallet::createFor($command->getWalletId());

        $this->repository->save($wallet);
    }
}