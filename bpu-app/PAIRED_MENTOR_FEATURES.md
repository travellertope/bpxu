# PAIRED Mentor Platform — Comprehensive Feature Specification

> Derived from deep analysis of the legacy CodeIgniter PAIRED codebase and adapted for the new Next.js + WordPress headless architecture.

---

## Table of Contents

1. [Mentor Profile & Settings](#1-mentor-profile--settings)
2. [Mentor Dashboard](#2-mentor-dashboard)
3. [Session Management](#3-session-management)
4. [Booking Lifecycle](#4-booking-lifecycle)
5. [Availability & Schedule](#5-availability--schedule)
6. [Mentee Management](#6-mentee-management)
7. [Messaging](#7-messaging)
8. [Work Experience & Education](#8-work-experience--education)
9. [Skills & Expertise](#9-skills--expertise)
10. [Reviews & Ratings](#10-reviews--ratings)
11. [Notifications](#11-notifications)
12. [Mentor Application Flow](#12-mentor-application-flow)
13. [Mentor Discovery & Search](#13-mentor-discovery--search)
14. [Admin Features](#14-admin-features)
15. [Mentee-Side Features](#15-mentee-side-features)
16. [Data Model](#16-data-model)
17. [API Endpoints](#17-api-endpoints)
18. [Page Map](#18-page-map)
19. [Build Priority](#19-build-priority)

---

## 1. Mentor Profile & Settings

**Route:** `/paired/mentor/settings`

### 1.1 Personal Information

| Field | Type | Notes |
|-------|------|-------|
| First name | text | Required |
| Last name | text | Required |
| Email | text | Read-only (from WP account) |
| Phone number | text | Optional |
| Gender | select | Male / Female / Non-binary / Prefer not to say |
| Location / Residence | text | City or region in the UK |
| Profile photo | image upload | Replaces Gravatar; stored on Cloudflare R2 |
| About me / Bio | textarea | Up to 2000 chars; displayed on public profile |

### 1.2 Professional Information

| Field | Type | Notes |
|-------|------|-------|
| Industry | select | From predefined list (Banking & Financial Services, Technology, etc.) |
| Field of expertise | text | Subfield within industry |
| Current role / Job title | text | e.g. "Senior Product Manager" |
| Company | text | Current employer |
| Employment status | select | Full-time, Part-time, Self-employed, Freelance, Student, Unemployed |
| Years of experience | number | 1–30+ |
| Level of education | select | GCSE, A-Level, Bachelor's, Master's, PhD, Professional qualification |
| Skills | tag input | Multiple; from predefined list + free-text |

### 1.3 Mentorship Settings

| Field | Type | Notes |
|-------|------|-------|
| Mentorship availability | select | Once a month, Twice a month, Once every 2 months |
| Mentees at once | number | 1–6; capacity cap |
| Mentorship requirements | textarea | What mentor expects from mentees |
| Meet type | select | Google Meet (auto-generated) / Custom link |
| Meeting URL | url | Fallback custom link; ignored when Google Meet is auto-generated |

### 1.4 Social & Links

| Field | Type | Notes |
|-------|------|-------|
| LinkedIn | url | |
| X (Twitter) | url | |
| Instagram | url | |
| Facebook | url | |
| Portfolio / Website | url | |

### 1.5 BP Network

| Field | Type | Notes |
|-------|------|-------|
| BP Network | select | Black Professionals UK / Europe / Ireland / Australia |

### 1.6 UI Behaviour

- Grouped into collapsible sections (Personal, Professional, Mentorship, Social)
- Save button per section or single "Save all" at bottom
- Success/error toast on save
- Unsaved changes warning on navigate away

---

## 2. Mentor Dashboard

**Route:** `/paired/dashboard` (when `user.roles.includes('mentor')`)

### 2.1 Stats Cards (top row)

| Stat | Source | Icon |
|------|--------|------|
| Pending requests | Count of bookings with status `pending` where user is mentor | Clock |
| Upcoming sessions | Bookings with status `confirmed` and date >= today | Calendar |
| Total sessions completed | Bookings with status `completed` | Check |
| Total mentees | Distinct mentee IDs from all bookings | Users |

### 2.2 Pending Booking Requests

- Table/card list of bookings with status `pending`
- Each row shows: mentee name, mentee avatar, session date, time slot, notes
- Action buttons: **Accept** / **Decline**
- Accept changes status to `confirmed` and sends email to mentee
- Decline changes status to `cancelled` and sends email to mentee

### 2.3 Upcoming Sessions

- List of confirmed bookings with date >= today, sorted by date ascending
- Shows: mentee name, date, time, meeting link (if set)
- Action: **Mark as completed** (after session date passes)
- Action: **Cancel session**

### 2.4 Recent Activity / Quick Links

- Link to "Edit my profile" → `/paired/mentor/settings`
- Link to "View my public profile" → `/paired/mentors/{id}`
- Link to "My mentees" → `/paired/mentor/mentees`
- Mentor tips sidebar (rotating tips for good mentorship)

---

## 3. Session Management

> Mentors can create multiple session types from day one. Each session type represents a different offering (e.g. "Career Strategy Call", "CV Review", "Mock Interview").

**Route:** `/paired/mentor/sessions` (manage) · Displayed on mentor's public profile

### 3.1 Session Type Fields

| Field | Type | Notes |
|-------|------|-------|
| Session name | text | Required. e.g. "Career Strategy Call" |
| Duration | select | 30 / 45 / 60 minutes |
| Description | textarea | What the session covers |
| Price | number | 0 = free (default for now); future paid sessions |
| Type | select | One-off / Recurring |
| Visibility | toggle | Show/hide from public profile |
| Group booking | toggle | Allow multiple mentees per slot |
| Slot capacity | number | If group booking enabled, max per slot |
| Cover image | image | Optional; stored on Cloudflare R2 |

### 3.2 Session CRUD

- Mentors can create, edit, and delete session types
- Each session type is a WordPress custom post type `paired_session`
- Session meta: `_session_mentor_id`, `_session_name`, `_session_duration`, `_session_description`, `_session_price`, `_session_type`, `_session_visibility`, `_session_group_booking`, `_session_slot_capacity`, `_session_cover_image`
- When a mentee books, they select which session type they want

### 3.3 Default Session

- On mentor approval, a default session is auto-created: "Mentorship Session" (60 min, free, visible)
- Mentor can edit or create additional types

### 3.4 Paid Sessions (Phase 3 — future)

- Stripe integration for UK payments
- Mentor payout settings (bank details / PayPal)
- Coupon/discount codes
- Payment status tracking on bookings

---

## 4. Booking Lifecycle

### 4.1 Status Flow

```
[Mentee books] → pending
       │
       ├── Mentor accepts → confirmed
       │         │
       │         ├── Session happens → completed
       │         │
       │         └── Either party cancels → cancelled
       │
       └── Mentor declines → cancelled
```

### 4.2 Booking Data

| Field | Type | Notes |
|-------|------|-------|
| Mentor ID | int | WordPress user ID |
| Mentee ID | int | WordPress user ID |
| Session ID | int | Which session type was booked |
| Date | date | Selected by mentee |
| Time slot | string | e.g. "10:00 - 11:00" |
| Duration | int | Minutes (from session type) |
| Notes | text | Mentee's message to mentor |
| Status | enum | `pending`, `confirmed`, `cancelled`, `completed` |
| Created at | datetime | Auto |
| Google Meet link | url | Auto-generated via Google Calendar API on confirm |
| Google Calendar event ID | string | For updating/cancelling the calendar event |

### 4.3 Google Meet Auto-Generation

When a mentor **accepts** a booking request (status changes from `pending` → `confirmed`):

1. **Backend creates a Google Calendar event** via Google Calendar API using a platform-owned **service account**
2. The event includes `conferenceData.createRequest` which auto-generates a Google Meet link
3. The Meet link is stored on the booking as `_bpu_booking_meet_link`
4. The Calendar event ID is stored as `_bpu_booking_calendar_event_id`
5. Both mentor and mentee receive the Meet link in the confirmation email

**Setup requirements:**
- Google Cloud project with Calendar API enabled
- Service account with credentials JSON
- Service account credentials stored as WordPress options (`_paired_google_service_account`)
- Service account needs no domain-wide delegation — it creates events on its own calendar and shares with participants

**Calendar event details:**
- Title: `"PAIRED: {session_name} — {mentor_name} & {mentee_name}"`
- Duration: from session type
- Attendees: mentor email + mentee email (both receive calendar invite)
- Description: session description + mentee's notes
- Conference: Google Meet (auto-created)

**On cancellation:**
- Calendar event is deleted via API using stored event ID
- Meet link is cleared from booking

**On completion:**
- Calendar event remains (for record-keeping)

### 4.4 Email Notifications per Status Change

| Event | Recipient | Email content |
|-------|-----------|---------------|
| Booking created | Mentor | "New session request from {mentee}" with date/time/notes |
| Booking created | Mentee | "Your session request has been sent to {mentor}" |
| Accepted | Mentee | "Your session with {mentor} is confirmed" with meeting link |
| Declined | Mentee | "Session request declined" with link to browse other mentors |
| Completed | Mentee | "Session completed — we hope it was valuable" |
| Cancelled | Both | "Session cancelled" with reason if provided |

---

## 5. Availability & Schedule

**Route:** Part of `/paired/mentor/settings` (Availability tab)

### 5.1 Weekly Schedule (default hours)

Mentors set their available hours per day of week. This is the primary availability mechanism.

| Field | Type | Notes |
|-------|------|-------|
| Day of week | Mon–Sun | Toggle each day on/off |
| Start time | time | e.g. 09:00 |
| End time | time | e.g. 17:00 |
| Multiple slots per day | repeater | Allow e.g. 09:00–12:00 and 14:00–17:00 |

### 5.2 Auto-Slot Generation

- System generates bookable time slots based on:
  - Mentor's weekly schedule (available hours)
  - Session duration (from the session type being booked)
  - Buffer time between slots (default 15 minutes)
- Existing confirmed bookings are excluded from available slots
- Slots are shown in the mentee's local timezone (detected or selected)
- Availability window: next 40 days

### 5.3 Holiday / Blocked Dates

- Mentor can block specific dates (holidays, personal time)
- Stored as JSON array in user meta `_paired_holidays`
- Blocked dates excluded from slot generation

### 5.4 Timezone

| Field | Type | Notes |
|-------|------|-------|
| Timezone | select | Full IANA timezone list; default Europe/London |

- All times stored in UTC internally
- Displayed in mentor's timezone on their dashboard
- Displayed in mentee's timezone on booking form

### 5.5 Capacity

| Field | Type | Notes |
|-------|------|-------|
| Mentees at once | number | 1–6; max active mentee relationships |
| Mentorship availability | select | Once a month, Twice a month, Once every 2 months |

- When capacity is reached, mentor's profile shows "Currently full" badge
- Mentees can still view profile but booking button is disabled

### 5.6 Custom Hours per Session Type (Phase 2)

- Override default weekly hours for specific session types
- e.g. "Mock Interview" only available on Saturdays

---

## 6. Mentee Management

**Route:** `/paired/mentor/mentees`

### 6.1 Mentee List

- All unique mentees derived from bookings where current user is mentor
- Card or table layout
- Each entry shows:
  - Mentee name and avatar
  - Number of sessions (total bookings together)
  - Last session date
  - Status of most recent booking
  - Link to view full booking history with this mentee

### 6.2 Mentee Detail (inline expand or modal)

- All bookings with this mentee, sorted by date
- Each booking shows: date, time, status, notes
- Quick action to message mentee (Phase 2)

---

## 7. Messaging

> **Phase 2 feature.** Not in MVP.

### 7.1 Features

| Feature | Description |
|---------|-------------|
| Contact list | All mentees (for mentor) or mentors (for mentee) from booking history |
| Send message | Text-only messages |
| Message history | Threaded by contact, sorted by time |
| Notifications | Email notification when new message received |
| Unread count | Badge on nav item |

### 7.2 Data Model

| Field | Type |
|-------|------|
| from_user_id | int |
| to_user_id | int |
| message | text |
| created_at | datetime |
| read_at | datetime (nullable) |

---

## 8. Work Experience & Education

> Stored as custom post types or serialised user meta in WordPress.

### 8.1 Work Experience

**Route:** Part of `/paired/mentor/settings` (section)

| Field | Type | Notes |
|-------|------|-------|
| Job title | text | Required |
| Company | text | Required |
| Start date | month/year | Required |
| End date | month/year | Nullable; "I currently work here" toggle |
| Description | textarea | Key responsibilities/achievements |

- Add multiple entries
- Edit / delete existing
- Displayed chronologically on public profile
- Most recent first

### 8.2 Education

| Field | Type | Notes |
|-------|------|-------|
| Institution | text | Required |
| Degree / Qualification | text | Required |
| Start year | year | Required |
| End year | year | Nullable |

- Add multiple entries
- Displayed on public profile

---

## 9. Skills & Expertise

> Skills are **hardcoded** in the codebase (no admin UI needed). Comprehensive list to cover the breadth of Black professional expertise in the UK.

### 9.1 Predefined Skill Categories

**Engineering & Technology**
- Front-end Development, Back-end Development, Full Stack Development, Mobile Development (iOS), Mobile Development (Android), DevOps, Cloud Engineering (AWS), Cloud Engineering (Azure), Cloud Engineering (GCP), Site Reliability Engineering, QA & Testing, Data Engineering, AI & Machine Learning, Cybersecurity, Blockchain, Embedded Systems, Systems Architecture, Database Administration, API Development, Technical Leadership

**Product & Project Management**
- Product Management, Product Strategy, Product Analytics, Program Management, Project Management, Agile & Scrum, Product Operations, Technical Product Management

**Design & Creative**
- UX Design, UI Design, Graphic Design, Motion Design, Brand Design, Industrial Design, Design Systems, Design Ops, UX Research, Interaction Design, Service Design, 3D Design, Game Design, XR/VR Design

**Marketing & Communications**
- Digital Marketing, Content Marketing, Social Media Marketing, Brand Strategy, Growth Marketing, SEO & SEM, Email Marketing, PR & Communications, Event Marketing, Influencer Marketing, Marketing Analytics, Community Management, Product Marketing, Performance Marketing

**Data & Analytics**
- Data Analysis, Data Science, Machine Learning, Business Intelligence, Statistical Modelling, Data Visualisation, Natural Language Processing, Computer Vision, Big Data, A/B Testing & Experimentation

**Finance & Banking**
- Investment Banking, Corporate Finance, Financial Planning & Analysis, Accounting, Risk Management, Compliance & Regulation, Wealth Management, Fintech, Audit, Tax, Treasury, Private Equity, Venture Capital, Insurance, Actuarial Science

**Legal**
- Corporate Law, Employment Law, Intellectual Property, Contract Law, Regulatory Compliance, Commercial Law, Immigration Law, Family Law, Criminal Law, Legal Operations

**Healthcare & Life Sciences**
- Clinical Medicine, Nursing, Public Health, Health Tech, Pharmaceutical, Biotech, Mental Health, Health Policy, Clinical Research, Health Informatics

**Education & Training**
- Teaching, Curriculum Development, EdTech, Corporate Training, Academic Research, Higher Education, STEM Education, Coaching & Mentoring, Special Education, Learning Design

**Human Resources**
- Talent Acquisition, HR Business Partnering, Learning & Development, Compensation & Benefits, Employee Relations, DEI Strategy, People Analytics, Organisational Development, HR Tech, Employer Branding

**Sales & Business Development**
- Enterprise Sales, B2B Sales, Account Management, Business Development, Sales Operations, Customer Success, Partnership Management, Revenue Operations, Sales Engineering

**Operations & Strategy**
- Management Consulting, Business Strategy, Operations Management, Supply Chain, Procurement, Change Management, Process Improvement, Lean & Six Sigma, Logistics

**Media & Entertainment**
- Journalism, Broadcasting, Film Production, Music Industry, Publishing, Podcasting, Photography, Content Creation, Streaming & Digital Media

**Property & Construction**
- Property Development, Architecture, Surveying, Construction Management, Urban Planning, Estate Management, Facilities Management

**Entrepreneurship**
- Startup Founding, Fundraising, Business Planning, Bootstrapping, Social Enterprise, Franchise, E-commerce, Scaling & Growth

**Public Sector & Policy**
- Civil Service, Policy Analysis, Local Government, International Development, Charity & Non-profit, Public Affairs, Community Development

### 9.2 UI

- Tag-style input on profile settings with autocomplete from the full list above
- Skills grouped by category in the autocomplete dropdown
- Allow free-text custom skills (appended to user's list)
- Display as badges on public profile and directory cards
- Searchable in mentor directory filter

---

## 10. Reviews & Ratings

> **Phase 2 feature.** Not in MVP.

### 10.1 Features

| Feature | Description |
|---------|-------------|
| Star rating | 1–5 stars, left by mentee after session marked completed |
| Written feedback | Optional text review |
| Display on profile | Average rating + review count on public profile |
| Review prompt | Email sent to mentee after session completed, linking to review form |

### 10.2 Data Model

| Field | Type |
|-------|------|
| mentor_id | int |
| mentee_id | int |
| booking_id | int |
| rating | int (1–5) |
| feedback | text |
| created_at | datetime |

---

## 11. Notifications

### 11.1 Email Notifications (MVP)

| Trigger | Recipient | Template |
|---------|-----------|----------|
| New booking request | Mentor | Branded HTML email with mentee info, date, notes |
| Booking accepted | Mentee | Confirmation with meeting details |
| Booking declined | Mentee | Encouragement to try other mentors |
| Session completed | Mentee | Thank you + browse more mentors |
| Mentor application approved | Applicant | Welcome to PAIRED as mentor |
| Mentor application rejected | Applicant | Feedback and encouragement |
| Password reset | User | Reset link (already built) |

### 11.2 In-App Notifications (Phase 2)

- Bell icon in topbar with unread count
- Dropdown list of recent notifications
- Types: new booking, booking status change, new message, new review
- Mark as read on click

---

## 12. Mentor Application Flow

**Route:** `/paired/apply`

### 12.1 Current Flow (already built)

1. User clicks "Apply to mentor" from PAIRED homepage
2. Fills application form (basic info, motivation, experience)
3. Application stored as WordPress post (`mentor_application` CPT)
4. Admin reviews at `/paired/admin/applications`
5. Admin approves → user gets `mentor` role + welcome email
6. Admin rejects → user gets rejection email

### 12.2 Enhancements (Phase 2)

- Multi-step application wizard
- Upload CV / portfolio
- Reference check step
- Auto-match criteria scoring
- Mentor onboarding checklist after approval

---

## 13. Mentor Discovery & Search

**Route:** `/paired/mentors`

### 13.1 Current Features (already built)

- Grid of mentor cards with photo, name, role, company, industry, years of experience
- Search by name
- Filter by industry
- Pagination
- Click card → full mentor profile at `/paired/mentors/{id}`

### 13.2 Enhancements

| Feature | Description |
|---------|-------------|
| Skill filter | Filter by specific skills |
| Location filter | Filter by UK region |
| Sort options | By name, years of experience, newest |
| AI matching | "Find my top 3 mentors" based on mentee's profile |
| Availability indicator | Badge showing if mentor is currently accepting mentees |
| Rating display | Star rating on card (Phase 2) |
| Favourite mentors | Heart/save button; list in mentee dashboard (Phase 2) |

---

## 14. Admin Features

**Route:** `/paired/admin/*`

### 14.1 Current (already built)

- `/paired/admin/applications` — Review, approve, reject mentor applications

### 14.2 Planned

| Page | Features |
|------|----------|
| `/paired/admin/mentors` | List all mentors, edit profiles, deactivate accounts |
| `/paired/admin/bookings` | View all bookings platform-wide, filter by status/date |
| `/paired/admin/stats` | Platform stats: total mentors, mentees, bookings, completion rate |
| `/paired/admin/skills` | Manage predefined skill categories and skills |

---

## 15. Mentee-Side Features

### 15.1 Mentee Dashboard (`/paired/dashboard` when not mentor)

| Section | Content |
|---------|---------|
| Upcoming sessions | Confirmed bookings with date >= today |
| Past sessions | Completed bookings |
| Pending requests | Bookings awaiting mentor response |
| AI-matched mentors | Top 3 suggested mentors (existing feature) |
| Quick actions | Browse mentors, view booking history |

### 15.2 Booking Flow (already built)

1. Browse mentor directory or view mentor profile
2. Click "Book a session"
3. Select date + time slot
4. Add notes/message
5. Submit → booking created with `pending` status
6. Wait for mentor to accept/decline

### 15.3 Mentee Profile (Phase 2)

- Mentees can set their own industry, career goals, skills they want to develop
- This data powers AI matching
- Visible to mentors when reviewing booking requests

---

## 16. Data Model

### WordPress Storage Strategy

| Data | Storage Method | Notes |
|------|---------------|-------|
| User profile fields | `usermeta` table | All fields in sections 1.1–1.5 |
| Profile photos | Cloudflare R2 | Uploaded via pre-signed URL; public URL stored in usermeta `_paired_photo_url` |
| Session cover images | Cloudflare R2 | Same pattern as profile photos |
| Bookings | Custom post type `mentorship_booking` | Already exists |
| Session types | Custom post type `paired_session` | New — mentor's session offerings |
| Work experience | Custom post type `mentor_experience` | New |
| Education | Custom post type `mentor_education` | New |
| Skills (predefined) | Hardcoded in PHP + JS | Full list in section 9.1 |
| User skills | `usermeta` serialised array | Already exists as `skills_separate` |
| Availability schedule | `usermeta` JSON | `_paired_weekly_schedule` — array of day/start/end |
| Holidays | `usermeta` JSON | `_paired_holidays` — array of blocked dates |
| Google service account | WordPress option | `_paired_google_service_account` — credentials JSON |
| Reviews | Custom post type `mentor_review` | Phase 2 |
| Messages | Custom post type `paired_message` | Phase 2 |
| Notifications | Custom post type `paired_notification` | Phase 2 |
| Mentor applications | Custom post type `mentor_application` | Already exists |

### Booking Meta Keys

- `_bpu_booking_mentor_id`
- `_bpu_booking_mentee_id`
- `_bpu_booking_session_id` — Which session type was booked
- `_bpu_booking_date`
- `_bpu_booking_time_slot`
- `_bpu_booking_duration` — Minutes (from session type)
- `_bpu_booking_notes`
- `_bpu_booking_status` — `pending` | `confirmed` | `cancelled` | `completed`
- `_bpu_booking_created_at`
- `_bpu_booking_meet_link` — Auto-generated Google Meet URL
- `_bpu_booking_calendar_event_id` — Google Calendar event ID for updates/deletion

### Session Type Meta Keys (new)

- `_session_mentor_id`
- `_session_name`
- `_session_duration` — 30 / 45 / 60
- `_session_description`
- `_session_price` — 0 for free
- `_session_type` — `one_off` | `recurring`
- `_session_visibility` — `visible` | `hidden`
- `_session_group_booking` — 0 | 1
- `_session_slot_capacity` — Max per slot if group
- `_session_cover_image` — R2 URL

### Experience Meta Keys (new)

- `_mentor_exp_user_id`
- `_mentor_exp_title`
- `_mentor_exp_company`
- `_mentor_exp_start_date`
- `_mentor_exp_end_date`
- `_mentor_exp_is_current`
- `_mentor_exp_description`

### Education Meta Keys (new)

- `_mentor_edu_user_id`
- `_mentor_edu_institution`
- `_mentor_edu_degree`
- `_mentor_edu_start_year`
- `_mentor_edu_end_year`

---

## 17. API Endpoints

### Already Built

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/bpu/v1/paired/mentors` | List mentors (search, filter, paginate) |
| GET | `/bpu/v1/paired/mentors/{id}` | Single mentor public profile |
| POST | `/bpu/v1/paired/bookings` | Create a booking |
| GET | `/bpu/v1/paired/bookings` | List user's bookings |
| POST | `/bpu/v1/paired/mentor-apply` | Submit mentor application |
| GET | `/bpu/v1/paired/mentor-applications` | Admin: list applications |
| POST | `/bpu/v1/paired/mentor-approve/{id}` | Admin: approve application |
| POST | `/bpu/v1/paired/mentor-reject/{id}` | Admin: reject application |
| GET | `/bpu/v1/paired/mentor/profile` | Mentor's own full profile |
| PUT | `/bpu/v1/paired/mentor/profile` | Update mentor's own profile |
| GET | `/bpu/v1/paired/mentor/mentees` | Mentor's mentee list |
| GET | `/bpu/v1/paired/mentor/stats` | Mentor dashboard stats |
| POST | `/bpu/v1/bookings/{id}/status` | Update booking status |

### New Endpoints Needed

| Method | Endpoint | Description | Phase |
|--------|----------|-------------|-------|
| GET | `/bpu/v1/paired/mentor/sessions` | List mentor's session types | 1 |
| POST | `/bpu/v1/paired/mentor/sessions` | Create session type | 1 |
| PUT | `/bpu/v1/paired/mentor/sessions/{id}` | Update session type | 1 |
| DELETE | `/bpu/v1/paired/mentor/sessions/{id}` | Delete session type | 1 |
| GET | `/bpu/v1/paired/mentors/{id}/sessions` | Public: list mentor's visible sessions | 1 |
| GET | `/bpu/v1/paired/mentor/experiences` | List mentor's work experiences | 1 |
| POST | `/bpu/v1/paired/mentor/experiences` | Add work experience | 1 |
| PUT | `/bpu/v1/paired/mentor/experiences/{id}` | Update work experience | 1 |
| DELETE | `/bpu/v1/paired/mentor/experiences/{id}` | Delete work experience | 1 |
| GET | `/bpu/v1/paired/mentor/education` | List mentor's education | 1 |
| POST | `/bpu/v1/paired/mentor/education` | Add education entry | 1 |
| PUT | `/bpu/v1/paired/mentor/education/{id}` | Update education entry | 1 |
| DELETE | `/bpu/v1/paired/mentor/education/{id}` | Delete education entry | 1 |
| GET | `/bpu/v1/paired/skills` | List all predefined skills (hardcoded) | 1 |
| POST | `/bpu/v1/paired/mentor/photo` | Upload profile photo to R2 | 1 |
| GET | `/bpu/v1/paired/mentor/availability` | Get mentor's weekly schedule | 1 |
| PUT | `/bpu/v1/paired/mentor/availability` | Update weekly schedule + holidays | 1 |
| GET | `/bpu/v1/paired/mentors/{id}/slots` | Public: available slots for a date range | 1 |
| POST | `/bpu/v1/paired/reviews` | Submit a review (mentee) | 2 |
| GET | `/bpu/v1/paired/mentors/{id}/reviews` | Get mentor's reviews | 2 |
| GET | `/bpu/v1/paired/messages` | List conversations | 2 |
| POST | `/bpu/v1/paired/messages` | Send a message | 2 |
| GET | `/bpu/v1/paired/notifications` | List notifications | 2 |
| PUT | `/bpu/v1/paired/notifications/{id}/read` | Mark notification read | 2 |

---

## 18. Page Map

### Existing Pages

| Route | Component | Auth |
|-------|-----------|------|
| `/paired` | Homepage | Public |
| `/paired/mentors` | Mentor directory | Public |
| `/paired/mentors/{id}` | Mentor public profile | Public |
| `/paired/dashboard` | Dashboard (role-aware) | Authenticated |
| `/paired/apply` | Mentor application form | Authenticated |
| `/paired/admin/applications` | Admin application review | Admin only |

### New Pages Needed

| Route | Component | Auth | Phase |
|-------|-----------|------|-------|
| `/paired/mentor/settings` | Profile settings (personal, professional, mentorship, social, availability) | Mentor | 1 |
| `/paired/mentor/sessions` | Manage session types (CRUD) | Mentor | 1 |
| `/paired/mentor/mentees` | Mentee list + history | Mentor | 1 |
| `/paired/mentor/bookings` | All bookings management | Mentor | 1 |
| `/paired/admin/mentors` | Admin mentor management | Admin | 2 |
| `/paired/admin/bookings` | Admin booking overview | Admin | 2 |
| `/paired/admin/stats` | Platform analytics | Admin | 2 |
| `/paired/mentors/{id}/review` | Submit review form | Authenticated | 2 |
| `/paired/messages` | Messaging inbox | Authenticated | 2 |

### New API Proxy Routes (Next.js)

| Route | Proxies to | Phase |
|-------|-----------|-------|
| `/api/paired/mentor/profile` | GET/PUT mentor profile | 1 |
| `/api/paired/mentor/sessions` | CRUD session types | 1 |
| `/api/paired/mentor/mentees` | GET mentor mentees | 1 |
| `/api/paired/mentor/stats` | GET mentor stats | 1 |
| `/api/paired/mentor/experiences` | CRUD experiences | 1 |
| `/api/paired/mentor/education` | CRUD education | 1 |
| `/api/paired/mentor/photo` | POST photo upload to R2 | 1 |
| `/api/paired/mentor/availability` | GET/PUT availability schedule | 1 |
| `/api/paired/bookings/[id]/status` | POST status update (triggers Google Meet) | 1 |
| `/api/paired/skills` | GET predefined skills | 1 |
| `/api/paired/mentors/[id]/slots` | GET available booking slots | 1 |

---

## 19. Build Priority

### Phase 1 — Core Mentor Platform

1. **Mentor profile settings page** — Edit all profile fields (personal, professional, mentorship, social, availability)
2. **Session type management** — Create/edit/delete session types from day one
3. **Full calendar-based availability** — Weekly schedule, holidays, timezone, auto-slot generation
4. **Booking management** — Accept/decline/complete bookings; Google Meet auto-generation on accept
5. **Mentee list page** — View all mentees and booking history
6. **Work experience & education** — CRUD on profile settings page
7. **Profile photo upload** — Cloudflare R2 storage with pre-signed URLs
8. **Updated public profile** — Show experience, education, skills, session types, availability on mentor's public page
9. **Email notifications** — Booking status change emails with Google Meet link
10. **Nav updates** — Add Settings, Sessions, Mentees, Bookings links for mentors
11. **Comprehensive hardcoded skills** — 150+ skills across 16 categories

### Phase 2 — Enhanced Platform

12. Reviews & ratings system
13. In-app messaging
14. In-app notification centre
15. Favourite mentors (mentee side)
16. AI mentor matching improvements
17. Mentee profile (career goals, skills to develop)
18. Admin mentor management
19. Admin platform analytics
20. Custom hours per session type

### Phase 3 — Premium Features (future)

21. Paid sessions (Stripe UK)
22. Group sessions
23. Recurring session scheduling
24. Mentor onboarding checklist
25. Referral programme
26. KYC verification
27. Google Calendar two-way sync (currently one-way: platform → calendar)

---

## Decisions Confirmed

| Decision | Answer |
|----------|--------|
| Session types | Multiple session types from day one |
| Profile photo storage | Cloudflare R2 (pre-signed upload URLs) |
| Skills management | Hardcoded — 150+ skills across 16 categories |
| Meeting links | Google Meet auto-generated via Google Calendar API (service account) |
| Availability | Full calendar-based: weekly schedule, holidays, timezone, auto-slot generation |
| Messaging | Phase 2 |

### Google Meet Integration — Service Account Approach

- A single platform-owned Google Cloud service account creates all calendar events
- No OAuth flow needed for mentors — they just receive calendar invites + Meet links
- Credentials stored as WordPress option
- Meet link generated on booking confirmation (mentor accepts)
- Calendar event deleted on booking cancellation
- Setup: Google Cloud Console → Calendar API → Service Account → download JSON key
