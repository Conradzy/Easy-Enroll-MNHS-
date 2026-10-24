# Easy Enroll MNHS

A PHP and MySQL enrollment management system for Maragusan National High School, with two-factor authentication and Cloudinary-backed uploads.

## Local setup with WAMP

1. Clone or copy the project into `C:\wamp64\www\authenticator`.
2. Start Apache and MySQL in WAMP.
3. Import `schema.sql` into MySQL. The application uses the `ems` database with the local WAMP defaults (`root` with no password).
4. Run `composer install` from the project directory.
5. Copy the example configuration files and add your own credentials:
   - `cloudinary_config.example.php` to `cloudinary_config.php`
   - `smtp_config.example.php` to `smtp_config.php`
   - `enrollment_smtp_config.example.php` to `enrollment_smtp_config.php`
   - `users.example.json` to `users.json`
6. Install the Python authentication dependencies:

   ```powershell
   py -m pip install bcrypt pyotp qrcode pillow
   ```

7. Open `http://localhost/authenticator/landingpage.php`.

Local credentials, generated QR codes, user data, dependencies, and uploaded student documents are intentionally excluded from Git.
