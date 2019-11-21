<?php

require __DIR__ . '/vendor/autoload.php';

use Loyalty\Command\AddPoints;
use Loyalty\Command\Handler\AddPointsHandler;
use Loyalty\Command\Handler\RegisterWalletHandler;
use Loyalty\Command\RegisterWallet;
use Loyalty\Repository\Database;
use Loyalty\Wallet;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;

$db = new PDO("mysql:host=192.168.33.10;dbname=szkolenie", "root", "shopware");

$repository = new Database($db);

$bus = new MessageBus([
    new HandleMessageMiddleware(new HandlersLocator([
        RegisterWallet::class => [new RegisterWalletHandler($repository)],
        AddPoints::class => [new AddPointsHandler($repository)]
    ]))
]);

$exchange = 'kowalik';
$queue = 'kowalik_queue';
$consumerTag = 'kowalik';

$connection = new AMQPStreamConnection(
    '63.35.163.222',
    '5672',
    'expondo',
    'expondo',
    '/'
);
$channel = $connection->channel();

$channel->queue_declare($queue, false, true, false, false);

$channel->exchange_declare($exchange, AMQPExchangeType::DIRECT, false, true, false);

$channel->queue_bind($queue, $exchange);

/**
 * @param \PhpAmqpLib\Message\AMQPMessage $message
 */
function process_message($message)
{
    global $bus;

    $json = json_decode($message->body, true);

    $uuid = Uuid::fromString($json['uuid']);
    $points = $json['points'];

    $bus->dispatch(new RegisterWallet($uuid));
    $bus->dispatch(new AddPoints($uuid, $points));

    $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);

    // Send a message with the string "quit" to cancel the consumer.
    if ($message->body === 'quit') {
        $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
    }
}

$channel->basic_consume($queue, $consumerTag, false, false, false, false, 'process_message');

/**
 * @param \PhpAmqpLib\Channel\AMQPChannel $channel
 * @param \PhpAmqpLib\Connection\AbstractConnection $connection
 */
function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}

register_shutdown_function('shutdown', $channel, $connection);

// Loop as long as the channel has callbacks registered
while ($channel ->is_consuming()) {
    $channel->wait();
}
