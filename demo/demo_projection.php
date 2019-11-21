<?php

use Loyalty\Repository\DatabaseES;
use Ramsey\Uuid\Uuid;

require __DIR__ . '/vendor/autoload.php';

$db = new PDO("mysql:host=192.168.33.10;dbname=szkolenie", "root", "shopware");

$repository = new DatabaseES($db);

$walletId = Uuid::fromString('8901a724-31e8-4e21-80f3-4dbf6ad89ccb');

$sql = 'SELECT * FROM wallet_events WHERE walletId=:id';

$stmt = $db->prepare($sql);
$stmt->execute(['id' => $walletId->toString()]);
$events = $stmt->fetchAll();

$n = 0;
$partialEvents = [];

foreach($events as $key => $event) {
    $partialEvents[$n][] = $events[$key];

    if ($n == 2) {
        $n = 0;
    }
    else $n++;
}

//dump($partialEvents);

$sum = 0;

foreach($partialEvents as $event) {
    foreach ($event as $item) {
        $payload = json_decode($item['payload'], true);

        $sum += $payload['points'];
    }

    $sql = 'INSERT INTO read_wallets VALUES("' . $walletId . '",' . $sum . ', NOW())';
    $stmt = $db->prepare($sql);
    $stmt->execute();
}

