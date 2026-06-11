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
| Profile photo | image upload | Replaces Gravatar; stored as WP user meta or media |
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
| Meet type | select | Google Meet / Zoom / Microsoft Teams |
| Meeting URL | url | Custom meeting link |

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

> **Note:** The old platform had a complex session-creation system where mentors defined named "sessions" (e.g. "Career Strategy Call", "CV Review") with durations, pricing, and separate schedules. For the new platform, we simplify this.

### 3.1 Phase 1 — Single session type (MVP)

Every mentor offers one type of session: a free 1-on-1 video call. Duration is 30 or 60 minutes (mentor chooses in settings). No pricing, no payment.

### 3.2 Phase 2 — Multiple session types (future)

| Field | Type | Notes |
|-------|------|-------|
| Session name | text | e.g. "Career Strategy Call" |
| Duration | select | 30 / 45 / 60 minutes |
| Description | textarea | What the session covers |
| Price | number | 0 = free; future paid sessions |
| Type | select | One-off / Recurring |
| Visibility | toggle | Show/hide from public profile |
| Group booking | toggle | Allow multiple mentees per slot |
| Slot capacity | number | If group booking enabled, max per slot |
| Cover image | image | Optional |

### 3.3 Phase 3 — Paid sessions (future)

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
| Date | date | Selected by mentee |
| Time slot | string | e.g. "10:00 - 11:00" |
| Notes | text | Mentee's message to mentor |
| Status | enum | `pending`, `confirmed`, `cancelled`, `completed` |
| Created at | datetime | Auto |
| Meeting link | url | Optional; set by mentor |

### 4.3 Email Notifications per Status Change

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

### 5.1 Phase 1 — Simple availability (MVP)

Mentors set their `mentorship_availability` (frequency) and `mentees_at_once` (capacity). Mentees pick a date and time slot when booking. No real-time calendar — just a date picker + time dropdown.

Available time slots are predefined: Morning (9:00–10:00), Late Morning (11:00–12:00), Afternoon (14:00–15:00), Late Afternoon (16:00–17:00), Evening (18:00–19:00).

### 5.2 Phase 2 — Custom availability (future)

| Feature | Description |
|---------|-------------|
| Default working hours | Set available hours per day of week (Mon–Sun) |
| Custom hours per session | Override defaults for specific session types |
| Holiday dates | Block out specific dates |
| Time zone | Mentor sets their timezone; slots shown in mentee's local time |
| Auto-slot generation | System generates available slots based on duration + working hours |
| Google Calendar sync | Two-way sync to avoid double-booking |
| Zoom auto-link | Generate Zoom meeting for each confirmed booking |

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

### 9.1 Predefined Skill Categories

| Category | Example Skills |
|----------|---------------|
| Engineering | Front-end, Back-end, Full Stack, Mobile, DevOps, AI/ML, QA, Data Engineering |
| Marketing | Digital Marketing, Branding, Content Marketing, Growth, Sales, Community |
| Product | Product Management, Program Management, Product Strategy |
| Design | UX, UI, Graphic Design, Motion Design, Design Ops |
| Data Science | Data Analysis, Data Engineering, Machine Learning |
| Content | Technical Writing, Copywriting, Content Strategy, UX Writing |
| Finance | Accounting, Investment Banking, Financial Planning, Risk Management |
| Legal | Corporate Law, Employment Law, IP Law |
| Healthcare | Clinical, Public Health, Health Tech |
| Education | Teaching, Curriculum Development, EdTech |

### 9.2 UI

- Tag-style input on profile settings
- Autocomplete from predefined list
- Allow free-text custom skills
- Display as badges on public profile and directory cards

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
| Bookings | Custom post type `mentorship_booking` | Already exists |
| Work experience | Custom post type `mentor_experience` | New |
| Education | Custom post type `mentor_education` | New |
| Skills (predefined) | Custom taxonomy or options | New |
| User skills | `usermeta` serialised array | Already exists as `skills_separate` |
| Reviews | Custom post type `mentor_review` | Phase 2 |
| Messages | Custom post type `paired_message` | Phase 2 |
| Notifications | Custom post type `paired_notification` | Phase 2 |
| Mentor applications | Custom post type `mentor_application` | Already exists |

