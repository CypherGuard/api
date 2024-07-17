from utils.consumer import Consumer

class DecryptDataConsumer(Consumer):

    def __init__(self):
        super().__init__('decrypt_data')

    def callback(self, ch, method, properties, body):
        print(f"[✅] Received #{ body }")
        return
