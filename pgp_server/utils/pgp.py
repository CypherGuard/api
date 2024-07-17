import gnupg

class GPGEncryption:
    def __init__(self, gpg_home='~/.gnupg'):
        self.gpg = gnupg.GPG(gnupghome=gpg_home)

    def import_key(self, key_data):
        """
        Imports a GPG key.

        :param key_data: Key data to import (public or private key).
        :return: Import result.
        """
        import_result = self.gpg.import_keys(key_data)
        if import_result.count == 0:
            raise ValueError("Error importing key.")
        return import_result

    def encrypt_text(self, text, public_key):
        """
        Encrypts text using the provided public key.

        :param text: Text to encrypt.
        :param public_key: Public key for encryption.
        :return: Encrypted text.
        """
        self.import_key(public_key)
        public_key_fingerprint = self.gpg.list_keys()[0]['fingerprint']
        encrypted_data = self.gpg.encrypt(text, public_key_fingerprint)
        if encrypted_data.ok:
            return str(encrypted_data)
        else:
            raise ValueError("Error during encryption: " + encrypted_data.stderr)

    def decrypt_text(self, encrypted_text, private_key):
        """
        Decrypts encrypted text using the provided private key.

        :param encrypted_text: Encrypted text.
        :param private_key: Private key for decryption.
        :return: Decrypted text.
        """
        self.import_key(private_key)
        decrypted_data = self.gpg.decrypt(encrypted_text)
        if decrypted_data.ok:
            return str(decrypted_data)
        else:
            raise ValueError("Error during decryption: " + decrypted_data.stderr)
