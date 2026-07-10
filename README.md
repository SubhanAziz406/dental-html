# Dental Clinic — 4-Page Website

A 4-page responsive website (Home, About, Services, Contact) built with
HTML5, CSS3, Bootstrap 5, and a core-PHP form handler (no frameworks).

## Structure
```
dental-html/
├── index.html          Home
├── about.html           About Us
├── services.html         Services & pricing
├── contact.html           Contact + appointment form
├── css/style.css          All custom styling (design tokens at top)
├── js/script.js           Footer year, accordion, AJAX form submit
├── php/send-mail.php       Form handler (PHP core, uses mail())
└── README.md
```

## Before you go live — 3 things to update

1. **Client email** — open `php/send-mail.php` and replace:
   ```php
   const CLINIC_EMAIL = 'CLIENT_EMAIL_PLACEHOLDER@example.com';
   ```
   with the clinic's real inbox.

2. **Real content** — search each `.html` file for bracketed placeholders
   like `Subhan Aziz`, and swap in the real doctor name, exact
   address, and confirmed working hours. Prices in `services.html` are
   placeholder estimates — update them to match the clinic's actual price list.

3. **Google Map embed** — in `contact.html`, the map iframe currently
   searches for " Dental Clinic Lahore" by name. For a pinpoint
   location, go to Google Maps → your business listing → Share → Embed a map,
   and copy the `src` URL into the `<iframe>` in `contact.html`.

## Running it locally

The 4 HTML pages will open fine by just double-clicking them, but the
**contact form needs a real PHP server** to work (PHP's `mail()` doesn't run
from a plain file:// page). To test locally:

```bash
cd dental-html
php -S localhost:8000
```

Then visit `http://localhost:8000`. Note: `mail()` typically won't actually
deliver email from a local machine unless you've configured a local mail
transport (sendmail/Mailhog/etc). It **will** work correctly once uploaded
to a real host like Hostinger/cPanel, which has mail() pre-configured.

## Deploying to shared hosting (cPanel/Hostinger)

1. Upload the entire `dental-html` folder contents to `public_html`
   (or a subfolder if this isn't the whole domain).
2. Make sure `php/send-mail.php` keeps its folder path — the form calls it
   at the relative path `php/send-mail.php`.
3. That's it — no database, no Composer install, no build step required.

## If mail() gets marked as spam or doesn't arrive

Some hosts silently drop `mail()` messages sent with a "From" address that
doesn't match the sending domain (this is already handled — the script sets
`From: noreply@yourdomain.com` automatically). If deliverability is still
unreliable after going live, the cleanest upgrade is swapping `mail()` for
PHPMailer + SMTP (using the clinic's real email account credentials) inside
`php/send-mail.php` — the validation, sanitization, and JSON response code
around it can stay exactly as-is.

## Design notes

- Colors, fonts, and spacing are all defined as CSS variables at the top of
  `css/style.css` (`:root` block) — change them there and they apply
  site-wide.
- The palette used is a placeholder professional teal/coral combination
  (I couldn't access the clinic's actual logo image to match exact brand
  colors). Swap the hex values in `:root` once you have the logo file.
- All icons are placeholder Bootstrap Icons / inline SVGs, as requested —
  swap in real clinic photos wherever you see a placeholder graphic
  (hero illustration, doctor photo block) for a more personal feel.
