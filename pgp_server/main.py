
import threading
from multiprocessing import Process

from endpoints.decrypt_data import DecryptDataConsumer
from endpoints.encrypt_data import EncryptDataConsumer
from utils.pgp_key import GPGKeyManager

if __name__ == '__main__':
    print("[✅] Init the PGP keys")
    GPGKeyManager()

    print("[✅] Starting the server")
    subscriber_list = []

    print("[✅] Add the endpoints to the subscriber list")
    subscriber_list.append(EncryptDataConsumer())
    subscriber_list.append(DecryptDataConsumer())

    process_list = []
    for sub in subscriber_list:
        print(f"[✅] Starting the endpoint {sub.queue}")
        process = Process(target=sub.run)
        process.start()
        process_list.append(process)

    for process in process_list:
        process.join()
