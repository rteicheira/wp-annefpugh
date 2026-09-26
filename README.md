# Anne Pugh Therapy

Custom WordPress theme for Anne Pugh, LCSW — a single-proprietor therapy practice in Berkeley/San Francisco. No blog — a small, focused sitemap plus legal pages, built to be warm, mobile-first, WCAG-minded, and locally searchable for the Bay Area.

## Sitemap (5 pages + legal)

- **Home** — fully static, no post loop: splash image + hero card, therapist photo + intro, 3 featured services with descriptions, practice-at-a-glance qualifications, contact CTA
- **About** — bio, credentials (LCSW), approach/modalities, populations served
- **Services** — chronic illness; pregnancy, prenatal & postpartum; grief; anxiety; burnout; depression; chronic pain; life transitions
- **FAQ / Rates** — fees, insurance, what to expect, telehealth vs. in-person
- **Contact** — form + phone
- Privacy Policy, Cookie Policy, Terms of Service — any slug, default page template

## Structure

```
style.css                        Theme header (name, version, text domain)
functions.php                    Setup, asset enqueue, nav menus, comments disabled
header.php / footer.php          Site-wide shell: skip link, mobile nav toggle, crisis notice + nav + legal links in footer
front-page.php                   Home page: 100% template parts, no post loop, no dependency on Reading Settings
page.php / index.php             Generic page template (About, FAQ, Services, legal pages)
page-templates/
  template-contact.php           Contact page with a native (no-plugin) form
inc/
  contact-form.php               Form handler: nonce, honeypot, sanitized/validated input, wp_mail()
  customizer.php                 Customizer "Practice Info" + "Homepage Images" panels
  seo.php                        Meta description, Open Graph tags, canonical link, MedicalBusiness JSON-LD
template-parts/
  hero-splash.php                Large splash image/gradient + overlaid hero card (name, tagline, CTA)
  therapist-intro.php            Therapist photo (or placeholder) + short intro, links to About
  services.php                   3 featured services, each with a real description, + "also treats" list, links to `services`
  qualifications.php             License, format, languages, insurance — "practice at a glance"
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
6. Under Appearance → Customize → Homepage Images, set:
   - Splash Image + alt text (large image at the top of the homepage — falls back to a sky/hills gradient if left empty)
   - Therapist Photo + alt text (shows a placeholder silhouette if left empty)
7. Under Appearance → Menus, assign:
   - **Primary** — main site nav (Home, About, Services, FAQ, Contact)
   - **Footer Menu** (optional) — same pages, repeated in the footer
   - **Footer Legal Menu** — Privacy Policy, Terms of Service, Cookie Policy (shown in the footer's bottom bar)
8. Optional but recommended: Settings → Reading → set "Your homepage displays" to a static page and pick Home. Not required for the homepage to render (`front-page.php` always wins at the site root regardless of this setting), but it makes `is_home()`/body classes report correctly since there's no blog on this site.

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

## Footer & crisis resources

The footer always shows a 988/911 crisis notice ("this website and email are not monitored for emergencies") — this isn't optional or tied to any Customizer setting, since it's standard practice for a mental health practice's site and shouldn't depend on setup being finished. Below that: site navigation (Footer Menu), contact info/NAP, and a bottom bar with copyright + legal links (Footer Legal Menu). Set up the Privacy Policy, Terms of Service, and Cookie Policy pages and assign them to the Footer Legal Menu location.

## Notes

- No blog: comments are disabled site-wide, there's no post archive/single template, and the homepage doesn't use the post loop at all.
- The contact form posts to `admin-post.php` and sends via `wp_mail()` to the Customizer's contact email, falling back to the site admin email.
