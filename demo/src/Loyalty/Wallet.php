<?php

namespace Loyalty;

use Loyalty\Events\Event;
use Loyalty\Events\PointsAdded;
use Loyalty\Events\WalletRegistered;
use Ramsey\Uuid\UuidInterface;
use Webmozart\Assert\Assert;

class Wallet
{
    const POINTS_EXPIRE_AFTER_SECONDS = 30;

    /**
     * @var UuidInterface
     */
    private $id;

    /**
     * @var int
     */
    private $balance = 0;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var array
     */
    private $events;

    /**
     * @var int
     */
    private $version = 0;

    /**
     * Wallet constructor.
     */
    private function __construct()
    {
    }

    public function handle(Event $event) {
        switch(get_class($event)) {
            case WalletRegistered::class:
                /** @var WalletRegistered $event */
                $this->id = $event->getWalletId();
                $this->createdAt = new \DateTimeImmutable();
                break;
            case PointsAdded::class:
                /** @var PointsAdded $event */
//                if (strtotime($event->getCreatedAt()->getTimestamp()) > time() - self::POINTS_EXPIRE_AFTER_SECONDS) {
                    $this->balance += $event->getPoints();
//                }
                break;
        }
    }

    public static function fromHistory(array $events) {
        $wallet = new self();

        foreach($events as $event) {
            /** @var Event $event */
            $wallet->handle($event);
            $wallet->version = $event->getVersion();
        }

        return $wallet;
    }

    public static function createFor(UuidInterface $id) {
        $wallet = new self();

        $event = new WalletRegistered($id);

        $wallet->handle($event);
        $wallet->recordThat($event);

        return $wallet;
    }

    public function addPoints(int $points, string $reason)
    {
        // Guard
        Assert::greaterThan($points, 0);

        $event = new PointsAdded($this->getId(), $points);

        $this->handle($event);
        $this->recordThat($event);
    }

    public function usePoints(int $points, string $reason)
    {

    }

    /**
     * @return UuidInterface
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function recordThat(Event $event) {
        $event->setVersion(++$this->version);

        $this->events[] = $event;
    }

    /**
     * @return array
     */
    public function fetchEvents(): array
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }
}