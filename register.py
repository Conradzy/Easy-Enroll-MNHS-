import sys
import bcrypt
import pyotp
import qrcode
import json
import os
import base64
from io import BytesIO

if len(sys.argv) != 4:
    print("Usage: register.py <username> <password> <email>")
    sys.exit(1)

username = sys.argv[1]
password = sys.argv[2]
email = sys.argv[3]

# Hash password
hashed_pw = bcrypt.hashpw(password.encode('utf-8'), bcrypt.gensalt())

# Generate Google Authenticator secret
secret = pyotp.random_base32()

# Generate provisioning URI
uri = pyotp.totp.TOTP(secret).provisioning_uri(
    name=email,
    issuer_name="Maragusan National High School Enrollment"
)

# Generate QR code for user to scan
img = qrcode.make(uri)

# Convert QR code to base64 for modal display
buffer = BytesIO()
img.save(buffer, format="PNG")
qr_base64 = base64.b64encode(buffer.getvalue()).decode('utf-8')

print(f"User '{username}' registered successfully.")
print("QR_BASE64:" + qr_base64)
print("Secret key (save securely):", secret)

# Load existing users
if os.path.exists("users.json"):
    with open("users.json", "r") as f:
        users = json.load(f)
else:
    users = {}

users[username] = {
    "password": hashed_pw.decode('utf-8'),
    "secret": secret,
    "email": email,
    "email_verified": True,
    "failed_attempts": 0,
    "locked_until": None
}

with open("users.json", "w") as f:
    json.dump(users, f)
