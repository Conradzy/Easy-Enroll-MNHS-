import sys
import smtplib
from email.message import EmailMessage
from typing import Dict, Tuple, Optional


def parse_args() -> Optional[Tuple[str, Dict[str, str]]]:
    """
    Parse CLI arguments.

    Usage:
      send_enrolled_email.py <email>
      send_enrolled_email.py <email> --smtp-host HOST --smtp-port PORT --smtp-user USER --smtp-pass PASS --from-email EMAIL --from-name NAME
    """
    args = sys.argv[1:]
    if len(args) < 1:
        print(
            "Usage: send_enrolled_email.py <email> "
            "[--smtp-host HOST --smtp-port PORT --smtp-user USER --smtp-pass PASS "
            "--from-email EMAIL --from-name NAME]"
        )
        return None

    email = args[0].strip()
    if not email:
        print("Recipient email is required")
        return None

    # Defaults to the school Gmail account
    opts: Dict[str, str] = {
        "smtp_host": "smtp.gmail.com",
        "smtp_port": "587",
        "smtp_user": "maragusannationalhighschool1@gmail.com",
        "smtp_pass": "avjs csmr minm smpt",
        "from_email": "maragusannationalhighschool1@gmail.com",
        "from_name": "Maragusan National High School",
    }

    i = 1
    while i < len(args):
        if args[i] == "--smtp-host" and i + 1 < len(args):
            opts["smtp_host"] = args[i + 1]
            i += 2
        elif args[i] == "--smtp-port" and i + 1 < len(args):
            opts["smtp_port"] = args[i + 1]
            i += 2
        elif args[i] == "--smtp-user" and i + 1 < len(args):
            opts["smtp_user"] = args[i + 1]
            i += 2
        elif args[i] == "--smtp-pass" and i + 1 < len(args):
            opts["smtp_pass"] = args[i + 1]
            i += 2
        elif args[i] == "--from-email" and i + 1 < len(args):
            opts["from_email"] = args[i + 1]
            i += 2
        elif args[i] == "--from-name" and i + 1 < len(args):
            opts["from_name"] = args[i + 1]
            i += 2
        else:
            i += 1

    return email, opts


def send_enrollment_email(recipient_email: str, opts: Dict[str, str]) -> None:
    """
    Send the official enrollment confirmation email to the given recipient.
    """
    smtp_host = opts.get("smtp_host", "smtp.gmail.com")
    smtp_port_str = opts.get("smtp_port", "465")
    try:
        smtp_port = int(smtp_port_str)
    except ValueError:
        smtp_port = 465

    smtp_user = opts.get("smtp_user", "")
    smtp_pass = opts.get("smtp_pass", "")
    from_email = opts.get("from_email") or smtp_user
    from_name = opts.get("from_name", "Maragusan National High School")

    if not smtp_user or not smtp_pass or not from_email:
        print("SMTP not configured")
        return

    subject = "Enrollment Confirmation - Maragusan National High School"
    body = (
        "You are officially Enrolled at Maragusan National High School.\n\n"
        "Please check your account for updates and profile setup."
    )

    msg = EmailMessage()
    msg["Subject"] = subject
    msg["From"] = f"{from_name} <{from_email}>"
    msg["To"] = recipient_email
    msg.set_content(body)

    try:
        if smtp_port == 465:
            with smtplib.SMTP_SSL(smtp_host, smtp_port, timeout=20) as server:
                server.login(smtp_user, smtp_pass)
                server.send_message(msg)
        else:
            with smtplib.SMTP(smtp_host, smtp_port, timeout=20) as server:
                server.starttls()
                server.login(smtp_user, smtp_pass)
                server.send_message(msg)
        print("sent")
    except Exception as exc:
        print(f"Error: {exc}")


def main() -> None:
    parsed = parse_args()
    if not parsed:
        return
    email, opts = parsed
    send_enrollment_email(email, opts)


if __name__ == "__main__":
    main()


