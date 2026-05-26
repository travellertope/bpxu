'use client';

import React, { useState } from 'react';
import { BPUUser, ACFProfile } from '@/lib/auth';
import { JobListing, CourseItem, CVReview, EventItem, BPUApi } from '@/lib/api';

function ProGate({ children, isPro, feature }: { children: React.ReactNode; isPro: boolean; feature: string }) {
    if (isPro) return <>{children}</>;
    return (
        <div className="card card-p space-y-3 text-center py-10">
            <p className="text-2xl">★</p>
            <p className="font-semibold">{feature} is a Pro feature</p>
            <p className="text-sm text-text-2">Upgrade to BPU Pro to unlock AI-powered career tools.</p>
            <a href="/upgrade" className="btn btn-amber btn-sm inline-flex mx-auto">Upgrade to Pro →</a>
        </div>
    );
}

type Tab = 'overview' | 'cv' | 'jobs' | 'courses' | 'events' | 'profile';

interface Props {
  user: BPUUser;
  initialJobs: JobListing[];
  initialCourses: CourseItem[];
  initialReviews: CVReview[];
  initialEvents: EventItem[];
  jwt: string;
}

export default function ClientDashboard({ user, initialJobs, initialCourses, initialReviews, initialEvents, jwt }: Props) {
  const isPro = user.is_pro;
  const [tab, setTab] = useState<Tab>('overview');
  const [profile, setProfile] = useState<ACFProfile>(user.profile);
  const [cvUrl, setCvUrl] = useState(user.cv_url || '');
  const [jobs] = useState<JobListing[]>(initialJobs);
  const [courses, setCourses] = useState<CourseItem[]>(initialCourses);
  const [reviews] = useState<CVReview[]>(initialReviews);
  const [events] = useState<EventItem[]>(initialEvents);

  // CV upload
  const [uploading, setUploading] = useState(false);
  const [uploadMsg, setUploadMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);

  // CV review request
  const [requestingReview, setRequestingReview] = useState(false);
  const [reviewRequestMsg, setReviewRequestMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);

  // CV review accordion
  const [openReview, setOpenReview] = useState<number | null>(null);

  // Profile edit
  const [editForm, setEditForm] = useState<Partial<ACFProfile>>(profile);
  const [saving, setSaving] = useState(false);
  const [saveMsg, setSaveMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);

  // Email preferences (pro)
  const [weeklyEmails, setWeeklyEmails] = useState(false);
  const [prefSaving, setPrefSaving] = useState(false);
  const [prefMsg, setPrefMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);

  const handleCVUpload = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;
    if (file.type !== 'application/pdf') {
      setUploadMsg({ type: 'err', text: 'Only PDF files are supported.' });
      return;
    }
    setUploading(true);
    setUploadMsg(null);
    const form = new FormData();
    form.append('cv_file', file);
    try {
      const res = await fetch('/api/member/cv-upload', { method: 'POST', body: form });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Upload failed.');
      setCvUrl(data.cv_url);
      setProfile(data.parsed_data);
      setEditForm(data.parsed_data);
      setUploadMsg({ type: 'ok', text: 'CV uploaded — your profile has been updated by Gemini Pro.' });
    } catch (err: unknown) {
      setUploadMsg({ type: 'err', text: err instanceof Error ? err.message : 'Upload error.' });
    } finally {
      setUploading(false);
    }
  };

  const handleCourseOpen = async (course: CourseItem) => {
    setCourses(prev => prev.map(c => c.id === course.id ? { ...c, status: 'In Progress' } : c));
    BPUApi.trackCourseProgress(course.id, jwt).catch(() => {});
    window.open(course.learn_more_url, '_blank', 'noopener,noreferrer');
  };

  const handleProfileSave = async () => {
    setSaving(true);
    setSaveMsg(null);
    try {
      const res = await fetch('/api/member/profile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(editForm),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Save failed.');
      setProfile({ ...profile, ...editForm });
      setSaveMsg({ type: 'ok', text: 'Profile saved successfully.' });
    } catch (err: unknown) {
      setSaveMsg({ type: 'err', text: err instanceof Error ? err.message : 'Could not save profile.' });
    } finally {
      setSaving(false);
    }
  };

  const handleRequestReview = async () => {
    setRequestingReview(true);
    setReviewRequestMsg(null);
    const result = await BPUApi.requestCVReview();
    setReviewRequestMsg(result.success
      ? { type: 'ok', text: 'Review requested — our team will be in touch within 5 working days.' }
      : { type: 'err', text: result.error || 'Could not submit request.' }
    );
    setRequestingReview(false);
  };

  const handlePrefSave = async () => {
    setPrefSaving(true);
    const ok = await BPUApi.updatePreferences({ weekly_emails: weeklyEmails });
    setPrefMsg(ok
      ? { type: 'ok', text: 'Preferences saved.' }
      : { type: 'err', text: 'Could not save preferences.' }
    );
    setPrefSaving(false);
  };

  const firstName = profile.first_name || user.display_name.split(' ')[0];

  const tabs: { id: Tab; label: string }[] = [
    { id: 'overview', label: 'Overview' },
    { id: 'cv',       label: 'CV Clinic' },
    { id: 'jobs',     label: `Jobs${jobs.length ? ` (${jobs.length})` : ''}` },
    { id: 'courses',  label: `Courses${courses.length ? ` (${courses.length})` : ''}` },
    { id: 'events',   label: `Events${events.length ? ` (${events.length})` : ''}` },
    { id: 'profile',  label: 'My Profile' },
  ];

  return (
    <div className="min-h-screen flex flex-col">

      {/* ── Topbar ─────────────────────────────────────────── */}
      <header className="topbar">
        <div className="topbar-inner">
          <a href="/" className="topbar-brand">
            <span>BPU</span> Portal
          </a>

          <div className="flex items-center gap-3">
            <a
              href="https://pairedbybpu.uk"
              target="_blank"
              rel="noopener noreferrer"
              className="btn btn-outline btn-sm hidden sm:inline-flex"
            >
              PAIRED ↗
            </a>
            <span className="text-sm text-text-2 hidden md:inline">
              {user.display_name}
            </span>
            <a href="/api/auth/logout" className="btn btn-ghost btn-sm">
              Sign out
            </a>
          </div>
        </div>
      </header>

      {/* ── Tab bar ────────────────────────────────────────── */}
      <div className="tab-bar">
        <div className="tab-bar-inner">
          {tabs.map(t => (
            <button
              key={t.id}
              className={`tab-item${tab === t.id ? ' active' : ''}`}
              onClick={() => setTab(t.id)}
            >
              {t.label}
            </button>
          ))}
        </div>
      </div>

      {/* ── Page content ───────────────────────────────────── */}
      <main className="flex-1 wrap py-8">

        {/* ════ OVERVIEW ════════════════════════════════════ */}
        {tab === 'overview' && (
          <div className="space-y-6 fade-up">
            {/* Welcome banner */}
            <div className="card card-p flex flex-col sm:flex-row sm:items-center justify-between gap-4">
              <div>
                <h1 className="text-2xl font-bold">Welcome back, {firstName}</h1>
                <p className="text-sm text-text-2 mt-1">
                  {profile.industryfield_of_expertise
                    ? `${profile.industryfield_of_expertise}${profile.years_of_experience ? ` · ${profile.years_of_experience} yrs exp` : ''}`
                    : 'Complete your profile to unlock personalised recommendations.'}
                </p>
              </div>
              <div className="flex items-center gap-2 shrink-0">
                {isPro && <span className="badge badge-green">Pro</span>}
                <button onClick={() => setTab('profile')} className="btn btn-outline btn-sm">Edit profile</button>
              </div>
            </div>

            {/* Pro upgrade prompt for free members */}
            {!isPro && (
              <div className="card card-p flex flex-col sm:flex-row sm:items-center justify-between gap-4" style={{ borderColor: 'var(--brand)' }}>
                <div>
                  <p className="font-semibold">Unlock AI-powered career tools</p>
                  <p className="text-sm text-text-2 mt-1">CV parsing, job matching, mentor compatibility scores, and weekly career digests.</p>
                </div>
                <a href="/upgrade" className="btn btn-amber btn-sm shrink-0">Upgrade to Pro →</a>
              </div>
            )}

            {/* Stats */}
            <div className="grid grid-cols-3 gap-4">
              {[
                { val: jobs.length,    label: 'Job matches'  },
                { val: courses.length, label: 'Courses'      },
                { val: reviews.length, label: 'CV reviews'   },
              ].map(s => (
                <div key={s.label} className="card card-p text-center">
                  <div className="stat-val">{s.val}</div>
                  <div className="stat-label">{s.label}</div>
                </div>
              ))}
            </div>

            {/* Quick previews */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              {/* Top jobs */}
              <div className="card card-p space-y-4">
                <div className="flex items-center justify-between">
                  <p className="section-title">Top job matches</p>
                  <button onClick={() => setTab('jobs')} className="text-xs text-brand-dark font-semibold hover:underline">View all</button>
                </div>
                {jobs.length === 0
                  ? <div className="empty">No matches today — check back tomorrow.</div>
                  : jobs.slice(0, 3).map(j => (
                    <div key={j.id} className="flex items-start justify-between gap-3 py-2 border-b border-border last:border-0">
                      <div className="min-w-0">
                        <p className="text-sm font-semibold truncate">{j.title}</p>
                        <p className="text-xs text-text-2 mt-0.5">{j.company} · {j.location}</p>
                      </div>
                      {isPro && j.match_score
                        ? <span className="badge badge-amber shrink-0">{j.match_score}%</span>
                        : <span className="badge badge-gray shrink-0 cursor-default" title="Upgrade to Pro">Pro</span>
                      }
                    </div>
                  ))
                }
              </div>

              {/* CV status */}
              <div className="card card-p space-y-4">
                <p className="section-title">CV Clinic</p>
                {cvUrl
                  ? <div className="alert alert-green text-sm">CV on file — <a href={cvUrl} target="_blank" rel="noopener noreferrer" className="underline font-semibold">Download</a></div>
                  : <div className="alert alert-amber text-sm">No CV uploaded yet. Upload a PDF to auto-fill your profile.</div>
                }
                <button onClick={() => setTab('cv')} className="btn btn-outline btn-sm">
                  Go to CV Clinic
                </button>
                {reviews.length > 0 && (
                  <p className="text-sm text-text-2">You have <span className="font-bold text-text">{reviews.length}</span> professional review{reviews.length !== 1 ? 's' : ''}.</p>
                )}
              </div>
            </div>
          </div>
        )}

        {/* ════ CV CLINIC ═══════════════════════════════════ */}
        {tab === 'cv' && (
          <div className="wrap-sm fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
            <div>
              <h2 className="text-xl font-bold">CV Clinic</h2>
              <p className="section-sub">Upload your CV as a PDF. Gemini Pro will parse it and auto-fill your profile.</p>
            </div>

            <ProGate isPro={isPro} feature="CV upload &amp; AI parsing">
              {/* Current CV */}
              {cvUrl && (
                <div className="alert alert-green flex items-center justify-between gap-4">
                  <span className="text-sm">CV on file</span>
                  <a href={cvUrl} target="_blank" rel="noopener noreferrer" className="btn btn-outline btn-sm">
                    Download
                  </a>
                </div>
              )}

              {/* Upload area */}
              <div className="card" style={{ borderStyle: 'dashed' }}>
                <label htmlFor="cv-file" className="block p-12 text-center cursor-pointer space-y-3" style={{ cursor: uploading ? 'not-allowed' : 'pointer' }}>
                  <div className="text-4xl">{uploading ? '⏳' : '📄'}</div>
                  <p className="text-base font-semibold">
                    {uploading ? 'Processing with Gemini Pro…' : 'Click to upload your CV'}
                  </p>
                  <p className="text-sm text-text-2">PDF only · Max 10 MB</p>
                  <input
                    id="cv-file"
                    type="file"
                    accept=".pdf"
                    className="sr-only"
                    onChange={handleCVUpload}
                    disabled={uploading}
                  />
                </label>
              </div>

              {uploading && (
                <div className="h-1.5 w-full bg-border rounded-full overflow-hidden">
                  <div className="h-full bg-brand rounded-full animate-pulse" style={{ width: '70%' }} />
                </div>
              )}

              {uploadMsg && (
                <div className={`alert ${uploadMsg.type === 'ok' ? 'alert-green' : 'alert-red'} text-sm`}>
                  {uploadMsg.text}
                </div>
              )}
            </ProGate>

            {/* Manual reviews */}
            <div className="divider" />
            <div className="space-y-3">
              <div className="flex items-center justify-between">
                <p className="section-title">Professional reviews</p>
                {!isPro && <span className="badge badge-amber">Pro feature</span>}
              </div>

              {isPro ? (
                <>
                  <div className="flex items-center justify-between">
                    <p className="text-sm text-text-2">Request a written critique from a BPU recruiter.</p>
                    <button
                      onClick={handleRequestReview}
                      disabled={requestingReview}
                      className="btn btn-amber btn-sm shrink-0"
                    >
                      {requestingReview ? 'Submitting…' : 'Request review'}
                    </button>
                  </div>
                  {reviewRequestMsg && (
                    <div className={`alert ${reviewRequestMsg.type === 'ok' ? 'alert-green' : 'alert-red'} text-sm`}>
                      {reviewRequestMsg.text}
                    </div>
                  )}
                  {reviews.length === 0
                    ? <div className="empty">No reviews yet.</div>
                    : reviews.map(r => (
                      <div key={r.id} className="card overflow-hidden">
                        <button
                          className="w-full flex items-center justify-between px-5 py-4 text-left text-sm font-semibold hover:bg-bg transition-colors"
                          onClick={() => setOpenReview(openReview === r.id ? null : r.id)}
                        >
                          <span className="flex items-center gap-3">
                            {r.title}
                            {r.score && <span className="badge badge-green">Score: {r.score}/100</span>}
                          </span>
                          <span className="text-text-3">{openReview === r.id ? '▲' : '▼'}</span>
                        </button>
                        {openReview === r.id && (
                          <div className="px-5 pb-5 pt-2 border-t border-border text-sm space-y-3">
                            <p className="whitespace-pre-line leading-relaxed text-text-2">{r.critique}</p>
                            <div className="flex justify-between text-xs text-text-3 pt-2 border-t border-border">
                              <span>Reviewer: {r.reviewer}</span>
                              <span>{new Date(r.date).toLocaleDateString('en-GB')}</span>
                            </div>
                          </div>
                        )}
                      </div>
                    ))
                  }
                </>
              ) : (
                <div className="card card-p text-center py-8 space-y-3">
                  <p className="text-sm text-text-2">Get written feedback from a BPU recruiter with a Pro membership.</p>
                  <a href="/upgrade" className="btn btn-amber btn-sm inline-flex mx-auto">Upgrade to Pro →</a>
                </div>
              )}
            </div>
          </div>
        )}

        {/* ════ JOBS ════════════════════════════════════════ */}
        {tab === 'jobs' && (
          <div className="space-y-4 fade-up">
            <div className="flex items-start justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold">Job matches</h2>
                <p className="section-sub">
                  {isPro
                    ? 'Daily recommendations matched to your profile by AI.'
                    : 'Latest jobs from BPU partners. Upgrade to Pro for AI match scores.'}
                </p>
              </div>
              {!isPro && (
                <a href="/upgrade" className="btn btn-amber btn-sm shrink-0">Unlock AI scores →</a>
              )}
            </div>

            {jobs.length === 0
              ? <div className="empty">No matching jobs today. Check back tomorrow.</div>
              : (
                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                  {jobs.map(j => (
                    <div key={j.id} className="card card-p card-lift flex flex-col gap-3">
                      <div className="flex items-start justify-between gap-2">
                        <div className="min-w-0">
                          <p className="font-semibold text-sm leading-snug">{j.title}</p>
                          <p className="text-xs text-text-2 mt-0.5">{j.company}</p>
                        </div>
                        {isPro && j.match_score
                          ? <span className="badge badge-amber shrink-0">{j.match_score}%</span>
                          : (
                            <span className="badge badge-gray shrink-0" style={{ filter: 'blur(4px)', userSelect: 'none' }}>
                              {j.match_score ?? 75}%
                            </span>
                          )
                        }
                      </div>
                      <div className="flex items-center gap-2 text-xs text-text-3">
                        <span>{j.location}</span>
                        <span>·</span>
                        <span>{j.date_posted}</span>
                      </div>
                      <a
                        href={`/api/jobs/track-click?jobId=${j.id}&url=${encodeURIComponent(j.apply_url)}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="btn btn-amber btn-sm mt-auto"
                      >
                        Apply →
                      </a>
                    </div>
                  ))}
                </div>
              )
            }
          </div>
        )}

        {/* ════ COURSES ═════════════════════════════════════ */}
        {tab === 'courses' && (
          <div className="space-y-4 fade-up">
            <div>
              <h2 className="text-xl font-bold">Accredited courses</h2>
              <p className="section-sub">Progress is tracked in Tutor LMS.</p>
            </div>

            {courses.length === 0
              ? <div className="empty">No courses available right now.</div>
              : (
                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                  {courses.map(c => (
                    <div key={c.id} className="card card-p card-lift flex flex-col gap-3">
                      <div className="flex items-start justify-between gap-2">
                        <span className="text-xs font-semibold text-text-3 uppercase tracking-wide">{c.category}</span>
                        <span className={`badge ${c.status === 'In Progress' ? 'badge-amber' : 'badge-gray'}`}>
                          {c.status}
                        </span>
                      </div>
                      <p className="font-semibold text-sm leading-snug">{c.title}</p>
                      <p className="text-xs text-text-2">by {c.provider}</p>
                      <button
                        onClick={() => handleCourseOpen(c)}
                        className="btn btn-outline btn-sm mt-auto"
                      >
                        Start learning →
                      </button>
                    </div>
                  ))}
                </div>
              )
            }
          </div>
        )}

        {/* ════ EVENTS ══════════════════════════════════════ */}
        {tab === 'events' && (
          <div className="space-y-4 fade-up">
            <div>
              <h2 className="text-xl font-bold">Upcoming events</h2>
              <p className="section-sub">BPU networking events, workshops, and community meetups.</p>
            </div>

            {events.length === 0
              ? (
                <div className="empty">
                  No upcoming events right now — check back soon.
                </div>
              )
              : (
                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                  {events.map(ev => {
                    const start = ev.start_date
                      ? new Date(ev.start_date).toLocaleDateString('en-GB', {
                          weekday: 'short', day: 'numeric', month: 'short', year: 'numeric',
                        })
                      : '';
                    const time = ev.start_date
                      ? new Date(ev.start_date).toLocaleTimeString('en-GB', {
                          hour: '2-digit', minute: '2-digit',
                        })
                      : '';
                    return (
                      <div key={ev.id} className="card card-p card-lift flex flex-col gap-3">
                        {ev.image && (
                          <img
                            src={ev.image}
                            alt={ev.title}
                            className="w-full rounded-lg object-cover"
                            style={{ height: '140px' }}
                          />
                        )}
                        <div className="flex items-start justify-between gap-2">
                          <span className="badge badge-purple text-xs">
                            {ev.is_virtual ? 'Online' : 'In Person'}
                          </span>
                          <span className="text-xs font-semibold text-brand">
                            {ev.cost === 'Free' || !ev.cost ? 'Free' : ev.cost}
                          </span>
                        </div>
                        <p className="font-semibold text-sm leading-snug">{ev.title}</p>
                        {start && (
                          <p className="text-xs text-text-2">
                            {start}{time ? ` · ${time}` : ''}
                          </p>
                        )}
                        {ev.venue && (
                          <p className="text-xs text-text-3 truncate">{ev.venue}</p>
                        )}
                        {ev.description && (
                          <p className="text-xs text-text-2 leading-relaxed line-clamp-2">
                            {ev.description}
                          </p>
                        )}
                        <a
                          href={ev.register_url || ev.url}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="btn btn-amber btn-sm mt-auto"
                        >
                          Register →
                        </a>
                      </div>
                    );
                  })}
                </div>
              )
            }
          </div>
        )}

        {/* ════ PROFILE ═════════════════════════════════════ */}
        {tab === 'profile' && (
          <div className="wrap-sm fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
            <div className="flex items-start justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold">My Profile</h2>
                <p className="section-sub">This information is used for job matching and mentor pairing.</p>
              </div>
              <div className="flex items-center gap-2 shrink-0">
                <button
                  onClick={handleProfileSave}
                  disabled={saving}
                  className="btn btn-amber btn-sm"
                >
                  {saving ? 'Saving…' : 'Save changes'}
                </button>
              </div>
            </div>

            {saveMsg && (
              <div className={`alert ${saveMsg.type === 'ok' ? 'alert-green' : 'alert-red'} text-sm`}>
                {saveMsg.text}
              </div>
            )}

            <div className="card card-p space-y-5">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Account</p>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="field-label">Display name</label>
                  <input className="field-input bg-bg" value={user.display_name} disabled readOnly />
                </div>
                <div>
                  <label className="field-label">Email</label>
                  <input className="field-input bg-bg" value={user.email} disabled readOnly />
                </div>
              </div>
            </div>

            <div className="card card-p space-y-5">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Personal details</p>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {([
                  ['First name',  'first_name'],
                  ['Last name',   'last_name'],
                  ['Phone',       'phone_number'],
                ] as [string, keyof ACFProfile][]).map(([label, key]) => (
                  <div key={key}>
                    <label className="field-label">{label}</label>
                    <input
                      className="field-input"
                      value={(editForm[key] as string) || ''}
                      onChange={e => setEditForm(f => ({ ...f, [key]: e.target.value }))}
                    />
                  </div>
                ))}
                <div>
                  <label className="field-label">Date of birth</label>
                  <input
                    type="date"
                    className="field-input"
                    value={(editForm.birthday as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, birthday: e.target.value }))}
                  />
                </div>
                <div>
                  <label className="field-label">Gender</label>
                  <select
                    className="field-input"
                    value={(editForm.what_is_your_gender as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, what_is_your_gender: e.target.value }))}
                  >
                    <option value="">Select…</option>
                    {['Male', 'Female', 'Prefer Not to Say', 'Others'].map(o => <option key={o} value={o}>{o}</option>)}
                  </select>
                </div>
                <div>
                  <label className="field-label">Sexuality</label>
                  <select
                    className="field-input"
                    value={(editForm.your_sexuality as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, your_sexuality: e.target.value }))}
                  >
                    <option value="">Select…</option>
                    {['Asexual', 'Bisexual', 'Gay', 'Intersex', 'Lesbian', 'Queer', 'Straight', 'Transgender', 'Prefer not to say'].map(o => <option key={o} value={o}>{o}</option>)}
                  </select>
                </div>
              </div>
            </div>

            <div className="card card-p space-y-5">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Location</p>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="sm:col-span-2">
                  <label className="field-label">Country</label>
                  <select
                    className="field-input"
                    value={(editForm.country_location as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, country_location: e.target.value }))}
                  >
                    <option value="">Select…</option>
                    {[
                      'United Kingdom',
                      'Afghanistan','Albania','Algeria','Andorra','Angola','Antigua and Barbuda','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bhutan','Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi','Cabo Verde','Cambodia','Cameroon','Canada','Central African Republic','Chad','Chile','China','Colombia','Comoros','Congo','Costa Rica','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Djibouti','Dominica','Dominican Republic','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Eswatini','Ethiopia','Fiji','Finland','France','Gabon','Gambia','Georgia','Germany','Ghana','Greece','Grenada','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania','Mauritius','Mexico','Micronesia','Moldova','Monaco','Mongolia','Montenegro','Morocco','Mozambique','Myanmar','Namibia','Nauru','Nepal','Netherlands','New Zealand','Nicaragua','Niger','Nigeria','North Korea','North Macedonia','Norway','Oman','Pakistan','Palau','Palestine','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent and the Grenadines','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','South Korea','South Sudan','Spain','Sri Lanka','Sudan','Suriname','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand','Timor-Leste','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu','Uganda','Ukraine','United Arab Emirates','United States','Uruguay','Uzbekistan','Vanuatu','Vatican City','Venezuela','Vietnam','Yemen','Zambia','Zimbabwe',
                    ].map(c => <option key={c} value={c}>{c}</option>)}
                  </select>
                </div>
                {editForm.country_location === 'United Kingdom' && (
                  <div>
                    <label className="field-label">Where in the UK?</label>
                    <select
                      className="field-input"
                      value={(editForm.where_in_the_uk as string) || ''}
                      onChange={e => setEditForm(f => ({ ...f, where_in_the_uk: e.target.value }))}
                    >
                      <option value="">Select…</option>
                      {['England', 'Scotland', 'Wales', 'Northern Ireland'].map(o => <option key={o} value={o}>{o}</option>)}
                    </select>
                  </div>
                )}
                <div>
                  <label className="field-label">City</label>
                  <input
                    className="field-input"
                    value={(editForm.location_city as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, location_city: e.target.value }))}
                  />
                </div>
              </div>
            </div>

            <div className="card card-p space-y-5">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Background</p>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="field-label">Education level</label>
                  <select
                    className="field-input"
                    value={(editForm.level_of_education as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, level_of_education: e.target.value }))}
                  >
                    <option value="">Select…</option>
                    {["High School","Bachelor's Degree","Professional Qualification","Masters Degree","PhD","Other","Prefer Not to Answer"].map(o => <option key={o} value={o}>{o}</option>)}
                  </select>
                </div>
                <div>
                  <label className="field-label">Ethnicity</label>
                  <select
                    className="field-input"
                    value={(editForm.how_would_you_best_describe_your_ethnicity as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, how_would_you_best_describe_your_ethnicity: e.target.value }))}
                  >
                    <option value="">Select…</option>
                    {['African','Asian','Black British','Black Caribbean','Gypsy or Irish Traveller','Hispanic','Mixed Ethnic Group','Other Black Background','White'].map(o => <option key={o} value={o}>{o}</option>)}
                  </select>
                </div>
                <div>
                  <label className="field-label">First-generation immigrant?</label>
                  <select
                    className="field-input"
                    value={(editForm['first-generation_immigrant'] as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, 'first-generation_immigrant': e.target.value }))}
                  >
                    <option value="">Select…</option>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                  </select>
                </div>
                <div>
                  <label className="field-label">Disability</label>
                  <select
                    className="field-input"
                    value={(editForm.do_you_have_any_disabilities_or_accessibility_needs_we_should_be_aware_of as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, do_you_have_any_disabilities_or_accessibility_needs_we_should_be_aware_of: e.target.value }))}
                  >
                    <option value="">Select…</option>
                    {['No Disability','Cognitive or learning disability','Hearing impairment','Mobility impairment','Visual impairment','Others (Input below)'].map(o => <option key={o} value={o}>{o}</option>)}
                  </select>
                </div>
                {editForm.do_you_have_any_disabilities_or_accessibility_needs_we_should_be_aware_of === 'Others (Input below)' && (
                  <div className="sm:col-span-2">
                    <label className="field-label">Please describe your disability</label>
                    <input
                      className="field-input"
                      placeholder="Describe your disability…"
                      value={(editForm.other_disability as string) || ''}
                      onChange={e => setEditForm(f => ({ ...f, other_disability: e.target.value }))}
                    />
                  </div>
                )}
              </div>
            </div>

            <div className="card card-p space-y-5">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Career</p>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="field-label">Employment status</label>
                  <select
                    className="field-input"
                    value={(editForm.current_employment_status as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, current_employment_status: e.target.value }))}
                  >
                    <option value="">Select…</option>
                    {['Employed Full-Time','Employed Part-Time','Self-employed','Not employed but looking for work','Not employed and not looking for work','Retired','Student','Prefer Not to Answer'].map(o => <option key={o} value={o}>{o}</option>)}
                  </select>
                </div>
                <div>
                  <label className="field-label">Industry</label>
                  <select
                    className="field-input"
                    value={(editForm.industry as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, industry: e.target.value }))}
                  >
                    <option value="">Select…</option>
                    {['Accounting','Administration & Office Support','Advertising, Arts & Media','Banking & Financial Services','Call Centre & Customer Service','Community Services & Development','Construction','Consulting & Strategy','Education & Training','Engineering','Farming, Animals & Conservation','Government & Defence','Healthcare & Medical','Hospitality & Tourism','Human Resources & Recruitment','Information & Communication Technology','Insurance & Superannuation','Legal','Manufacturing, Transport & Logistics','Marketing & Communications','Mining, Resources & Energy','Real Estate & Property','Retail & Consumer Products','Sales','Science & Technology','Self Employment','Sport & Recreation','Trades & Services','Other'].map(o => <option key={o} value={o}>{o}</option>)}
                  </select>
                </div>
                <div>
                  <label className="field-label">Field of expertise</label>
                  <select
                    className="field-input"
                    value={(editForm.industryfield_of_expertise as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, industryfield_of_expertise: e.target.value }))}
                  >
                    <option value="">Select…</option>
                    {['Accounting & Finance','Administration','Arts & Design','Business Development','Consulting','Customer Service','Data & Analytics','Education','Engineering','Healthcare','HR & Recruitment','IT & Software','Law & Legal Services','Logistics & Supply Chain','Management','Marketing & Communications','Media & Journalism','Operations','Policy & Government','Project Management','Property & Real Estate','Research & Science','Sales','Social Work & Community','Sport & Fitness','Other'].map(o => <option key={o} value={o}>{o}</option>)}
                  </select>
                </div>
                <div>
                  <label className="field-label">Years of experience</label>
                  <select
                    className="field-input"
                    value={(editForm.years_of_experience as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, years_of_experience: e.target.value }))}
                  >
                    <option value="">Select…</option>
                    {[...Array.from({ length: 40 }, (_, i) => String(i + 1)), '40+'].map(o => <option key={o} value={o}>{o}</option>)}
                  </select>
                </div>
                {editForm.industryfield_of_expertise === 'Other' && (
                  <div className="sm:col-span-2">
                    <label className="field-label">Please specify your field of expertise</label>
                    <input
                      className="field-input"
                      placeholder="Your field…"
                      value={(editForm.expertise_not_listed as string) || ''}
                      onChange={e => setEditForm(f => ({ ...f, expertise_not_listed: e.target.value }))}
                    />
                  </div>
                )}
                <div className="sm:col-span-2">
                  <label className="field-label">Skills (comma-separated)</label>
                  <input
                    className="field-input"
                    placeholder="e.g. React, Product Strategy, SQL"
                    value={(editForm.skills_separate as string) || ''}
                    onChange={e => setEditForm(f => ({ ...f, skills_separate: e.target.value }))}
                  />
                </div>
              </div>
            </div>

            <div className="card card-p space-y-4">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Bio</p>
              <div>
                <label className="field-label">Professional biography</label>
                <textarea
                  className="field-input field-textarea"
                  placeholder="Write a short professional bio…"
                  value={(editForm.user_bio as string) || ''}
                  onChange={e => setEditForm(f => ({ ...f, user_bio: e.target.value }))}
                />
              </div>
            </div>

            <div className="flex justify-end">
              <button
                onClick={handleProfileSave}
                disabled={saving}
                className="btn btn-amber"
              >
                {saving ? 'Saving…' : 'Save changes'}
              </button>
            </div>

            {/* Email preferences — Pro only */}
            <div className="divider" />
            <div className="card card-p space-y-4">
              <div className="flex items-center justify-between">
                <p className="text-xs font-bold uppercase tracking-wide text-text-3">Email Preferences</p>
                {!isPro && <span className="badge badge-amber">Pro</span>}
              </div>
              {isPro ? (
                <>
                  <label className="flex items-center gap-3 cursor-pointer">
                    <input
                      type="checkbox"
                      checked={weeklyEmails}
                      onChange={e => setWeeklyEmails(e.target.checked)}
                      className="w-4 h-4"
                    />
                    <span className="text-sm">Weekly job digest — top matches every Monday</span>
                  </label>
                  {prefMsg && (
                    <div className={`alert ${prefMsg.type === 'ok' ? 'alert-green' : 'alert-red'} text-sm`}>
                      {prefMsg.text}
                    </div>
                  )}
                  <button onClick={handlePrefSave} disabled={prefSaving} className="btn btn-outline btn-sm">
                    {prefSaving ? 'Saving…' : 'Save preferences'}
                  </button>
                </>
              ) : (
                <div className="flex items-center justify-between gap-4">
                  <p className="text-sm text-text-2">Weekly job digest and notification controls are available with Pro.</p>
                  <a href="/upgrade" className="btn btn-amber btn-sm shrink-0">Upgrade →</a>
                </div>
              )}
            </div>
          </div>
        )}

      </main>
    </div>
  );
}
