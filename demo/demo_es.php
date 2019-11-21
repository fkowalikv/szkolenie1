<?php

use Loyalty\Command\AddPoints;
use Loyalty\Command\Handler\AddPointsHandler;
use Loyalty\Command\Handler\RegisterWalletHandler;
use Loyalty\Command\RegisterWallet;
use Loyalty\Repository\DatabaseES;
use Loyalty\Wallet;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

require __DIR__ . '/vendor/autoload.php';

$db = new PDO("mysql:host=192.168.33.10;dbname=szkolenie", "root", "shopware");

$repository = new DatabaseES($db);

$bus = new MessageBus([
    new HandleMessageMiddleware(new HandlersLocator([
        RegisterWallet::class => [new RegisterWalletHandler($repository)],
        AddPoints::class => [new AddPointsHandler($repository)]
    ]))
]);

// Aplikacja, to żyje w kontrolerze webowym
//$walletId = Uuid::uuid4();
//$bus->dispatch(new RegisterWallet($walletId));

$walletId = Uuid::fromString('8901a724-31e8-4e21-80f3-4dbf6ad89ccb');
dump($repository->get($walletId)->getBalance());
//$bus->dispatch(new AddPoints($walletId, random_int(1, 1000)));

// :)
//dump($repository->get($walletId));
