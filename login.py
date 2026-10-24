import sys
import json
import os
import bcrypt
import pyotp
import time


def main():
    # Expect: login.py <username> <password> <otp>
    if len(sys.argv) != 4:
        print("Usage: login.py <username> <password> <otp>")
        return

    username = sys.argv[1]
    password = sys.argv[2]
    otp = sys.argv[3]

    # Load users database
    if not os.path.exists("users.json"):
        print("User database not found")
        return

    try:
        with open("users.json", "r") as f:
            users = json.load(f)
    except json.JSONDecodeError:
        print("User database is corrupted")
        return

    # Validate user
    if username not in users:
        print("Invalid username")
        return

    user = users[username]

    # Initialize lockout tracking defaults if missing
    failed_attempts = int(user.get("failed_attempts", 0))
    locked_until = user.get("locked_until")  # epoch seconds or None

    # If account is locked, block login
    now = int(time.time())
    if locked_until is not None:
        try:
            locked_until_int = int(locked_until)
        except (TypeError, ValueError):
            locked_until_int = 0
        if now < locked_until_int:
            remaining = locked_until_int - now
            minutes = max(1, remaining // 60)
            print(f"Account locked. Try again in {minutes} minute(s).")
            return

    # Verify password
    stored_hash_str = user.get("password", "")
    if not stored_hash_str:
        print("Invalid password")
        return

    stored_hash = stored_hash_str.encode("utf-8")
    if not bcrypt.checkpw(password.encode("utf-8"), stored_hash):
        # Increment failed attempts and possibly lock account
        failed_attempts += 1
        if failed_attempts >= 3:
            user["failed_attempts"] = failed_attempts
            user["locked_until"] = now + 15 * 60  # 15 minutes
            users[username] = user
            try:
                with open("users.json", "w") as f:
                    json.dump(users, f)
            except Exception:
                pass
            print("Too many failed attempts. Account locked for 15 minutes.")
            return
        else:
            user["failed_attempts"] = failed_attempts
            users[username] = user
            try:
                with open("users.json", "w") as f:
                    json.dump(users, f)
            except Exception:
                pass
            remaining = 3 - failed_attempts
            attempt_word = "attempt" if remaining == 1 else "attempts"
            print(f"Invalid password. {remaining} {attempt_word} remaining.")
            return

    # Verify OTP
    secret = user.get("secret")
    if not secret:
        print("Invalid OTP")
        return

    totp = pyotp.TOTP(secret)
    if not totp.verify(otp):
        print("Invalid OTP")
        return

    # Success: reset failed attempts and lockout
    user["failed_attempts"] = 0
    user["locked_until"] = None
    users[username] = user
    try:
        with open("users.json", "w") as f:
            json.dump(users, f)
    except Exception:
        pass

    # Success output consumed by login.php
    print("success")


if __name__ == "__main__":
    main()