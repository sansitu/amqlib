<?php

namespace amqlib;

include "amqlib.php";

$queue = 'test-queue';

$amqlib = new amqlib();
print_r($amqlib->subscribeMessage($queue));

