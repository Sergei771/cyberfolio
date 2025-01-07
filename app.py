
import re
import sys
# Fonction pour détecter le type de hachage
def detect_hash_type(hash_string):
    hash_types = {
        "MD5": r"^[a-fA-F0-9]{32}$",
        "SHA-1": r"^[a-fA-F0-9]{40}$",
        "SHA-256": r"^[a-fA-F0-9]{64}$",
        "SHA-512": r"^[a-fA-F0-9]{128}$",
        "bcrypt": r"^\$2[aby]?\$\d{2}\$.*",
        "Argon2": r"^\$argon2(id|i|d)\$.*",
    }
    for hash_type, pattern in hash_types.items():
        if re.match(pattern, hash_string):
            return hash_type
    return "Inconnu"

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Erreur : Aucun hash fourni.")
        sys.exit(1)

    hash_string = sys.argv[1]
    result = detect_hash_type(hash_string)
    print(result)