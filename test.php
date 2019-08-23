<?php

require_once __DIR__.'/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Maths\Maths;

$obj = new Maths();
echo $obj->add(25, 25);

/*class amq
{
    public function publishMessage()
    {

    }

    public function subcribeMessage()
    {

    }

    public function testadd()
    {
        $obj = new Maths();
        return $obj->add(25, 25);
    }
}*/