### Booking Meta Keys (existing)

- `_bpu_booking_mentor_id`
- `_bpu_booking_mentee_id`
- `_bpu_booking_date`
- `_bpu_booking_time_slot`
- `_bpu_booking_notes`
- `_bpu_booking_status` — `pending` | `confirmed` | `cancelled` | `completed`
- `_bpu_booking_created_at`
- `_bpu_booking_meeting_link` — New

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
| GET | `/bpu/v1/paired/mentor/experiences` | List mentor's work experiences | 1 |
| POST | `/bpu/v1/paired/mentor/experiences` | Add work experience | 1 |
| PUT | `/bpu/v1/paired/mentor/experiences/{id}` | Update work experience | 1 |
| DELETE | `/bpu/v1/paired/mentor/experiences/{id}` | Delete work experience | 1 |
| GET | `/bpu/v1/paired/mentor/education` | List mentor's education | 1 |
| POST | `/bpu/v1/paired/mentor/education` | Add education entry | 1 |
| PUT | `/bpu/v1/paired/mentor/education/{id}` | Update education entry | 1 |
| DELETE | `/bpu/v1/paired/mentor/education/{id}` | Delete education entry | 1 |
| GET | `/bpu/v1/paired/skills` | List all predefined skills | 1 |
| POST | `/bpu/v1/paired/mentor/photo` | Upload profile photo | 1 |
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
| `/paired/mentor/settings` | Profile settings (all sections) | Mentor | 1 |
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
| `/api/paired/mentor/mentees` | GET mentor mentees | 1 |
| `/api/paired/mentor/stats` | GET mentor stats | 1 |
| `/api/paired/mentor/experiences` | CRUD experiences | 1 |
| `/api/paired/mentor/education` | CRUD education | 1 |
| `/api/paired/mentor/photo` | POST photo upload | 1 |
| `/api/paired/bookings/[id]/status` | POST status update | 1 |
| `/api/paired/skills` | GET predefined skills | 1 |

---

## 19. Build Priority

### Phase 1 — Core Mentor Platform (MVP)

1. **Mentor profile settings page** — Edit all profile fields (personal, professional, mentorship, social)
2. **Booking management** — Accept/decline/complete bookings from dashboard
3. **Mentee list page** — View all mentees and booking history
4. **Work experience & education** — CRUD on profile settings page
5. **Profile photo upload** — Replace Gravatar with real photo
6. **Updated public profile** — Show experience, education, skills on mentor's public page
7. **Email notifications** — Booking status change emails (accept/decline/complete)
8. **Nav updates** — Add Settings, Mentees, Bookings links for mentors

### Phase 2 — Enhanced Platform

9. Reviews & ratings system
10. In-app messaging
11. In-app notification centre
12. Favourite mentors (mentee side)
13. AI mentor matching improvements
14. Mentee profile (career goals, skills to develop)
15. Admin mentor management
16. Admin platform analytics

### Phase 3 — Premium Features (future)

17. Paid sessions (Stripe UK)
18. Multiple session types per mentor
19. Custom availability calendar with timezone support
20. Google Calendar sync
21. Zoom/Teams auto-link generation
22. Group sessions
23. Mentor onboarding checklist
24. Referral programme
25. KYC verification

---

## Key Decisions to Confirm

1. **Session types** — MVP with single free session type, or allow mentors to create multiple session types from day one?
2. **Profile photo storage** — WordPress media library (via REST API upload) or external service (Cloudinary, S3)?
3. **Skills management** — Hardcoded list in code, or admin-manageable via WordPress?
4. **Meeting links** — Mentor provides their own link, or integrate with Zoom/Google Meet API?
5. **Availability** — Simple frequency selector (MVP) or full calendar-based availability?
6. **Messaging** — Include in Phase 1 or defer to Phase 2?
