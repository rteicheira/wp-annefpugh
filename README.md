# AnneFPugh Therapy

Custom WordPress theme for a single-proprietor therapy practice site. No blog — a small set of static pages (Home, About, Services, Contact) plus legal pages (Privacy Policy, Cookie Policy, Terms of Service).

## Structure

```
style.css                        Theme header (name, version, text domain)
functions.php                    Setup, asset enqueue, nav menus, comments disabled
header.php / footer.php          Site-wide shell
front-page.php                   Home page: hero + editable content + services teaser + CTA
page.php / index.php             Generic page template (About, Services, legal pages)
page-templates/
  template-contact.php           Contact page with a native (no-plugin) form
inc/
  contact-form.php               Form handler: nonce, honeypot, sanitized/validated input, wp_mail()
  customizer.php                 Customizer "Practice Info" panel (phone, email, address, hours)
template-parts/
  hero.php                       Home page hero
  services-teaser.php            Links to the page with slug `services`
  cta.php                        Links to the page with slug `contact`
css/main.css                     All styles
js/main.js                       All JavaScript
```

## Setup

1. Install as a theme in `wp-content/themes/annefpugh/` (or symlink this directory there).
2. Activate the theme.
3. Create these pages, matching the slugs — the home page template looks them up by slug:
   - `services`
   - `contact` (assign the **Contact Form** page template)
   - `about`
   - Privacy Policy, Cookie Policy, Terms of Service (any slugs — use the default page template)
4. Set the site's front page to a page using the front page template (Settings → Reading), or create a page titled "Home" and set it as the static front page.
5. Under Appearance → Customize → Practice Info, set the phone, email, address, and office hours.
6. Under Appearance → Menus, assign a Primary and (optionally) Footer menu.

## Notes

- No blog: comments are disabled site-wide and there's no post archive/single template.
- The contact form posts to `admin-post.php` and sends via `wp_mail()` to the Customizer's contact email, falling back to the site admin email.
