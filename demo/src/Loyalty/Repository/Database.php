<?php

namespace Loyalty\Repository;

use DateTime;
use Loyalty\Wallet;
use Loyalty\WalletRepository;
use PDO;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Database implements WalletRepository {
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
        $sql = 'SELECT * FROM wallets WHERE id=:id';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id->toString()]);
        $result = $stmt->fetch();

        $wallet = Wallet::fromData(
            Uuid::fromString($result['id']),
            $result['balance'],
            $result['createdAt']
        );

        return $wallet;
    }

    public function save(Wallet $wallet): void
    {
        $sql = 'INSERT INTO wallets VALUES(' . '"' .
            $wallet->getId()->toString() . '","' .
            $wallet->getBalance() . '","' .
            $wallet->getCreatedAt()->format('Y-m-d H:i:s') . '"' .
            ') ON DUPLICATE KEY UPDATE balance=' . $wallet->getBalance();

//        dump($sql);

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    }
}