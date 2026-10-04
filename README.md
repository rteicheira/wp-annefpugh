# Single Provider Therapy — WordPress theme

A warm, accessible WordPress theme for a single-provider therapy practice. You set everything from the dashboard: colors, images, practice details, and services. No code changes needed.

## Site structure (5 pages + legal)

| Page | Template | Purpose |
|---|---|---|
| Home | `front-page.php` (automatic) | Hero, meet the therapist, services grid, practice details, closing call to action |
| About | Default | Full bio, approach, credentials |
| Services | **Services** | Intro text, then every service with its image, title, and full description |
| Fees & FAQ | Default | Rates, insurance, what to expect |
| Contact | **Contact** | Contact form + phone/email/office/hours |
| Privacy Policy, Terms of Service, Cookie Policy, etc. | Default | Legal pages — linked from the footer |

## File layout

```text
style.css                     Theme header
functions.php                 Loads inc/*
header.php / footer.php       Site shell (skip link, crisis bar, nav / crisis notice, practice info, links)
front-page.php                Homepage (template parts only, no post loop)
page.php / index.php / 404.php
page-templates/
  template-services.php       "Services" page template
  template-contact.php        "Contact" page template
inc/
  template-functions.php      Defaults, annefpugh_mod(), contrast helpers, client IP, asset versions
  setup.php                   Theme supports, image sizes, menus, assets, comments disabled
  post-types.php              "Service" post type
  customizer.php              Customizer settings (design, homepage wording, page links)
  admin-practice-info.php     "Practice Info" admin screen, Dashboard box, toolbar link
  admin-service-order.php     Services → Change Order (drag-and-drop + Move up/down)
  recaptcha.php               Settings → Spam Protection (Google reCAPTCHA)
  custom-css.php              Customizer colors → contrast-checked CSS custom properties
  contact-form.php            Form handler (honeypot, signed timestamp, rate limit, reCAPTCHA, wp_mail)
  schema.php                  MedicalBusiness JSON-LD (skipped if an SEO plugin is active)
template-parts/
  crisis-bar.php              Optional 988/911 bar above the header
  crisis-notice.php           Permanent crisis notice in the footer
  contact-form.php            Shared form markup (full + quick variants)
  home/                       hero, about, services, practice-info, cta
  content/                    page, service-card, none
assets/css/main.css           All styles
assets/js/                    navigation, call-back toggle, reCAPTCHA, admin screens
languages/annefpugh.pot       Translation template (regenerate: wp i18n make-pot . languages/annefpugh.pot)
screenshot.png                Appearance → Themes thumbnail
audits/                       Code reviews — never deployed (see deployafp.sh)
```

## Setup

1. Activate the theme.
2. **Create the pages:** Home, About, Services (template: *Services*), Fees & FAQ, Contact (template: *Contact*), plus your legal pages.
3. **Settings → Reading:** set "Your homepage displays" to *A static page* → Home.
4. **Services** (left admin menu): add one entry per service:
   - *Title* is the service name.
   - *Featured image* is the service image.
   - *Excerpt* is the short summary on the homepage card.
   - *Content* is the full description on the Services page.
   - To change the order, use **Services → Change Order**. Drag the services, or use the Move up / Move down buttons, then click Save order. The Services list in the dashboard shows the same order as the website, and new services are added at the end.
5. **Practice Info** (left admin menu, just below Dashboard): name, credentials, photo and its description, introduction, phone, email, address, map link, session format, hours, fees, insurance, and license. Each field has an example and a line of help text. The same screen is linked from the "Edit Practice Info" item in the admin toolbar on the live site, and from the "Update your website" box on the Dashboard.
6. **Appearance → Customize** (design and homepage wording):
   - *Site Identity*: site title, logo, site icon
   - *Colors*: page background plus 9 theme colors (primary, secondary, body text, headings, alternate sections, header, cards/forms, footer, hero overlay) and the hero overlay strength
   - *Background Image*: optional site-wide background image
   - *Practice Theme Options →* Hero, Homepage Sections, Crisis & Safety, Page Links
   - Under *Page Links*, choose your About, Services, and Contact pages. The buttons and links throughout the site use these.
7. **Appearance → Menus:** assign *Primary Menu*, *Footer Quick Links*, and *Footer Legal Links*. The legal menu falls back to your designated Privacy Policy page if left empty.

## Editable images

