from utils.consumer import Consumer
from utils.pgp_key import GPGKeyManager


class PublicKeyConsumer(Consumer):

    def __init__(self):
        super().__init__('public_key')

    def callback(self, ch, method, properties, body):
        print(f"[✅] Received request for public key")

        public, private = GPGKeyManager().get_keys()

        print(f"[✅] Sending public key")
        self.response(public)
        return
