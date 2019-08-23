<?php
namespace amqlib;

chdir(dirname(__DIR__));

require_once('vendor/autoload.php');
include 'config/config.php';

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Maths\Maths;

class amqlib
{
    private $passive = false;
    private $durable = true;
    private $exclusive = false;
    private $autoDelete = false;

    private $prefetchSize = null;
    private $prefetchCount = 1;
    private $global = null;

    private $consumerTag = '';
    private $noLocal = false;
    private $noAck = false;
    private $noWait = false;

    public function __construct()
    {
    }

    public function publishMessage($routeKeyName, $content)
    {
        $pushData = "routeKeyName=".$routeKeyName."&content=".$content;

        $authorization = API_USERNAME .':'. API_PASSWORD;

        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
            'Authorization: Basic '. base64_encode($authorization)
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => API_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $pushData
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
    }

    /**
     * Process incoming request to generate pdf invoices and send them through 
     * email.
     */ 
    public function subscribeMessage($queue)
    {
        $connection = new AMQPConnection(AMQP_URL, AMQP_PORT, AMQP_USERNAME, AMQP_PASSWORD, AMQP_VHOST);
        $channel = $connection->channel();
        
        $channel->queue_declare($queue, $this->passive, $this->durable, $this->exclusive, $this->autoDelete);
            
        $channel->basic_qos($this->prefetchSize, $this->prefetchCount, $this->global);

        $channel->basic_consume($queue, $this->consumerTag, $this->noLocal, $this->noAck, $this->exclusive, $this->noWait, array($this, 'process'));
            
        while(count($channel->callbacks)) {
            $channel->wait();
        }
        
        $channel->close();
        $connection->close();
    }
    
    /**
     * process received request
     * 
     * @param AMQPMessage $msg
     */ 
    public function process(AMQPMessage $msg)
    {
        $response = $msg->body;
        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
        print($response);
    }
}
