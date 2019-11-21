<?php

namespace Loyalty\Repository;

use Loyalty\Events\Event;
use Loyalty\Events\PointsAdded;
use Loyalty\Events\WalletRegistered;
use Loyalty\Wallet;
use Loyalty\WalletRepository;
use PDO;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DatabaseES implements WalletRepository {
    /**
     * @var PDO
     */
    private $db;

    /**
     * Database constructor.
     * @param $db
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function get(UuidInterface $id): Wallet
    {
        $sql = 'SELECT * FROM wallet_events WHERE walletId=:id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id->toString()]);
        $dbEvents = $stmt->fetchAll();

        $events = [];

//        dump($dbEvents);

        foreach ($dbEvents as $dbEvent) {
            $walletId = $dbEvent['walletId'];
            $version = $dbEvent['version'];
            $class = $dbEvent['class'];
            $payload = json_decode($dbEvent['payload'], true);

//            dump($payload);

            switch ($class) {
                case 'Loyalty\Events\WalletRegistered':
                    $event = new WalletRegistered(Uuid::fromString($walletId));
                    $event->setVersion($version);
                    $events[] = $event;
                    break;
                case 'Loyalty\Events\PointsAdded':
                    $event = new PointsAdded($walletId, $payload['points']);
                    $event->setVersion($version);
                    $events[] = $event;
                    break;
            }
        }

        $wallet = Wallet::fromHistory($events);

        return $wallet;
    }

    public function save(Wallet $wallet): void
    {
        $events = $wallet->fetchEvents();

        foreach ($events as $event) {
            $payload = 0;

//            dump(get_class($event));

            switch(get_class($event)) {
                case WalletRegistered::class:
                    /** @var WalletRegistered $event */
                    $payload = 0;
                    break;
                case PointsAdded::class:
                    /** @var PointsAdded $event */
                    $payload = [
                        'points' => $event->getPoints()
                    ];
                    break;
            }

            /** @var Event $event */
            $sql = 'INSERT INTO wallet_events(walletId, createdAt, class, version, payload) VALUES(' . '"' .
                $event->getWalletId() . '","' .
                $event->getCreatedAt()->format('Y-m-d H:i:s') . '","' .
                str_replace('\\' ,'\\\\', get_class($event)) . '","' .
                $event->getVersion() . '",\'' .
                json_encode($payload) . '\'' .
                ')';

            dump($sql);

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
    }
}