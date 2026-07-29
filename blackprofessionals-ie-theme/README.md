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
   | About | About |
   | Our Story | Our Story (set as child of About) |
   | Our Team | Our Team (set as child of About) |
   | Events | Events |
   | Contact | Contact |
   | Membership | Membership |
   | Partnership | Partnership |
   | Ambassadorship | Ambassadorship |
   | Imprint | Imprint |
4. **Build the menus** in Appearance → Menus:
   - **Primary Menu**: Home, About (with Our Story / Our Team nested under it), Events, Contact, Membership, Partnership, Ambassadorship.
   - **Footer Menu**: Imprint (and any other legal links, e.g. Privacy Policy, once created).
5. **Fill in Theme Settings** (its own admin menu item once ACF is active): legal entity name, address, contact email/phone, social links, and (under the *Forms* tab) your Privacy Statement page link and hCaptcha keys. These feed the site footer, the Imprint page, and the membership form automatically.
6. **Add events** under the new *Events* menu item in wp-admin (each event has a date, time, location, and registration link).
7. **Upload a logo** in Appearance → Customize → Site Identity.
8. **Review applications** under the new *Membership Applications* and *Ambassador Applications* menu items in wp-admin — each submission is stored there (private, admin-only) along with any uploaded CV.

### About the forms

- **Membership** page: a full application form (name, contact details, immigration/demographic questions, CV upload, volunteering interest, required consent checkbox) matching the live blackprofessionals.eu member sign-up form.
- **Partnership** page: a short "Explore Partnership" enquiry form (organisation, contact, job title, email, message) — this one has no sensitive data, so it's simply emailed to the Theme Settings contact address, nothing is stored in the database.
- **Ambassadorship** page: a "Become an Ambassador" application form (contact details, nearest city, ethnicity, current status, CV/cover letter upload), reusing the same choice lists as the membership form.

A few notes that apply across the membership and ambassador forms:

- **hCaptcha is optional** (membership form only). Leave the site/secret key fields in Theme Settings blank and the form works without a captcha; fill both in and the widget + server-side verification switch on automatically.
- **Dropdown option lists** (industry, nearest city, ethnicity, current status, etc.) are all defined in one place: `inc/membership-form.php` → `bpu_ie_membership_form_choices()`. Edit that array directly to add/remove/rename options — the ambassador form reuses it too.
- **Sensitive data**: the membership and ambassador forms collect special-category personal data (ethnicity, sexuality, immigration status). It's stored only in their respective private post types (never emailed in plaintext) — but retention/access policy is still up to the org to define under GDPR; this theme doesn't auto-delete old applications.
- The membership form's consent checkbox links to whatever URL is set in Theme Settings → Forms → *Privacy Statement & Consent Page* (falling back to the site's built-in Privacy Policy page if set).

## Structure

- `style.css` — theme header + all site styling (brand tokens at the top).
- `functions.php` — theme setup, menu registration, asset enqueue.
- `inc/acf-fields.php` — all editable field definitions, organised by page.
- `inc/custom-post-types.php` — the Events post type and the private Membership/Ambassador Applications post types.
- `inc/contact-form.php` — native contact form handler (no form-plugin dependency).
- `inc/membership-form.php` — the "Become a Member" application form, shared choice lists, submission handler, and CV upload handling.
- `inc/partnership-form.php` — the "Explore Partnership" enquiry form.
- `inc/ambassador-form.php` — the "Become an Ambassador" application form.
- `inc/template-tags.php` — shared helpers (hero, tier grid, CTA band, social icons).
- `page-templates/` — one file per custom page template.

## Content source

Home page and Imprint page copy were ported from the live blackprofessionals.eu site (shared directly by the team, since the Wayback Machine snapshot wasn't reachable from this environment) and adapted from "Europe" to "Ireland" wording where appropriate. The Imprint page correctly keeps the actual registered legal entity, **Black Professionals Europe e. V.** (Neuss, Germany), since that's the entity that legally operates the site.
