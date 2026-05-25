'use client';

import React, { useState } from 'react';
import { BPUUser, ACFProfile } from '@/lib/auth';
import { JobListing, CourseItem, CVReview, BPUApi } from '@/lib/api';

type Tab = 'overview' | 'cv' | 'jobs' | 'courses' | 'profile';

interface Props {
  user: BPUUser;
  initialJobs: JobListing[];
  initialCourses: CourseItem[];
  initialReviews: CVReview[];
  jwt: string;
}

export default function ClientDashboard({ user, initialJobs, initialCourses, initialReviews, jwt }: Props) {
  const [tab, setTab] = useState<Tab>('overview');
  const [profile, setProfile] = useState<ACFProfile>(user.profile);
  const [cvUrl, setCvUrl] = useState(user.cv_url || '');
  const [jobs] = useState<JobListing[]>(initialJobs);
  const [courses, setCourses] = useState<CourseItem[]>(initialCourses);
  const [reviews] = useState<CVReview[]>(initialReviews);

  // CV upload
  const [uploading, setUploading] = useState(false);
  const [uploadMsg, setUploadMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);

  // CV review accordion
  const [openReview, setOpenReview] = useState<number | null>(null);

  // Profile edit
  const [editForm, setEditForm] = useState<Partial<ACFProfile>>(profile);
  const [saving, setSaving] = useState(false);
  const [saveMsg, setSaveMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);

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

  const firstName = profile.first_name || user.display_name.split(' ')[0];

  const tabs: { id: Tab; label: string }[] = [
    { id: 'overview', label: 'Overview' },
    { id: 'cv',       label: 'CV Clinic' },
    { id: 'jobs',     label: `Jobs${jobs.length ? ` (${jobs.length})` : ''}` },
    { id: 'courses',  label: `Courses${courses.length ? ` (${courses.length})` : ''}` },
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
              <button onClick={() => setTab('profile')} className="btn btn-outline btn-sm shrink-0">
                Edit profile
              </button>
            </div>

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
                      {j.match_score && (
                        <span className="badge badge-amber shrink-0">{j.match_score}%</span>
                      )}
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

            {/* Manual reviews */}
            <div className="divider" />
            <div className="space-y-3">
              <div className="flex items-center justify-between">
                <p className="section-title">Professional reviews</p>
                <span className="badge badge-amber">Pro feature</span>
              </div>
              <p className="text-sm text-text-2">Manual critiques from BPU recruiters. Upgrade to Pro to request one.</p>

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
            </div>
          </div>
        )}

        {/* ════ JOBS ════════════════════════════════════════ */}
        {tab === 'jobs' && (
          <div className="space-y-4 fade-up">
            <div>
              <h2 className="text-xl font-bold">Job matches</h2>
              <p className="section-sub">Daily recommendations semantically matched to your profile.</p>
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
                        {j.match_score && <span className="badge badge-amber shrink-0">{j.match_score}%</span>}
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
              <p className="section-sub">Courses by BPU partner providers. Progress is tracked in Tutor LMS.</p>
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
                  ['First name',   'first_name'],
                  ['Last name',    'last_name'],
                  ['Phone',        'phone_number'],
                  ['Age range',    'age_range'],
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
              </div>
            </div>

            <div className="card card-p space-y-5">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Location</p>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {([
                  ['Country',      'country_location'],
                  ['City',         'location_city'],
                  ['UK region',    'where_in_the_uk'],
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
              </div>
            </div>

            <div className="card card-p space-y-5">
              <p className="text-xs font-bold uppercase tracking-wide text-text-3">Career</p>
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {([
                  ['Employment status',   'current_employment_status'],
                  ['Industry / expertise','industryfield_of_expertise'],
                  ['Years of experience', 'years_of_experience'],
                  ['Education level',     'level_of_education'],
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
          </div>
        )}

      </main>
    </div>
  );
}