| Image | Where to change it |
|---|---|
| Logo | Customize → Site Identity |
| Site background | Customize → Background Image |
| Homepage hero | Customize → Practice Theme Options → Homepage: Hero |
| Therapist photo (and its description) | Practice Info |
| Service images | Services → edit service → Featured image |
| Interior page banners | Edit page → Featured image (shown behind the page title) |

## Accessibility

- Skip link, landmark regions, one `h1` per page, labelled `nav`/`section` regions.
- Keyboard-operable mobile menu (`aria-expanded`, Escape closes it and returns focus to the toggle).
- Visible focus outlines everywhere, at least 3:1 against whatever background they're on (footer and crisis bar included); form field borders 3.9:1; touch targets of at least 44px.
- **Automatic contrast:** pick any colors. For each area (page, alternate sections, header, cards/forms, footer), the theme checks every text, heading, link, and focus-ring color against that area's background and darkens or lightens it, keeping the hue, until it meets WCAG AA (4.5:1 for text, 3:1 for focus rings). Button, footer, and hero text are set to white or near-black. Tested against deliberately bad combinations: all 15 pairings pass every time.
- Keep the hero overlay at 40% or higher when using a photo.
- `prefers-reduced-motion` is respected.
- Form fields have visible labels, `autocomplete` hints, and required-field markers; status messages are announced.

## Safety & privacy

- The footer on every page shows a crisis notice: 988 (Suicide & Crisis Lifeline), 911, and Crisis Text Line (text HOME to 741741), plus a note that the site isn't monitored for emergencies. It can't be turned off.
- An optional slim 988/911 bar can be shown above the header (on by default).
- All crisis wording is editable under Customize → Practice Theme Options → Crisis & Safety: the top bar, the footer heading, the footer resources (one per line), the footer note, and the contact form's safety note. Phone numbers typed there (988, 911, 741741, or full numbers like (555) 123-4567) become tap-to-call or tap-to-text links automatically. A field left blank shows the default wording, so crisis information can't be emptied out by accident.
- Two forms share one template (`template-parts/contact-form.php`) and one handler: the full form on the Contact page (name, email, optional phone, message), and a short call-back form in the homepage/Services "Ready to take the first step?" section (name, phone, what they're looking for). Both send to the email under **Practice Info**, falling back to the site admin email if none is set.
- **Spam protection**, always on:
  - **Honeypot:** a hidden field that only bots fill in.
  - **Signed timestamp in each form:** rejects anything submitted faster than a person can type. Unlike a WordPress nonce it never expires, so forms keep working on cached pages.
  - **Rate limit:** 5 messages per visitor per 10 minutes.
  - **Behind Cloudflare:** add `add_filter( 'annefpugh_trust_cloudflare_ip', '__return_true' );` so the limit uses the real visitor IP. Without it, the `CF-Connecting-IP` header is ignored, because anyone can fake it when the site isn't behind Cloudflare.
- **Google reCAPTCHA** (optional), under **Settings → Spam Protection**:
  - v3 (invisible, recommended for accessibility) or v2 "I'm not a robot" checkbox
  - site key and secret key (the secret is never shown again after saving)
  - a strictness score for v3, and a check that the token was issued for this site's domain (override with the `annefpugh_recaptcha_hostname` filter)
  - Google's script only loads on pages with a form, and for v3 only once a visitor starts typing.
  - **Messages are never lost because of Google.** If a visitor's browser blocks Google, or Google can't be reached, the message is still delivered but its subject starts with **[Unverified]**. Only messages Google positively flags as bots are rejected.
  - reCAPTCHA sends visitor data to Google, so mention it in the Privacy Policy.
- Field lengths are enforced on the server, and failed emails are logged to the PHP error log.
- The contact forms ask visitors not to send sensitive health details. It sends via `wp_mail()` and stores nothing in the database. It's not a HIPAA-compliant intake — use a proper client portal for that.
- No external fonts or scripts are loaded, so visitors' data isn't sent to third parties.
- Comments are disabled site-wide, including comment feeds and the admin-bar comments item.

## Deploying

Use `~/scripts/deployafp.sh`. It mirrors this folder to the server with `rsync --delete` and never publishes `.git`, `audits/`, or `*.md` files. Run `deployafp.sh -n` first to preview the changes. Asset URLs are versioned by file modification time, so visitors always get the latest CSS/JS without a manual version bump.
