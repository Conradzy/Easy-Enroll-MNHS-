# Quick SSL Fix - Your php.ini is NOT Configured!

## The Problem
Your php.ini file doesn't have the SSL certificate settings. The `ini_set()` calls in PHP scripts don't persist - you MUST update php.ini directly.

## Quick Fix (Choose One)

### Option 1: Automatic Fix (Recommended)
1. **Open in browser:** `http://localhost/authenticator/update_php_ini.php`
2. **If it says "permission denied":**
   - Right-click on the script output
   - Copy the php.ini path shown
   - Open php.ini as Administrator
   - Follow the manual steps below

### Option 2: Manual Fix
1. **Find your php.ini:**
   - Open: `http://localhost/authenticator/verify_ssl_fix.php`
   - It will show you the exact path

2. **Edit php.ini:**
   - Open php.ini in Notepad++ or similar (as Administrator)
   - Press Ctrl+F and search for `curl.cainfo`
   - If found, change it to:
     ```ini
     curl.cainfo = "C:\wamp64\www\authenticator\cacert.pem"
     ```
   - If NOT found, add this line anywhere in the file:
     ```ini
     curl.cainfo = "C:\wamp64\www\authenticator\cacert.pem"
     ```
   
   - Search for `openssl.cafile`
   - If found, change it to:
     ```ini
     openssl.cafile = "C:\wamp64\www\authenticator\cacert.pem"
     ```
   - If NOT found, add this line:
     ```ini
     openssl.cafile = "C:\wamp64\www\authenticator\cacert.pem"
     ```

3. **Save php.ini**

4. **RESTART WAMP:**
   - Click WAMP icon → Restart All Services
   - OR Stop Apache, then Start Apache

5. **Verify:**
   - Open: `http://localhost/authenticator/verify_ssl_fix.php`
   - It should show ✓ for all checks

6. **Test:**
   - Open: `http://localhost/authenticator/test_cloudinary.php`
   - Try uploading an image

## Why This is Needed
- PHP scripts can't permanently change php.ini settings
- `ini_set()` only works for that script execution
- Guzzle (used by Cloudinary) reads php.ini settings
- You MUST update php.ini and restart Apache

## Still Not Working?
1. Verify php.ini path is correct (check verify_ssl_fix.php)
2. Make sure you RESTARTED Apache/WAMP
3. Check that cacert.pem exists in your project folder
4. Verify the path in php.ini uses forward slashes or escaped backslashes

