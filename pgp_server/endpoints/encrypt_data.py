from utils.consumer import Consumer


class EncryptDataConsumer(Consumer):

        def __init__(self):
            super().__init__('encrypt_data')

        def callback(self, ch, method, properties, body):
            print(f"[✅] Received #{ body }")

            self.response('Encrypted data')
            return
