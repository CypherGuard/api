import gnupg
import threading

class GPGKeyManager:
    _instance = None
    _lock = threading.Lock()

    def __new__(cls, *args, **kwargs):
        if not cls._instance:
            with cls._lock:
                if not cls._instance:
                    cls._instance = super(GPGKeyManager, cls).__new__(cls)
                    cls._instance.__initialized = False
        return cls._instance

    def __init__(self, name='John Doe', email='john.doe@example.com', passphrase='mysecurepassphrase'):
        if self.__initialized:
            return

        self.name = name
        self.email = email
        self.passphrase = passphrase

        self.gpg = gnupg.GPG()  # Use default GPG home directory
        self.public_key, self.private_key = self.generate_and_export_keys(self.name, self.email, self.passphrase)
        self.__initialized = True

    def generate_and_export_keys(self, name, email, passphrase):
        """
        Generates a GPG key pair and exports the public and private keys.

        :param name: Name associated with the key.
        :param email: Email associated with the key.
        :param passphrase: Passphrase for the private key.
        :return: Tuple containing public key and private key.
        """
        key = self.generate_key(name, email, passphrase)
        public_key, private_key = self.export_keys(key.fingerprint)
        return public_key, private_key

    def generate_key(self, name, email, passphrase):
        """
        Generates a GPG key pair (public and private keys).

        :param name: Name associated with the key.
        :param email: Email associated with the key.
        :param passphrase: Passphrase for the private key.
        :return: Key generation result.
        """
        input_data = self.gpg.gen_key_input(
            name_real=name,
            name_email=email,
            passphrase=passphrase
        )
        key = self.gpg.gen_key(input_data)
        if not key:
            raise ValueError("Error generating key.")
        return key

    def export_keys(self, fingerprint):
        """
        Exports the public and private keys for the given fingerprint.

        :param fingerprint: The fingerprint of the key to export.
        :return: Tuple containing public key and private key.
        """
        public_key = self.gpg.export_keys(fingerprint)
        private_key = self.gpg.export_keys(keyids=fingerprint, secret=True, passphrase=self.passphrase)
        return public_key, private_key

    def get_keys(self):
        """
        Returns the generated public and private keys.

        :return: Tuple containing public key and private key.
        """
        return self.public_key, self.private_key
