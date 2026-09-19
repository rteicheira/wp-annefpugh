# Anne Pugh Therapy

Custom WordPress theme for Anne Pugh, LCSW — a single-proprietor therapy practice in Berkeley/San Francisco. No blog — a small, focused sitemap plus legal pages, built to be warm, mobile-first, WCAG-minded, and locally searchable for the Bay Area.

## Sitemap (5 pages + legal)

- **Home** — hero, specialty pills, services teaser, contact CTA
- **About** — bio, credentials (LCSW), approach/modalities, populations served
- **Services** — chronic illness; pregnancy, prenatal & postpartum; grief; anxiety; burnout; depression; chronic pain; life transitions
- **FAQ / Rates** — fees, insurance, what to expect, telehealth vs. in-person
- **Contact** — form + phone
- Privacy Policy, Cookie Policy, Terms of Service — any slug, default page template

## Structure

```
style.css                        Theme header (name, version, text domain)
functions.php                    Setup, asset enqueue, nav menus, comments disabled
header.php / footer.php          Site-wide shell: skip link, mobile nav toggle, NAP in footer
front-page.php                   Home page: hero + editable content + services teaser + CTA
page.php / index.php             Generic page template (About, FAQ, Services, legal pages)
page-templates/
  template-contact.php           Contact page with a native (no-plugin) form
inc/
  contact-form.php               Form handler: nonce, honeypot, sanitized/validated input, wp_mail()
  customizer.php                 Customizer "Practice Info" panel
  seo.php                        Meta description, Open Graph tags, canonical link, MedicalBusiness JSON-LD
template-parts/
  hero.php                       Home page hero (name, tagline, service area, CTA button)
  services-teaser.php            Fixed specialty list + links to the page with slug `services`
  cta.php                        Contact button + click-to-call, links to the page with slug `contact`
css/main.css                     All styles
js/main.js                       Mobile nav toggle only
```

## Setup

1. Install as a theme in `wp-content/themes/annefpugh/` (or symlink this directory there).
2. Activate the theme.
3. Create these pages, matching the slugs — the home page template looks them up by slug:
   - `services`
   - `contact` (assign the **Contact Form** page template)
   - `about`
   - FAQ / rates page (any slug)
   - Privacy Policy, Cookie Policy, Terms of Service (any slugs — use the default page template)
4. Set the site's front page to a page using the front page template (Settings → Reading), or create a page titled "Home" and set it as the static front page.
5. Under Appearance → Customize → Practice Info, set:
   - Phone, Contact Email, Office Address, Office Hours
   - Service Area (shown in the hero — defaults to a Berkeley/SF/East Bay line)
   - Homepage Meta Description (SEO, ~155 characters)
   - Psychology Today Profile URL (surfaced in the footer and in the JSON-LD `sameAs`)
6. Under Appearance → Menus, assign a Primary and (optionally) Footer menu.

## SEO & local search

- `inc/seo.php` outputs a meta description, Open Graph tags, a canonical link, and `MedicalBusiness` JSON-LD structured data (name, phone, email, address, hours, `areaServed`: Berkeley/San Francisco/Bay Area, and `sameAs` the Psychology Today profile) — fill in the Customizer fields above so this data is complete.
- WordPress core ships an XML sitemap by default (`/wp-sitemap.xml`); no plugin needed.
- Keep page titles and the meta description specific (city names, specialties) rather than generic — that's what drives local search matches.

## Accessibility (WCAG)

- Skip-to-content link, landmark `<main>`, and a keyboard-operable mobile nav toggle (`aria-expanded`, closes on Escape).
- Visible focus outlines on all interactive elements.
- Text and button colors were checked for 4.5:1+ contrast against the background.
- Minimum 44px touch targets on buttons and the nav toggle.
- Add descriptive `alt` text to all uploaded images (logo, headshots) — the theme doesn't set this for you.

## Notes

- No blog: comments are disabled site-wide and there's no post archive/single template.
- The contact form posts to `admin-post.php` and sends via `wp_mail()` to the Customizer's contact email, falling back to the site admin email.
