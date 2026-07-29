# Black Professionals Ireland — WordPress Theme

A custom WordPress theme for **blackprofessionals.eu** (the Black Professionals Ireland subsidiary site), built on the shared BPU Navy (`#001b69`) & Red (`#cc0000`) brand used across the rest of this org's sites.

## Requirements

- WordPress
- **Advanced Custom Fields** (free or Pro) — almost all page content (hero text, member/partner benefit cards, team members, events, tier pricing) is editable through ACF fields registered by this theme. Without ACF active, pages still render using built-in fallback text, but editors won't be able to change the copy from wp-admin.

## Setup checklist

1. **Activate the theme** (Appearance → Themes).
2. **Install & activate ACF.**
3. **Create these pages** in Pages → Add New, and set each one's *Page Attributes → Template* as noted:
   | Page | Template |
   |---|---|
   | Home | (mark as the site's static front page in Settings → Reading) |
   | About | About — a lightweight hub page; it just links to its children (see below) |
   | Our Story | Our Story (set as child of About) — carries the actual mission/purpose/impact content |
   | Our Team | Our Team (set as child of About) |
   | Events | Events |
   | Contact | Contact |
   | Membership | Membership |
   | Partnership | Partnership |
   | Ambassadorship | Ambassadorship |
   | Imprint | Imprint |

   The About page automatically lists whichever pages are set as its children (via `get_pages`), so once Our Story and Our Team are children of About, no extra linking is needed.
4. **Build the menus** in Appearance → Menus:
   - **Primary Menu**: Home, About (with Our Story / Our Team nested under it), Events, Contact, Membership, Partnership, Ambassadorship.
   - **Footer Menu**: Imprint (and any other legal links, e.g. Privacy Policy, once created).
5. **Fill in Theme Settings** (its own admin menu item once ACF is active) — it now has five tabs:
   - **General**: legal entity name, address, contact email/phone, social links, Privacy Statement page link. Feeds the footer, Imprint, and Contact pages.
   - **Notifications**: where "new submission" emails go for all four forms (defaults to the Contact Email above if left blank; accepts a comma-separated list).
   - **Captcha**: pick **None** (default), **hCaptcha**, or **Google reCAPTCHA v2**, then fill in that provider's site/secret keys. Whichever is chosen applies to all four forms at once.
   - **SMTP**: off by default (uses the server's normal mail sending). Turn on and fill in host/port/username/password/encryption to route mail through a real SMTP provider. "From Email"/"From Name" apply either way, so you can fix the sender address even with SMTP off.
   - **Form Messages**: the exact success/error (and captcha-error) wording shown to visitors after submitting each form — edit any of these without touching code.
6. **Add events** under the new *Events* menu item in wp-admin (each event has a date, time, location, and registration link).
7. **Upload a logo** in Appearance → Customize → Site Identity — the crop/resize tool there is built into WordPress core (enabled via this theme's `add_theme_support( 'custom-logo', ... )`), so any uploaded image can be repositioned/cropped before saving.
8. **Review submissions**: *Membership Applications*, *Ambassador Applications*, and *Form Submissions* (Contact + Partnership) all appear as their own admin-only menu items in wp-admin.

### About the forms

- **Contact** page: name/email/subject/message, secondary to the Address block. Stored as a `bpu_form_submission` post (form_type = contact) plus a best-effort notification email.
- **Membership** page: a full application form (name, contact details, immigration/demographic questions, CV upload, volunteering interest, required consent checkbox) matching the live blackprofessionals.eu member sign-up form. Stored as a private `bpu_member_app` post.
- **Partnership** page: a short "Explore Partnership" enquiry form (organisation, contact, job title, email, message). Stored as a `bpu_form_submission` post (form_type = partnership) — no sensitive data, so a generic post type is enough.
- **Ambassadorship** page: a "Become an Ambassador" application form (contact details, nearest city, ethnicity, current status, CV/cover letter upload), reusing the same choice lists as the membership form. Stored as a private `bpu_ambassador_app` post.

Every form now shares the same underlying wiring (`inc/captcha.php`, `inc/smtp.php`, `inc/template-tags.php` → `bpu_ie_notification_recipients()`):

- **Storage is what determines success/failure** for the visitor — the notification email is best-effort, so a slow or misconfigured mail server never turns a successfully-recorded submission into a user-facing error.
- **Captcha** (`bpu_ie_render_captcha()` / `bpu_ie_verify_captcha()`) is provider-agnostic — set the provider once in Theme Settings and every form picks it up. Skipped automatically when set to None or when a provider's keys aren't both filled in.
- **Dropdown option lists** (industry, nearest city, ethnicity, current status, etc.) are all defined in one place: `inc/membership-form.php` → `bpu_ie_membership_form_choices()`. Edit that array directly to add/remove/rename options — the ambassador form reuses it too.
- **Sensitive data**: the membership and ambassador forms collect special-category personal data (ethnicity, sexuality, immigration status). It's stored only in their respective private post types (never emailed in plaintext) — but retention/access policy is still up to the org to define under GDPR; this theme doesn't auto-delete old applications.
- The membership form's consent checkbox links to whatever URL is set in Theme Settings → General → *Privacy Statement & Consent Page* (falling back to the site's built-in Privacy Policy page if set).

## Structure

- `style.css` — theme header + all site styling (brand tokens at the top).
- `functions.php` — theme setup, menu registration, asset enqueue.
- `inc/acf-fields.php` — all editable field definitions, organised by page, including the five-tab Theme Settings options page.
- `inc/custom-post-types.php` — the Events post type, the private Membership/Ambassador Applications post types, and the generic Form Submissions post type (+ its `bpu_ie_store_form_submission()` helper).
- `inc/captcha.php` — provider-agnostic captcha rendering/verification (hCaptcha or reCAPTCHA v2), shared by all four forms.
- `inc/smtp.php` — SMTP + From-address configuration for `wp_mail()`, driven by Theme Settings.
- `inc/contact-form.php` — native contact form (render + handler).
- `inc/membership-form.php` — the "Become a Member" application form, shared choice lists, submission handler, and CV upload handling.
- `inc/partnership-form.php` — the "Explore Partnership" enquiry form.
- `inc/ambassador-form.php` — the "Become an Ambassador" application form.
- `inc/template-tags.php` — shared helpers (hero, tier grid, CTA band, social icons, notification recipients).
- `page-templates/` — one file per custom page template.

## Content source

Home page and Imprint page copy were ported from the live blackprofessionals.eu site (shared directly by the team, since the Wayback Machine snapshot wasn't reachable from this environment) and adapted from "Europe" to "Ireland" wording where appropriate. The Imprint page correctly keeps the actual registered legal entity, **Black Professionals Europe e. V.** (Neuss, Germany), since that's the entity that legally operates the site.
