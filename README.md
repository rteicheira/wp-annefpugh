# Single Provider Therapy — WordPress theme

A warm, accessible WordPress theme for a single-provider therapy practice. You set everything from the dashboard: colors, images, practice details, and services. No code changes needed.

[![CodeQL](https://github.com/rteicheira/wp-annefpugh/actions/workflows/github-code-scanning/codeql/badge.svg)](https://github.com/rteicheira/wp-annefpugh/actions/workflows/github-code-scanning/codeql) [![PHPMD](https://github.com/rteicheira/wp-annefpugh/actions/workflows/phpmd.yml/badge.svg)](https://github.com/rteicheira/wp-annefpugh/actions/workflows/phpmd.yml)

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
  setup.php                   Theme supports, image sizes, menus, assets
  disable-comments.php        Turns comments off site-wide (forms, REST, XML-RPC, feeds, admin)
  hardening.php               XML-RPC off, usernames kept private, no blog archives/search/feeds
  post-types.php              "Service" post type
  customizer.php              Customizer settings (design, homepage wording, page links)
  admin-practice-info.php     "Practice Info" admin screen, Dashboard box, toolbar link
  admin-service-order.php     Services → Change Order (drag-and-drop + Move up/down)
  spam-protection.php         Settings → Spam Protection (Turnstile / reCAPTCHA: forms, login, comments)
  custom-css.php              Customizer colors → contrast-checked CSS custom properties
  form-guard.php              Anti-spam for every public form (honeypot, timestamp, rate limits, link check, page passwords, password reset, no sign-ups)
  contact-form.php            Contact form handler (uses form-guard.php; sends with wp_mail)
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
   - *Excerpt* is the short summary on the homepage card. Without one, the card shows the first 28 words of the content.
   - When the full description says more than the card, the card gets a **Read more** button that opens the whole description in an overlay.
   - The homepage grid is 1 / 2 / 3 columns (phone / tablet / desktop). If the services don't fill the last row, the gap is filled by a **welcome card** ("Not sure where to start?" plus a button). It's sized to the gap at each screen size and hidden when the row is full. Edit or turn it off under Customize → Homepage: Sections.
   - *Content* is the full description on the Services page.
   - To change the order, use **Services → Change Order**. Drag the services, or use the Move up / Move down buttons, then click Save order. The Services list in the dashboard shows the same order as the website, and new services are added at the end.
5. **Practice Info** (left admin menu, just below Dashboard): name, credentials, photo and its description, introduction (a visual editor with bold, italics, links, and lists), phone, email, address, map link, session format, hours, fees, insurance, and license. **Insurance** accepts HTML links plus bold/italics (e.g. a link to Headway or a payer list); line breaks are kept and other HTML is stripped. Tick **"Don't show my email address on the website"** to keep the email private: it's removed from the footer and Contact page, but contact form messages are still sent to it. Each field has an example and a line of help text. The same screen is linked from the "Edit Practice Info" item in the admin toolbar on the live site, and from the "Update your website" box on the Dashboard.
6. **Appearance → Customize** (design and homepage wording):
   - *Site Identity*: site title, logo, site icon
   - *Colors*: page background plus 9 theme colors (primary, secondary, body text, headings, alternate sections, header, cards/forms, footer, hero overlay) and the hero overlay strength
   - *Background Image*: optional site-wide background image
   - *Practice Theme Options →* Hero, Homepage Sections, Crisis & Safety, Footer, Page Links
   - *Footer*: the website credit, a small centered line at the very bottom of every page ("Developed & maintained by Russ Teicheira ↗"). You can turn it off, and change the wording, name, and link (it opens in a new tab). Blank wording falls back to "Developed & maintained by"; a blank name hides the line.
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
- Keyboard-operable mobile menu (`aria-expanded`, Escape closes it and returns focus to the toggle). It also closes when a menu link is followed (so same-page links don't leave it covering the section) or on a click outside it.
- Visible focus outlines everywhere, at least 3:1 against whatever background they're on (footer and crisis bar included); form field borders 3.9:1; touch targets of at least 44px.
- **Automatic contrast:** pick any colors. For each area (page, alternate sections, header, cards/forms, footer), the theme checks every text, heading, link, and focus-ring color against that area's background and darkens or lightens it, keeping the hue, until it meets WCAG AA (4.5:1 for text, 3:1 for focus rings). Button, footer, and hero text are set to white or near-black. Tested against deliberately bad combinations: all 15 pairings pass every time.
- Keep the hero overlay at 40% or higher when using a photo.
- `prefers-reduced-motion` is respected.
- Form fields have visible labels, `autocomplete` hints, and required-field markers; status messages are announced.

## Safety & privacy

- The footer on every page shows a crisis notice: 988 (Suicide & Crisis Lifeline), 911, and Crisis Text Line (text HOME to 741741), plus a note that the site isn't monitored for emergencies. It can't be turned off.
- An optional slim 988/911 bar can be shown above the header (on by default).
- All crisis wording is editable under Customize → Practice Theme Options → Crisis & Safety: the top bar, the footer heading, the footer resources (one per line; may include HTML links like `<a href="https://988lifeline.org">988lifeline.org</a>` plus bold/italics, and everything else is stripped), the footer note, and the contact form's safety note. Phone numbers typed there (988, 911, 741741, or full numbers like (555) 123-4567) become tap-to-call or tap-to-text links automatically. A field left blank shows the default wording, so crisis information can't be emptied out by accident.
- Two forms share one template (`template-parts/contact-form.php`) and one handler: the full form on the Contact page (name, email, optional phone, message), and a short call-back form in the homepage/Services "Ready to take the first step?" section (name, phone, what they're looking for). That section's button text and an optional **Call-back button link** are under Customize → Homepage: Sections. If a link is set (e.g. an online booking page), the button opens it in a new tab instead of showing the form. Both send to the email under **Practice Info**, falling back to the site admin email if none is set.
- **Spam protection**, always on:
  - **Honeypot:** a hidden field that only bots fill in.
  - **Signed timestamp in each form:** rejects anything submitted faster than a person can type. Unlike a WordPress nonce it never expires, so forms keep working on cached pages.
  - **Rate limit:** 5 messages per visitor per 10 minutes.
  - **Link check:** a message with more than 2 links is delivered with **[Possible spam]** at the start of the subject and a warning not to click the links. Messages are never dropped for this.
  - **Behind Cloudflare:** add `add_filter( 'annefpugh_trust_cloudflare_ip', '__return_true' );` so the limit uses the real visitor IP. Without it, the `CF-Connecting-IP` header is ignored, because anyone can fake it when the site isn't behind Cloudflare.
- **Cloudflare Turnstile or Google reCAPTCHA** (optional), under **Settings → Spam Protection**. The layout and wording match the russteicheira theme.
  - **Protect:** *Comment boxes* (comments are off on this site; this applies if they're turned back on), *Contact forms* (the Contact page and "Request a call back"), and *Log in, password reset, and registration* (also covers the password form on password-protected pages).
  - **Provider:** Turnstile (recommended) or reCAPTCHA v3 (invisible) / v2 (checkbox). Each keeps its own keys, so you can switch back and forth. Secrets are never shown again after saving.
  - **Checks:** a strictness score for reCAPTCHA v3; tokens must be issued for this site's domain (override with the `annefpugh_captcha_hostname` filter) and for the same form.
  - **Loading:** the provider's script only loads on pages with a protected form, and (except reCAPTCHA v2) only once a visitor starts filling it in.
  - **Contact messages are never lost.** If the visitor's browser blocks the check, or the provider can't be reached, the message is delivered with **[Unverified]** at the start of the subject. Only messages the provider positively flags are rejected.
  - **Login is stricter.** A login or password reset without a completed check is refused, so bots can't skip it. Only an unreachable provider lets a login through, so an outage can't lock you out. Application-password logins aren't affected. XML-RPC is switched off entirely (see *Hardening* below), so it can't be used to get around the check.
  - **Test first:** after saving, open the login page in a private window and make sure you can still log in.
  - **Locked out?** Add `define( 'ANNEFPUGH_SPAM_PROTECTION_OFF', true );` to `wp-config.php`, log in, fix the settings, then remove the line. While it's set, the settings page shows a warning that protection is switched off.
  - Both services receive visitor data, so mention the one you use in the Privacy Policy.
- **Other public forms** (`inc/form-guard.php`):
  - **Password-protected pages:** the page-password form gets the honeypot, signed timestamp, and the log-in Turnstile/reCAPTCHA check, plus a limit of 10 attempts per visitor per 10 minutes, so page passwords can't be guessed.
  - **Password reset:** at most 5 "Lost your password?" requests per visitor per hour, even with Turnstile/reCAPTCHA off, so nobody can flood your inbox with reset emails.
  - **Registration is always off.** The site has no member accounts, so "Anyone can register" is forced off and no spam accounts can be created.
  - **Search** is switched off (see *Hardening*), and **comments, pingbacks, and trackbacks** are closed (see above).
- **With Wordfence active,** the theme leaves log-in security to Wordfence. Wordfence covers log in, password reset and registration with brute-force limits, its own captcha and 2FA.
  - **Turned off:**
    - the log-in, password-reset and registration Turnstile/reCAPTCHA check
    - the password-reset rate limit
    - the page-password guard
  - **Still on:**
    - the contact-form guard (honeypot, timestamp, rate limit, link check, and Turnstile/reCAPTCHA), because Wordfence doesn't protect theme forms
    - forced-off registration
    - everything under *Hardening*
  - **Settings screen:** Settings → Spam Protection shows a notice while this is in effect.
  - **Active, not just installed:** an installed but deactivated Wordfence protects nothing, so the theme's own checks stay on.
  - **Keeping the theme's checks anyway:** add `add_filter( 'annefpugh_defer_to_wordfence', '__return_false' );`.
- **Adding a form later:** every new public form must use `inc/form-guard.php`. Print `annefpugh_form_guard_fields( 'my-form' )` inside it and `annefpugh_captcha_field_html( 'contact' )` before the submit button. Call `annefpugh_form_guard_check( 'my-form' )` first in its handler, then `annefpugh_form_guard_record( 'my-form' )` once accepted. The steps are also in the file's header.
- Field lengths are enforced on the server, and failed emails are logged to the PHP error log.
- The contact forms ask visitors not to send sensitive health details. It sends via `wp_mail()` and stores nothing in the database. It's not a HIPAA-compliant intake — use a proper client portal for that.
- No external fonts or scripts are loaded, so visitors' data isn't sent to third parties.
- **Comments are off everywhere** (`inc/disable-comments.php`):
  - closed on all content, with new content defaulting to closed
  - comment form handler, REST API comment routes, XML-RPC pingbacks and comment methods, and the `X-Pingback` header all removed
  - comment feeds redirect home
  - Recent Comments widget removed
  - Comments and Settings → Discussion screens hidden, and their direct URLs redirect to the Dashboard

  Saved WordPress settings aren't changed, so switching themes restores the defaults.
- **Hardening** (`inc/hardening.php`):
  - **XML-RPC is off.** `xmlrpc.php` answers with 403, and no methods are available, so bots can't use it to guess passwords around the login check. Nothing on this site uses it: the block editor uses the REST API. If a service ever needs XML-RPC (e.g. the Jetpack app), it will stop working until this is removed.
  - **Your username stays private:**
    - the REST user list (`/wp-json/wp/v2/users`) is only available when logged in
    - `?author=1` and `/author/…` pages return 404
    - oEmbed data has no author
    - there's no users sitemap
    - the login page says "username, email address, or password is incorrect" without saying which
    - the "Lost your password?" form shows the same "Check your email" page whether or not the account exists (an email is only sent for a real one)
  - **No blog pages:** search results and author, category, tag and date archives return a proper 404, and `/feed/` redirects to the homepage. A no-blog site has nothing to show there.
