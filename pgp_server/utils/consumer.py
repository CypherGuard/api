import os
import sys
import pika
from dotenv import load_dotenv

load_dotenv()

class Consumer:

    def __init__(self, queue: str):
        url = os.environ.get('CLOUDAMQP_URL', 'amqp://guest:guest@localhost:5672/%2f')
        self.queue = queue

        self.params = pika.URLParameters(url)
        self.connection = pika.BlockingConnection(self.params)
        print(f"[✅] Connection over {queue} channel established")

        self.channel = self.create_channel(queue, 'amq.direct')

        self.response_queue = queue + '_response'
        self.response_channel = self.create_channel(self.response_queue, 'amq.direct')

        self.channel.basic_consume(
            queue,
            self.callback,
            auto_ack=True,
        )

    def create_channel(self, queue, exchange):
        channel = self.connection.channel()
        channel.queue_declare(queue=queue)
        channel.queue_bind(exchange=exchange, queue=queue, routing_key=queue)
        return channel

    def callback(self, ch, method, properties, body):
        print(f"[✅] Received #{body}")

    def response(self, body):
        self.response_channel.basic_publish(
            exchange='amq.direct',
            routing_key=self.response_queue,
            body=body
        )
        return

    def run(self):
        try:
            print(f"[❎] Waiting for messages on {self.queue}.")
            self.channel.start_consuming()
        except Exception as e:
            print(f"Error: #{e}")
            sys.exit(0)
