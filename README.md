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
  template-functions.php      Defaults, annefpugh_mod(), contrast helpers, image helper
  setup.php                   Theme supports, image sizes, menus, assets, comments disabled
  post-types.php              "Service" post type
  customizer.php              Customizer settings (design, homepage wording, page links)
  admin-practice-info.php     "Practice Info" admin screen, Dashboard box, toolbar link
  custom-css.php              Customizer colors → CSS custom properties
  contact-form.php            Form handler (nonce, honeypot, sanitized input, wp_mail)
  schema.php                  MedicalBusiness JSON-LD (skipped if an SEO plugin is active)
template-parts/
  crisis-bar.php              Optional 988/911 bar above the header
  crisis-notice.php           Permanent crisis notice in the footer
  home/                       hero, about, services, practice-info, cta
  content/                    page, service-card, none
assets/css/main.css           All styles
assets/js/navigation.js       Mobile menu toggle
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
   - *Order* (under Page Attributes) sets the display order.
5. **Practice Info** (left admin menu, just below Dashboard): name, credentials, photo and its description, introduction, phone, email, address, map link, session format, hours, fees, insurance, and license. Each field has an example and a line of help text. The same screen is linked from the "Edit Practice Info" item in the admin toolbar on the live site, and from the "Update your website" box on the Dashboard.
6. **Appearance → Customize** (design and homepage wording):
   - *Site Identity*: site title, logo, site icon
   - *Colors*: background color plus 7 theme colors and the hero overlay strength
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
- Visible focus outlines everywhere; touch targets of at least 44px.
- **Automatic contrast:** the theme calculates button text, footer text, and hero text as white or near-black from the colors you pick, so they stay WCAG-legible. Body text and heading colors are yours to choose — keep them dark enough (4.5:1) against your background.
- Keep the hero overlay at 40% or higher when using a photo.
- `prefers-reduced-motion` is respected.
- Form fields have visible labels, `autocomplete` hints, and required-field markers; status messages are announced.

## Safety & privacy

- The footer on every page shows a crisis notice: 988 (Suicide & Crisis Lifeline), 911, and Crisis Text Line (text HOME to 741741), plus a note that the site isn't monitored for emergencies. It can't be turned off.
- An optional slim 988/911 bar can be shown above the header (on by default).
- The contact form asks visitors not to send sensitive health details. It sends via `wp_mail()` and stores nothing in the database. It's not a HIPAA-compliant intake — use a proper client portal for that.
- No external fonts or scripts are loaded, so visitors' data isn't sent to third parties.
- Comments are disabled site-wide.
