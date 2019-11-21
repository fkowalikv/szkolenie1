<?php

require __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use Ramsey\Uuid\Uuid;

$exchange = 'kowalik';
$queue = 'kowalik_queue';

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

for($i=0; $i<10000; $i++) {
    $selectedId = [
        'uuid' => Uuid::uuid4(),
        'points' => random_int(1,100)
    ];

    $messageBody = json_encode($selectedId);

    $message = new AMQPMessage($messageBody, array('content_type' => 'text/plain', 'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT));
    $channel->basic_publish($message, $exchange);
}




$channel->close();
$connection->close();
