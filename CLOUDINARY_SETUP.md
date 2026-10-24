# Cloudinary Setup Guide

This guide will help you migrate from local file storage to Cloudinary for image storage.

## Step 1: Create a Cloudinary Account

1. Go to [https://cloudinary.com/users/register/free](https://cloudinary.com/users/register/free)
2. Sign up for a free account (25GB storage, 25GB bandwidth per month)
3. After registration, you'll be taken to your dashboard

## Step 2: Get Your Cloudinary Credentials

1. In your Cloudinary dashboard, go to **Settings** (gear icon)
2. Scroll down to find your **Cloudinary URL** or individual credentials:
   - **Cloud Name**: Found in your dashboard URL or settings
   - **API Key**: Found in the Account Details section
   - **API Secret**: Found in the Account Details section (click "Reveal" to see it)

## Step 3: Configure Your Application

1. Open `cloudinary_config.php`
2. Replace the placeholder values with your actual credentials:

```php
return [
    'cloud_name' => 'your_actual_cloud_name',
    'api_key' => 'your_actual_api_key',
    'api_secret' => 'your_actual_api_secret',
    'secure' => true
];
```

## Step 4: Install Cloudinary PHP SDK

### Option A: Using Composer (Recommended)

1. If you don't have Composer installed, download it from [https://getcomposer.org/](https://getcomposer.org/)

2. In your project root directory, run:
```bash
composer require cloudinary/cloudinary_php
```

3. This will create a `vendor/` directory with the Cloudinary SDK

### Option B: Manual Installation

1. Download the Cloudinary PHP SDK from [https://github.com/cloudinary/cloudinary_php](https://github.com/cloudinary/cloudinary_php)
2. Extract it to your project directory
3. Update the path in `cloudinary_helper.php` to point to the SDK

## Step 5: Test the Integration

1. Try uploading an image through your enrollment form
2. Check your Cloudinary dashboard's Media Library to see if the image was uploaded
3. Verify that the image URL is stored in your database

## Step 6: Update Existing Images (Optional)

If you have existing images stored locally, you can migrate them to Cloudinary:

1. Create a migration script to:
   - Read existing image paths from the database
   - Upload each image to Cloudinary
   - Update the database with the new Cloudinary URL
   - Optionally delete the local files

## Benefits of Using Cloudinary

- ✅ **CDN Delivery**: Images are served from a global CDN for faster loading
- ✅ **Automatic Optimization**: Images are automatically optimized for web
- ✅ **Transformations**: Easy image transformations (resize, crop, etc.)
- ✅ **Scalability**: No server storage limits
- ✅ **Backup**: Images are automatically backed up
- ✅ **Free Tier**: 25GB storage and 25GB bandwidth per month

## Troubleshooting

### Error: "Cloudinary SDK not found"
- Make sure you've installed the Cloudinary PHP SDK
- Check that the `vendor/autoload.php` file exists
- Verify the path in `cloudinary_helper.php`

### Error: "Invalid credentials"
- Double-check your credentials in `cloudinary_config.php`
- Make sure there are no extra spaces or quotes
- Verify your API key and secret are correct

### Images not uploading
- Check PHP error logs
- Verify file upload limits in `php.ini`:
  - `upload_max_filesize`
  - `post_max_size`
- Check Cloudinary dashboard for upload errors

## Security Notes

⚠️ **Important**: Never commit your `cloudinary_config.php` file with real credentials to version control!

1. Add `cloudinary_config.php` to `.gitignore`
2. Create a `cloudinary_config.example.php` with placeholder values
3. Use environment variables for production deployments

## Support

- Cloudinary Documentation: [https://cloudinary.com/documentation](https://cloudinary.com/documentation)
- PHP SDK Documentation: [https://cloudinary.com/documentation/php_integration](https://cloudinary.com/documentation/php_integration)

