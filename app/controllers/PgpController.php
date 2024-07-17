<?php

namespace App\Controllers;

use App\Utils\AMQPClient;
use Leaf\Http\Request;

class PgpController extends Controller
{
    public function __construct() {
        parent::__construct();
    }

    public function get_public_key()
    {
        try {
            $amqpClient = new AMQPClient();
            $queue_name = 'public_key';
            $message_body = ''; // Assuming no specific message body is needed
            $response = $amqpClient->sendMessageAndWaitForResponse($queue_name, $message_body);
            $amqpClient->close();

            // Return the response as JSON
            return response()->json(['public_key' => $response]);
        } catch (Exception $e) {
            // Handle exceptions and return an error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
