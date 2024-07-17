<?php
namespace App\Utils;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPProtocolChannelException;

class AMQPClient
{
    private AMQPStreamConnection $connection;
    private AMQPChannel $channel;
    private AMQPChannel$responseChannel;
    private string $exchange = 'amq.direct';

    public function __construct()
    {
        $url_str = getenv('CLOUDAMQP_URL') or exit("CLOUDAMQP_URL not set");
        $url = parse_url($url_str);

        $host = $url['host'];
        $port = isset($url['port']) ? $url['port'] : 5672;
        $user = $url['user'];
        $password = $url['pass'];
        $vhost = ($url['path'] == '/' || !isset($url['path'])) ? '/' : substr($url['path'], 1);

        // Optional parameters
        $insist = false;
        $login_method = "AMQPLAIN";
        $login_response = null;
        $locale = "en_US";
        $connection_timeout = 3;
        $read_write_timeout = 3;
        $context = null;

        // Create AMQPStreamConnection
        $this->connection = new AMQPStreamConnection(
            $host, $port, $user, $password, $vhost,
            $insist, $login_method, $login_response, $locale,
            $connection_timeout, $read_write_timeout, $context
        );

        $this->channel = $this->connection->channel();
        $this->channel->exchange_declare($this->exchange, 'direct', true, true, false);

        $this->responseChannel = $this->connection->channel();
        $this->responseChannel->exchange_declare($this->exchange, 'direct', true, true, false);
    }

    public function sendMessageAndWaitForResponse($queue_name, $message_body): string
    {
        $response_queue = $queue_name . '_response';

        $this->declareQueue($queue_name, $this->channel);
        $this->declareQueue($response_queue, $this->responseChannel);

        $this->publishMessage($queue_name, $message_body);

        return $this->waitForResponse($response_queue);
    }

    private function declareQueue(string $queue_name, AMQPChannel $channel): void
    {
        try {
            // Check if the queue already exists with passive=true
            $this->channel->queue_declare($queue_name, true, true, false, false);
        } catch (AMQPProtocolChannelException $e) {
            if ($e->getCode() == 404) {
                // Queue does not exist, declare it
                $this->channel->queue_declare($queue_name, false, true, false, false);
            } elseif ($e->getCode() == 406) {
                // Queue exists but with different parameters
                echo sprintf("Queue '%s' already exists with different parameters.\n", $queue_name);
                // Handle the error or log it
            }
        }
        $channel->queue_bind($queue_name, $this->exchange);
    }

    private function publishMessage($queue_name, $message_body): void
    {
        $msg = new AMQPMessage($message_body, [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT
        ]);
        $this->channel->basic_publish($msg, $this->exchange, $queue_name);
    }

    private function waitForResponse($response_queue): string
    {
        $retrieved_msg = null;
        while ($retrieved_msg == null) {
            $retrieved_msg = $this->channel->basic_get($response_queue);
        }

        $this->channel->basic_ack($retrieved_msg->getDeliveryTag());
        return $retrieved_msg->getBody();
    }

    public function close(): void
    {
        $this->channel->close();
        $this->responseChannel->close();
        $this->connection->close();
    }
}
