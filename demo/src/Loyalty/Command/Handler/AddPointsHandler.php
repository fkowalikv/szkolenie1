<?php


namespace Loyalty\Command\Handler;


use Loyalty\Command\AddPoints;
use Loyalty\WalletRepository;

class AddPointsHandler
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

    public function __invoke(AddPoints $command)
    {
        $wallet = $this->repository->get($command->getWalletId());

        $wallet->addPoints($command->getPoints(), '');

        $this->repository->save($wallet);
    }
}