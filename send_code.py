import sys
import os
import smtplib
from email.message import EmailMessage


def parse_args():
  
    args = sys.argv[1:]
    if len(args) < 2:
        print("Usage: send_code.py <email> <code> [--smtp-host HOST --smtp-port PORT --smtp-user USER --smtp-pass PASS --from-email EMAIL --from-name NAME]")
        return None

    email = args[0]
    code = args[1]

    # Defaults from env
    opts = {
        'smtp_host': os.getenv('SMTP_HOST', 'smtp.gmail.com'),
        'smtp_port': int(os.getenv('SMTP_PORT', '587')),
        'smtp_user': os.getenv('SMTP_USER', ''),
        'smtp_pass': os.getenv('SMTP_PASS', ''),
        'from_email': os.getenv('FROM_EMAIL', ''),
        'from_name': os.getenv('FROM_NAME', 'Account Verification')
    }

    # Parse flags
    i = 2
    while i < len(args):
        if args[i] == '--smtp-host' and i + 1 < len(args):
            opts['smtp_host'] = args[i + 1]
            i += 2
        elif args[i] == '--smtp-port' and i + 1 < len(args):
            try:
                opts['smtp_port'] = int(args[i + 1])
            except ValueError:
                pass
            i += 2
        elif args[i] == '--smtp-user' and i + 1 < len(args):
            opts['smtp_user'] = args[i + 1]
            i += 2
        elif args[i] == '--smtp-pass' and i + 1 < len(args):
            opts['smtp_pass'] = args[i + 1]
            i += 2
        elif args[i] == '--from-email' and i + 1 < len(args):
            opts['from_email'] = args[i + 1]
            i += 2
        elif args[i] == '--from-name' and i + 1 < len(args):
            opts['from_name'] = args[i + 1]
            i += 2
        else:
            i += 1

    return email, code, opts


def main() -> None:
    parsed = parse_args()
    if not parsed:
        return
    recipient_email, verification_code, opts = parsed

    smtp_host = opts['smtp_host']
    smtp_port = opts['smtp_port']
    smtp_user = opts['smtp_user']
    smtp_pass = opts['smtp_pass']
    from_email = opts['from_email'] or smtp_user
    from_name = opts['from_name']

    if not smtp_user or not smtp_pass or not from_email:
        print("SMTP not configured")
        return

    subject = "Your verification code"
    body = (
        f"Your email verification code is: {verification_code}\n\n"
        "This code will expire in 10 minutes."
    )

    msg = EmailMessage()
    msg["Subject"] = subject
    msg["From"] = f"{from_name} <{from_email}>"
    msg["To"] = recipient_email
    msg.set_content(body)

    try:
        if smtp_port == 465:
            # Use SSL for port 465
            with smtplib.SMTP_SSL(smtp_host, smtp_port, timeout=20) as server:
                server.login(smtp_user, smtp_pass)
                server.send_message(msg)
        else:
            # Use TLS for other ports (e.g., 587)
            with smtplib.SMTP(smtp_host, smtp_port, timeout=20) as server:
                server.starttls()
                server.login(smtp_user, smtp_pass)
                server.send_message(msg)
        print("sent")
    except Exception as exc:
        print(f"Error: {exc}")


if __name__ == "__main__":
    main()


