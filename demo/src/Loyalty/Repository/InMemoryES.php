<?php

namespace Loyalty\Repository;

use Loyalty\Events\Event;
use Loyalty\Wallet;
use Loyalty\WalletRepository;
use Ramsey\Uuid\UuidInterface;

class InMemoryES implements WalletRepository
{
    private $events = [];

    public function get(UuidInterface $id): Wallet
    {
        return Wallet::fromHistory(
            $this->events[$id->toString()]
        );
    }

    public function save(Wallet $wallet): void
    {
        /** @var Event $event */
        foreach ($wallet->fetchEvents() as $event) {
            $this->events[$wallet->getId()->toString()][$event->getVersion()] = $event;
        }
    }
}
