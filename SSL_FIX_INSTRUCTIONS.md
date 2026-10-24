# Fix SSL Certificate Issue for Cloudinary

## Problem
You're getting this error:
```
cURL error 60: SSL certificate problem: unable to get local issuer certificate
```

This happens because PHP's cURL on Windows/WAMP doesn't have the CA certificate bundle to verify SSL certificates.

## Solution 1: Update php.ini (Recommended)

1. **Find your php.ini file:**
   - Open `http://localhost/authenticator/fix_ssl.php` in your browser
   - It will show you the exact path to your php.ini file

2. **Edit php.ini:**
   - Open the php.ini file in a text editor (as Administrator)
   - Find these lines (or add them if they don't exist):
     ```ini
     curl.cainfo = "C:\wamp64\www\authenticator\cacert.pem"
     openssl.cafile = "C:\wamp64\www\authenticator\cacert.pem"
     ```
   - Make sure the path points to the `cacert.pem` file in your project directory
   - Save the file

3. **Restart Apache/WAMP:**
   - Stop and start WAMP services
   - Or restart Apache from WAMP menu

4. **Test:**
   - Go to `http://localhost/authenticator/test_cloudinary.php`
   - Try uploading an image

## Solution 2: Development Workaround (Quick Fix)

If you can't edit php.ini, you can use development mode:

1. **Enable dev_mode in cloudinary_config.php:**
   ```php
   'dev_mode' => true  // Development only!
   ```

2. **This will disable SSL verification** (less secure, but works for development)

3. **⚠️ WARNING:** Never use `dev_mode => true` in production!

## Solution 3: Use Environment Variables

You can also set these environment variables before starting PHP:

```bash
set SSL_CERT_FILE=C:\wamp64\www\authenticator\cacert.pem
```

But this needs to be set before PHP starts, so it's not practical for web requests.

## Verify the Fix

After applying Solution 1, test with:
- `http://localhost/authenticator/test_cloudinary.php`
- Try uploading an image through your enrollment form

## Production Deployment

For production servers:
1. Make sure `cacert.pem` is in your project directory
2. Update php.ini on the production server
3. Set `dev_mode => false` in cloudinary_config.php
4. Restart the web server

