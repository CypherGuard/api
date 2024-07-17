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

        self.channel = self.create_channel(queue)

        self.channel.basic_consume(
            queue,
            self.callback,
            auto_ack=True,
        )

    def create_channel(self, queue):
        channel = self.connection.channel()
        channel.queue_declare(queue=queue)
        return channel

    def callback(ch, method, properties, body):
        print(f"[✅] Received #{ body }")

    def run(self):
        try:
            print(f"[❎] Waiting for messages on {self.queue}.")
            self.channel.start_consuming()
        except Exception as e:
            print(f"Error: #{e}")
            sys.exit(0)

