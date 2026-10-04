# Code review: Single Provider Therapy theme

> **Status (2026-10-03):** every finding below has been addressed in theme version 1.1.0. Details are in the [Resolution log](#resolution-log) at the end.

- **Date:** 2026-10-03
- **Scope:** every file in the theme (`/home/crossfire/wp-annefpugh`, ~3,700 lines: PHP, JS, CSS) at theme version 1.0.3, including uncommitted work.
- **Method:** I read every file. On the dev container I checked rendered HTML, made direct requests to theme files, measured color contrast with the WCAG formula, and searched mechanically for unused functions, CSS classes and template parts.

## Summary

There are no critical or high-severity issues. Output escaping, input sanitization, capability checks and nonces are applied consistently, and the riskiest paths (contact form, admin screens) hold up. The most important findings are:

1. **Accessibility gaps against the theme's own WCAG goal.** The keyboard focus ring is nearly invisible on the dark footer and crisis bar. Form input borders are too faint. The "automatic contrast" protection only covers button, footer and hero text, so link text, headings and section text can be made unreadable through color choices.
2. **Contact form robustness.**
   - With reCAPTCHA off there's no rate limit, so the form can be used to flood the practice inbox.
   - Forms will start failing on cached pages after about a day.
   - With reCAPTCHA v3 on, visitors who block Google can never send a message.
3. **Deployment.** `deployafp.sh` publishes the README, and would publish this audit, to the public web. It also never removes deleted files.

| Severity | Count |
|---|---|
| Critical / High | 0 |
| Medium | 7 |
| Low | 16 |
| Info / housekeeping | 8 |

---

## Medium

### M1. Focus indicator is nearly invisible on the footer and crisis bar
`assets/css/main.css:85`

The global `:focus-visible` outline uses `--color-primary`. With the default colors, the outline is 2.2:1 against the footer and crisis bar background (`#1b2a4a`), below the 3:1 that WCAG 2.2 (1.4.11 Non-text Contrast / 2.4.7 Focus Visible) requires. A keyboard user tabbing through the footer — which includes the 988/911 links — can lose track of where they are. This depends on the colors chosen: any primary color close to the footer color makes it worse.

**Fix:** inside dark areas, use the computed footer text color for the outline, e.g. `.site-footer :focus-visible, .crisis-bar :focus-visible { outline-color: var(--color-footer-text); }`. A two-tone ring (outline plus a contrasting box-shadow) also works on any background.

### M2. Form input borders fail non-text contrast
`assets/css/main.css:708`

`border: 1px solid rgba(27, 42, 74, 0.45)` on white measures **2.66:1**, below the 3:1 required for the outline of a form control (WCAG 1.4.11). Low-vision users may not see where the fields are.

**Fix:** use a solid border of at least 3:1, e.g. `#767676` or the body text color at around 60% opacity, and re-measure.

### M3. "Automatic contrast" only covers some color pairings
`inc/custom-css.php`, `assets/css/main.css:76, 169, 173–179, 204, 320, 469, 575, 710`

The README and the Customizer suggest colors are kept legible automatically. In fact only three pairings are computed: text on buttons, footer text, and text over the hero overlay. Not covered:

- **`--color-primary` is also the link color** for in-text links, "More about my approach" and "Get directions", all on white. A light brand color such as `#f5d000` gives **1.51:1** link text, against 4.5:1 needed.
- **`--color-secondary` is used as text color** for the "Meet your therapist" eyebrow label, and nothing checks it.
- **`--color-text` / `--color-heading` on `--color-surface`:** a dark surface color makes section text unreadable. Text color isn't derived from the surface color.
- **Not adjustable at all**, despite the "all colors adjustable" requirement:
  - the header background
  - the mobile menu panel
  - card, form-card and input backgrounds
  - borders and shadows
  - success and error notice colors

  These are all hardcoded `#ffffff` or navy values.

**Fix:**
- Add a computed `--color-link`: use the primary color if it reaches 4.5:1 against the background, otherwise a darkened version of it.
- Compute eyebrow and surface text colors the same way.
- Expose header and card backgrounds as Customizer colors, with computed text colors.
- Change the Customizer wording so it doesn't over-promise.

### M4. No rate limit on the contact form
`inc/contact-form.php:25–81`

With reCAPTCHA off, which is the default, the only defenses are the honeypot and a WordPress nonce:
- For logged-out visitors the nonce is identical for everyone and valid for 12–24 hours.
- A script can fetch the page once and then submit thousands of messages, each sent by `wp_mail()` to the practice.

That floods the therapist's inbox, could get the server's mail flagged as spam, and buries real inquiries.

**Fix:** a per-IP transient limit, for example 5 submissions per 10 minutes. On a site behind Cloudflare, read the IP from `HTTP_CF_CONNECTING_IP` with validation, as on russteicheira. Also consider turning reCAPTCHA on by default once keys exist.

### M5. Forms break on cached pages
`template-parts/contact-form.php:60`

The nonce is printed into the page HTML. If production uses full-page caching (e.g. Endurance Page Cache, as on russteicheira, or a Cloudflare HTML cache), visitors get a cached copy whose nonce expires after 12–24 hours. Every submission after that fails with "Sorry, your message couldn't be sent". This would happen silently, with no error visible to the practice.

**Fix:** either exclude pages with forms from the cache, or drop the nonce for this logged-out form. The honeypot, reCAPTCHA and rate limiting provide the actual protection; a nonce doesn't protect logged-out forms much. Alternatively, fetch a fresh nonce over AJAX when the form is first focused.

### M6. reCAPTCHA v3 permanently blocks visitors who block Google
`assets/js/recaptcha.js:56`, `inc/recaptcha.php` (`annefpugh_recaptcha_verify`)

If Google's script can't load (privacy extensions, strict tracking protection, some corporate networks), the browser sends the form without a token, and the server rejects any missing token as spam. Those visitors can never use either form. The design intent was the opposite: never lose a real inquiry, and only let messages through when Google itself is down. The "please call" message softens this, but someone hesitant to phone a therapist may simply give up.

**Fix:** treat "no token" differently from "token with a low score". Let tokenless submissions through, mark the email subject `[unverified]`, and rely on the honeypot and rate limit (M4). Keep rejecting low-score and invalid tokens.

### M7. The deploy script publishes non-theme files and never removes old ones
`/home/crossfire/scripts/deployafp.sh` (outside the theme, but it determines what ends up public)

- `cp -afr /home/crossfire/wp-annefpugh/*` copies **`README.md`, `LICENSE`, and `audits/`** into the web-served theme folder. On the dev container, `README.md` is publicly readable now at `/wp-content/themes/wp-annefpugh/README.md`, and it describes the theme's internals. **This audit, which lists the site's weaknesses, would be published the same way.**
- `cp` never deletes, so files removed from the theme stay on the server. The rebuild removed 9 old template files that would still be live after a script deploy.

**Fix:** use `rsync -a --delete` with exclusions for `.git`, `audits/` and `*.md`, or deploy from `git archive`. A temporary workaround is to keep `audits/` outside the theme directory.

> **Status: fixed 2026-10-03.**
>
> - `deployafp.sh` now uses `rsync --archive --delete --delete-excluded --chown=www-data:www-data`.
> - It excludes `.git`, `audits/`, `*.md`, `.env*` and editor/OS junk files.
> - Before running it checks that the source is a theme (`style.css` + `functions.php`) and that the destination is the expected path.
> - A new `-n` option previews changes. `-z` still makes a backup first.
>
> The next real run deletes the `README.md` currently published on the dev volume and the stale files listed in this finding. Tested against a scratch copy of the server folder.

---

## Low

| # | Where | Issue | Fix |
|---|---|---|---|
| L1 | `template-parts/home/about.php:21–26` | **Visible bug:** the heading renders as "Anne F. Pugh **,** LCSW" with a stray space before the comma. The line break between the name and the credentials `<span>` becomes a space. Confirmed in the rendered HTML. | Put the name and `<span>` on one line with no whitespace between them, or build the string in PHP. |
| L2 | `inc/contact-form.php:78` | `Reply-To: Name <email>` breaks if the name contains a comma ("Smith, Jane"). `wp_mail()` splits Reply-To on commas, so the reply address can come out mangled. | Remove commas and quotes from the name, or put the name in quotes. |
| L3 | `inc/contact-form.php:46–49` | `maxlength` (100/150/30/2000) is only enforced in the browser. A script can send a multi-megabyte message, which gets emailed. | Truncate on the server with `mb_substr()` to the same limits. |
| L4 | `template-parts/contact-form.php:62–65` | The honeypot field is named and labelled "Website". Some browser and password-manager autofill ignores `autocomplete="off"` and fills it. The real message is then **silently thrown away while the visitor sees "Thank you"**. | Use a non-semantic name and label (e.g. `afp_hp_2`), add `autocomplete="new-password"`, and consider logging honeypot hits. |
| L5 | `inc/schema.php:53` | `JSON_UNESCAPED_SLASHES` lets a `</script>` inside any value end the JSON-LD block. Today every value is sanitized before it gets here, so this isn't exploitable. Defense in depth only. | Drop that flag or add `JSON_HEX_TAG`. |
| L6 | `template-parts/home/hero.php:11`, `about.php:12` | If the hero image or therapist photo is deleted from the Media Library, its ID stays saved. The hero then shows a dark overlay with no image, and the about section shows an empty space instead of the placeholder. | Check that `annefpugh_image()` returned markup before adding `hero--has-image` or skipping the placeholder. |
| L7 | `inc/customizer.php:148` | The help text says the safety note is "Shown above the form on the Contact page". It's also shown in the call-back form on the homepage and Services page. | Update the wording. |
| L8 | `inc/admin-practice-info.php:185, 189` | `trim( $raw )` throws a PHP 8 TypeError if a field arrives as an array (`email[]=…`). Admin-only and nonce-protected, so it can only crash your own save. | Cast to string, or skip non-string values. |
| L9 | `inc/recaptcha.php` (`annefpugh_recaptcha_verify`) | Google's `hostname` in the response isn't checked. If "Verify the origin of reCAPTCHA solutions" is ever turned off in Google's console, tokens issued for other sites would be accepted. | Compare `$result['hostname']` with the site's domain. |
| L10 | `assets/js/recaptcha.js:36–46` | After submitting, `data-verified="1"` and the disabled button persist. If the browser restores the page from its back/forward cache, a second submit sends the old, used token, which is rejected as spam. | Reset both on the `pageshow` event, or clear `verified` after `form.submit()`. |
| L11 | `inc/template-functions.php:101` | The phone-number linker also matches numbers after `$`, e.g. "$988". Unlikely in crisis wording, but possible. | Also exclude `$` in the lookbehind. |
| L12 | `footer.php:52–68`, `header.php:39` | With no legal menu and no Privacy Policy page set, the footer shows an empty "Policies" navigation area. With no primary menu and no Contact page, the mobile "Menu" button opens an empty panel. | Only output each when it has content. |
| L13 | `page-templates/template-services.php:32` | Service content runs through `apply_filters( 'the_content' )` while the global `$post` is still the Services page. Blocks and shortcodes that read the current post (e.g. post-title, post-date) show the page's data. | Use `setup_postdata()` and `wp_reset_postdata()` around each service. |
| L14 | `inc/setup.php:52` | `callback-toggle.js` loads on every page, although only the homepage and Services page have the call-back section. | Enqueue it from `template-parts/home/cta.php`. |
| L15 | `template-parts/**`, root templates | Template files have no `ABSPATH` guard. A direct request returns a bare HTTP 500; no path or error text leaked on the dev container. Hardening only. | Add `defined( 'ABSPATH' ) \|\| exit;`. |
| L16 | `inc/contact-form.php:31–33, 80` | An expired nonce shows the same generic "couldn't be sent" message as a mail failure, and `wp_mail()` failures aren't logged. Hard to tell apart and diagnose in production. | Use distinct statuses, and `error_log()` mail failures; the `wp_mail_failed` action gives the reason. |

---

## Info / housekeeping

- **Unused code:** none found. Every PHP function is referenced, every CSS class in `main.css` and `admin.css` is used (the `notice--*` classes are built dynamically), and every `get_template_part()` target exists.
- **Translations:** `load_theme_textdomain()` points at a `languages/` folder that doesn't exist. The admin JS uses `wp.i18n` without `wp_set_script_translations()`. Harmless while the site is English-only.
- **No `screenshot.png`:** the theme shows a blank thumbnail under Appearance → Themes. Add a 1200×900 screenshot.
- **Manual cache-busting:** `ANNEFPUGH_VERSION` must be bumped by hand for browsers to pick up CSS/JS changes, and it has been forgotten once already. Consider `filemtime()`-based versions for theme assets.
- **Services in the REST API:** published services are readable at `/wp-json/wp/v2/annefpugh_service` because the post type uses `show_in_rest` (needed for the block editor). That's the same content as the public Services page, so it isn't a leak. Drafts aren't exposed.
- **Comments disabled incompletely:** support, `comments_open` and the admin menu are off, but comment feed links and the admin-bar comments item remain. Cosmetic.
- **Schema:** the whole address goes into `streetAddress`. Separate city/state/ZIP fields in Practice Info would give richer local-search data.
- **Images forced decorative:** service images on cards and on the Services page always get `alt=""`. That's fine while they're decorative; if a service image ever carries meaning, its Media Library alt text is ignored.

---

## Checked and sound

- **Escaping:** every dynamic value is escaped with the right function for where it's printed. The three places marked `phpcs:ignore` are genuinely safe (core functions that escape themselves, or `annefpugh_linkify_phone_numbers()`, which escapes before linking).
- **Input handling:** every value from forms or the URL is unslashed and sanitized by type. Customizer colors pass `sanitize_hex_color`, so the CSS printed into the page can't be injected into.
- **Permission checks:** every admin save checks the user's capability and a nonce. The Practice Info, service order and reCAPTCHA screens each check permissions both when showing the screen and when saving.
- **Redirects:** all use `wp_safe_redirect()`, and the return address comes from `wp_get_referer()` (same site only), so the forms can't be used to bounce visitors to another site.
- **Email header injection:** not possible. `sanitize_text_field()` strips line breaks from name and phone, and email is checked by `sanitize_email()` and `is_email()`.
- **Service reordering:** the save ignores any submitted ID that isn't a service, and checks edit permission for each one (verified in a rolled-back transaction).
- **Secrets:** the reCAPTCHA secret is never printed back; leaving the field blank keeps the saved one.
- **Crisis information:** it can't be emptied, because blank fields fall back to the defaults. Every number becomes a tap-to-call or tap-to-text link, and visitor text is escaped before linking.
- **Accessibility basics present:** skip link, landmark regions, one `h1` per page, labelled forms with `autocomplete`, `aria-expanded` disclosures with Escape support, reduced-motion support, and 44px touch targets.

## Not verified

There's no browser on the build host, so these have only been reviewed as code, never run:
- mobile menu
- call-back form opening and closing
- Practice Info photo picker
- drag-and-drop service ordering
- reCAPTCHA v3 getting a token on submit

Check them once in a real browser, including keyboard-only and with a screen reader.

---

## Resolution log

All changes were deployed to the dev container and tested there. Tests that needed different settings changed them in memory only; tests that save data ran inside a database transaction that was rolled back. Real practice data was left untouched.

| ID | Resolution | Verified by |
|---|---|---|
| M1 | Focus outlines use `--color-focus` (≥ 3:1 for each area). Inside the footer and crisis bar they switch to the footer text color, and on photo headers to the overlay text color. | Contrast stress test |
| M2 | Field border set to `#7a8194`: 3.89:1 on white, 3.54:1 on the default alternate section color. Fields are always white with `#1b2a4a` text. | Measured |
| M3 | New `annefpugh_accessible_color()` darkens or lightens a color, keeping its hue, until it meets the target ratio.<br>• Per-area variables: text, heading and link for the page, alternate sections, header and cards; focus color for each area that has its own background (cards keep the surrounding focus color, since their outline is drawn outside the card).<br>• Links and focus rings are re-pointed per area with CSS variable scoping.<br>• Header and card/form backgrounds are now Customizer colors.<br>• The Colors section help text was rewritten. | 8 color scenarios, including an "everything awful" set: all 15 text/background pairings pass in every one |
| M4 | Rate limit of 5 messages per visitor per 10 minutes, counted in a transient keyed by a salted hash of the IP. Cloudflare's IP header is used only after opting in with the `annefpugh_trust_cloudflare_ip` filter. New "busy" message that offers the phone number. | 6th send from one IP → `busy` |
| M5 | Nonce replaced with a signed timestamp (`time.hmac`, keyed with `wp_salt('nonce')`). It never expires, so cached pages keep working. Submissions under 3 seconds → `spam`; a missing or forged token → `expired` ("reload and try again"). | Instant submit → `spam`; forged or missing token → `expired`; 10 s old → `sent` |
| M6 | `annefpugh_recaptcha_verify()` now returns `pass` / `fail` / `unverified`. A missing token (Google blocked) or a Google outage → delivered with an `[Unverified]` subject and an explanatory note. Only a `fail` is rejected. | v3 on with no token → sent as "[Unverified] Call-back request…" |
| M7 | `deployafp.sh` rewritten: `rsync --delete --delete-excluded`, excludes `.git` / `audits/` / `*.md`, safety checks, `-n` dry run (see status note under M7). | Scratch-copy test; README and audits return 404 on the dev site |
| L1 | Name and credentials are printed with no whitespace in between. | Renders "Anne F. Pugh, LCSW" |
| L2 | The Reply-To name is cleaned of `" , ; < >` and extra whitespace is collapsed. | `Smith, Jane "JJ"` → `Reply-To: Smith Jane JJ <…>` |
| L3 | Server-side limits enforced with `mb_substr()`: name 100, email 150, phone 30, message 2000 characters. | 5,000-character message → 2,000 |
| L4 | Spam-trap field renamed `afp_extra_field`, labelled "Leave this field empty", with `autocomplete="new-password"`. Hits are logged when `WP_DEBUG` is on. | Filled field → silently `sent`, no email |
| L5 | JSON-LD is encoded with `JSON_HEX_TAG \| JSON_HEX_AMP`. | Rendered schema |
| L6 | The hero and about sections render the image first, and only add `hero--has-image` / skip the placeholder if markup actually came back. | Code path |
| L7 | Customizer help text now names both forms. | Code review |
| L8 | Practice Info only accepts string values; arrays are treated as empty. | Array POST (rolled back) → saves with no fatal error |
| L9 | reCAPTCHA checks `hostname` against the site's domain (filter: `annefpugh_recaptcha_hostname`). | Google test key (hostname `testkey.google.com`) → `fail`; with the filter → `pass` |
| L10 | `recaptcha.js` resets the token, `verified` flag and button on a `pageshow` event restored from the back/forward cache. | Code review (no browser) |
| L11 | The phone linker skips numbers preceded by `$`. | `$988` left alone; `988` linked |
| L12 | The Policies footer nav is only output when there's a legal menu or Privacy Policy page. The header menu button and nav are only output when there's a menu, Contact page or phone number. | Code path |
| L13 | The Services template uses `global $post` + `setup_postdata()` / `wp_reset_postdata()`, and `the_title()` / `the_content()`. | Each service shows its own title; the page title is restored afterwards |
| L14 | `callback-toggle.js` is registered in `setup.php` and enqueued from `cta.php`. | Loaded on the homepage and Services template; not on Contact or legal pages |
| L15 | `defined( 'ABSPATH' ) \|\| exit;` added to every PHP file. | `grep -L ABSPATH` → none missing |
| L16 | New `expired` and `busy` statuses. `wp_mail_failed` is logged during the send. | Status messages render |
| Info: translations | `languages/annefpugh.pot` generated (241 strings) with WP-CLI, run temporarily in the container and removed afterwards. `wp_set_script_translations()` added for the service-order script. | File present |
| Info: screenshot | `screenshot.png` (1200×900) drawn with GD in the default palette. | Viewed |
| Info: cache-busting | `annefpugh_asset_version()` uses each file's modification time, and every enqueue uses it. | `main.css?ver=<mtime>` in the page |
| Info: REST | No change: published services are public on the Services page anyway, and the block editor needs REST. | — |
| Info: comments | Comment feed links and the admin-bar comments item removed. | No comment feed links in `<head>` |
| Info: schema address | `annefpugh_schema_address()` splits a last line like "Berkeley, CA 94707" into city, state and ZIP. Anything else is kept whole. | 3 sample addresses |
| Info: decorative images | The Services page now uses Media Library alt text. Cards keep `alt=""`, since the title follows. | Code review |

### Still needs a real browser
There's no browser on the build host. Still to check by hand:
- mobile menu
- call-back form opening and closing
- photo picker
- drag-and-drop service ordering
- the reCAPTCHA v3 token and back/forward-cache reset
- keyboard and screen-reader use
- a visual check of the per-area link and focus colors
