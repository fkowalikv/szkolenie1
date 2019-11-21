<?php

use Gaufrette\Adapter\Zip;
use Gaufrette\Filesystem;
use Loyalty\Command\AddPoints;
use Loyalty\Command\Handler\AddPointsHandler;
use Loyalty\Command\Handler\RegisterWalletHandler;
use Loyalty\Command\RegisterWallet;
use Loyalty\Repository\Database;
use Loyalty\Repository\Gaufrette;
use Loyalty\Repository\InMemory;
use Loyalty\Wallet;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

require __DIR__ . '/vendor/autoload.php';

$db = new PDO("mysql:host=192.168.33.10;dbname=szkolenie", "root", "shopware");

// Konfig
//$repository = new InMemory(); // new PDOWallets, new DoctrineWallet
//$repository = new \Loyalty\Repository\File(__DIR__ . '/var/');
//$repository = new Gaufrette(
//    new Filesystem(
//        new Zip('var/wallets.zip')
//    )
//);
$repository = new Database($db);

$bus = new MessageBus([
    new HandleMessageMiddleware(new HandlersLocator([
        RegisterWallet::class => [new RegisterWalletHandler($repository)],
        AddPoints::class => [new AddPointsHandler($repository)]
    ]))
]);

// Aplikacja, to żyje w kontrolerze webowym
for ($i = 0; $i < 1000; $i++) {
    $walletId = Uuid::uuid4();
    $bus->dispatch(new RegisterWallet($walletId));

    $bus->dispatch(new AddPoints($walletId, random_int(1, 1000)));
}

// :)
//dump($repository);
