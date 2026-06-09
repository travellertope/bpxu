'use client';

import React, { useState } from 'react';
import { BPUUser, ACFProfile, WorkExperience, Education, Certification } from '@/lib/auth';
import { JobListing, CourseItem, CVReview, EventItem, BPUApi } from '@/lib/api';

function ProGate({ children, isPro, feature }: { children: React.ReactNode; isPro: boolean; feature: string }) {
    if (isPro) return <>{children}</>;
    return (
        <div style={{ position: 'relative' }}>
            {/* Blurred preview */}
            <div style={{ filter: 'blur(4px)', pointerEvents: 'none', userSelect: 'none', opacity: 0.5 }} aria-hidden="true">
                {children}
            </div>
            {/* Overlay */}
            <div
                className="absolute inset-0 flex flex-col items-center justify-center gap-3"
                style={{ background: 'rgba(0,0,0,0.7)', borderRadius: 'var(--radius)', padding: '2rem' }}
            >
                <p className="text-3xl">★</p>
                <p className="text-white font-bold text-base text-center">{feature}</p>
                <p className="text-white/75 text-sm text-center">Requires BPU Pro membership</p>
                <a href="/upgrade" className="btn btn-amber btn-sm">Upgrade to Pro →</a>
            </div>
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
  const [cvTab, setCvTab] = useState<'analyse' | 'upload' | 'review'>('analyse');
  const [profile, setProfile] = useState<ACFProfile>(user.profile);
  const [cvUrl, setCvUrl] = useState(user.cv_url || '');
  const [jobs] = useState<JobListing[]>(initialJobs);
  const [courses, setCourses] = useState<CourseItem[]>(initialCourses);
  const [reviews] = useState<CVReview[]>(initialReviews);
  const [events] = useState<EventItem[]>(initialEvents);

  // CV Analyzer (free feature)
  const [analyzeRole, setAnalyzeRole] = useState('');
  const [analyzeJD, setAnalyzeJD] = useState('');
  const [analyzeCvFile, setAnalyzeCvFile] = useState<File | null>(null);
  const [analyzing, setAnalyzing] = useState(false);
  const [analyzeMsg, setAnalyzeMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);
  const [analyzeResult, setAnalyzeResult] = useState<{
    score: number;
    strengths: string[];
    weaknesses: string[];
    recommendation: string;
  } | null>(null);

  const handleCVAnalyze = async () => {
    if (!analyzeRole.trim()) {
      setAnalyzeMsg({ type: 'err', text: 'Please enter a target role.' });
      return;
    }
    if (!cvUrl && !analyzeCvFile) {
      setAnalyzeMsg({ type: 'err', text: 'Please upload a CV to analyze.' });
      return;
    }
    setAnalyzing(true);
    setAnalyzeMsg(null);
    setAnalyzeResult(null);
    try {
      const form = new FormData();
      form.append('target_role', analyzeRole.trim());
      if (analyzeJD.trim()) form.append('job_description', analyzeJD.trim());
      if (analyzeCvFile) form.append('cv_file', analyzeCvFile);
      const res = await fetch('/api/member/cv-analyze', { method: 'POST', body: form });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Analysis failed.');
      setAnalyzeResult(data);
    } catch (err: unknown) {
      setAnalyzeMsg({ type: 'err', text: err instanceof Error ? err.message : 'Analysis error.' });
    } finally {
      setAnalyzing(false);
    }
  };

  // Structured CV data (parsed from CV, read-only)
  const [experiences, setExperiences] = useState<WorkExperience[]>(user.experiences || []);
  const [educations, setEducations] = useState<Education[]>(user.educations || []);
  const [certifications, setCertifications] = useState<Certification[]>(user.certifications || []);
  const [cvLanguages, setCvLanguages] = useState<string>(user.languages || '');
  const [cvParsedAt, setCvParsedAt] = useState<string>(user.cv_parsed_at || '');

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

      // Re-fetch the full profile from WP to get all ACF-saved fields
      const profileRes = await fetch('/api/member/profile', { cache: 'no-store' });
      if (profileRes.ok) {
        const profileData = await profileRes.json();
        if (profileData.profile) {
          setProfile(profileData.profile);
          setEditForm(profileData.profile);
        }
        if (profileData.experiences?.length)    setExperiences(profileData.experiences);
        if (profileData.educations?.length)     setEducations(profileData.educations);
        if (profileData.certifications?.length) setCertifications(profileData.certifications);
        if (profileData.languages)              setCvLanguages(profileData.languages);
        if (profileData.cv_parsed_at)           setCvParsedAt(profileData.cv_parsed_at);
      } else {
        // Fallback: merge only non-empty parsed fields into form state
        const parsed = data.parsed_data || {};
        const nonEmpty = Object.fromEntries(
          Object.entries(parsed).filter(([, v]) => v !== '' && v !== null && v !== undefined && !Array.isArray(v))
        );
        setProfile(prev => ({ ...prev, ...nonEmpty }));
        setEditForm(prev => ({ ...prev, ...nonEmpty }));
        if (parsed.work_experiences?.length)  setExperiences(parsed.work_experiences);
        if (parsed.education_history?.length) setEducations(parsed.education_history);
        if (parsed.certifications?.length)    setCertifications(parsed.certifications);
        if (parsed.languages)                 setCvLanguages(parsed.languages);
        setCvParsedAt(new Date().toLocaleString());
      }
      setUploadMsg({ type: 'ok', text: 'CV uploaded — your profile has been updated automatically.' });
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
            <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals United" />
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
              {(() => {
                const inProgress = courses.filter(c => c.status === 'In Progress').length;
                const completed  = courses.filter(c => c.status === 'Completed').length;
                const upcomingEvents = events.filter(ev => ev.start_date && new Date(ev.start_date) >= new Date()).length;
                return [
                  {
                    val:   courses.length,
                    label: courses.length === 0 ? 'Enrolled courses' : inProgress > 0 ? `${inProgress} in progress` : completed > 0 ? `${completed} completed` : 'Enrolled',
                    sub:   'Courses',
                    tab:   'courses' as Tab,
                  },
                  {
                    val:   upcomingEvents,
                    label: upcomingEvents === 1 ? 'Upcoming event' : 'Upcoming events',
                    sub:   'Events',
                    tab:   'events' as Tab,
                  },
                  {
                    val:   reviews.length,
                    label: reviews.length === 1 ? 'Review received' : 'Reviews received',
                    sub:   'CV Clinic',
                    tab:   'cv' as Tab,
                  },
                ];
              })().map(s => (
                <button
                  key={s.sub}
                  onClick={() => setTab(s.tab)}
                  className="card card-p text-center hover:border-brand transition-colors cursor-pointer"
                  style={{ background: 'none', width: '100%' }}
                >
                  <div className="stat-val">{s.val}</div>
                  <div className="stat-label">{s.label}</div>
                  <div className="text-xs text-text-3 mt-1">{s.sub}</div>
                </button>
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
                      {isPro && j.match_score && (
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
                  ? <div className="alert alert-green text-sm flex items-center justify-between gap-3">
                      <span>CV on file</span>
                      <div className="flex items-center gap-2">
                        <a href={cvUrl} target="_blank" rel="noopener noreferrer" className="underline font-semibold">Download</a>
                        <span className="text-text-3">·</span>
                        <button onClick={() => { setTab('cv'); setCvTab('upload'); }} className="underline font-semibold">Replace</button>
                      </div>
                    </div>
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
              <p className="section-sub">Three ways to get the most out of your CV — pick the tool that fits your need.</p>
            </div>

            {/* ── Sub-tab bar ── */}
            <div className="card" style={{ padding: '6px', display: 'flex', gap: '4px' }}>
              {(
                [
                  { id: 'analyse', label: 'AI Analysis', badge: 'Free' },
                  { id: 'upload',  label: 'Upload & Parse', badge: isPro ? undefined : 'Pro' },
                  { id: 'review',  label: 'Expert Review',  badge: isPro ? undefined : 'Pro' },
                ] as const
              ).map(({ id, label, badge }) => (
                <button
                  key={id}
                  onClick={() => setCvTab(id)}
                  className={cvTab === id ? 'btn btn-amber btn-sm flex-1' : 'btn btn-ghost btn-sm flex-1'}
                  style={{ justifyContent: 'center', gap: '6px' }}
                >
                  {label}
                  {badge && (
                    <span className={`badge ${badge === 'Free' ? 'badge-green' : 'badge-amber'}`} style={{ fontSize: '10px', padding: '1px 5px' }}>
                      {badge}
                    </span>
                  )}
                </button>
              ))}
            </div>

            {/* ── Tab: AI Analysis ── */}
            {cvTab === 'analyse' && (
              <div className="card card-p space-y-5">
                <div>
                  <p className="section-title">Instant AI Analysis</p>
                  <p className="text-sm text-text-2">
                    Paste a job description and upload your CV — our AI scores how well you match the role and highlights
                    specific strengths and gaps. <strong>Free for all members.</strong> No CV saved to your profile.
                  </p>
                </div>

                <div className="space-y-3">
                  <div>
                    <label className="field-label">Target role <span className="text-red-400">*</span></label>
                    <input
                      className="field-input"
                      placeholder="e.g. Senior Software Engineer"
                      value={analyzeRole}
                      onChange={e => setAnalyzeRole(e.target.value)}
                      disabled={analyzing}
                    />
                  </div>
                  <div>
                    <label className="field-label">Job description <span className="text-text-3 font-normal">(optional — paste the JD for a more accurate score)</span></label>
                    <textarea
                      className="field-input field-textarea"
                      placeholder="Paste the job description here…"
                      rows={4}
                      value={analyzeJD}
                      onChange={e => setAnalyzeJD(e.target.value)}
                      disabled={analyzing}
                    />
                  </div>
                  {cvUrl
                    ? <p className="text-xs text-text-2">Using your saved CV. <label htmlFor="analyze-cv-file" className="underline cursor-pointer">Use a different file</label>
                        {analyzeCvFile && <span className="ml-2 text-brand">✓ {analyzeCvFile.name}</span>}
                        <input id="analyze-cv-file" type="file" accept=".pdf" className="sr-only" onChange={e => setAnalyzeCvFile(e.target.files?.[0] ?? null)} />
                      </p>
                    : <div>
                        <label className="field-label">CV (PDF) <span className="text-red-400">*</span></label>
                        <label htmlFor="analyze-cv-file" className="block card p-4 text-center text-sm text-text-2 cursor-pointer" style={{ borderStyle: 'dashed' }}>
                          {analyzeCvFile ? <span className="text-brand font-medium">✓ {analyzeCvFile.name}</span> : 'Click to upload PDF'}
                        </label>
                        <input id="analyze-cv-file" type="file" accept=".pdf" className="sr-only" onChange={e => setAnalyzeCvFile(e.target.files?.[0] ?? null)} />
                      </div>
                  }
                  {analyzeMsg && (
                    <div className={`alert ${analyzeMsg.type === 'ok' ? 'alert-green' : 'alert-red'} text-sm`}>
                      {analyzeMsg.text}
                    </div>
                  )}
                  <button
                    onClick={handleCVAnalyze}
                    disabled={analyzing}
                    className="btn btn-amber"
                  >
                    {analyzing ? 'Analysing…' : 'Analyse my CV'}
                  </button>
                </div>

                {analyzing && (
                  <div className="space-y-2 text-center py-4">
                    <div className="h-1.5 w-full bg-border rounded-full overflow-hidden">
                      <div className="h-full bg-brand rounded-full animate-pulse" style={{ width: '60%' }} />
                    </div>
                    <p className="text-sm text-text-2">Our system is reviewing your CV…</p>
                  </div>
                )}

                {analyzeResult && (
                  <div className="space-y-4 pt-2 border-t border-border">
                    {/* Score */}
                    <div className="flex items-center gap-5">
                      <div
                        className="shrink-0 w-20 h-20 rounded-full flex items-center justify-center text-2xl font-extrabold"
                        style={{
                          background: analyzeResult.score >= 70 ? 'var(--green-bg)' : analyzeResult.score >= 45 ? '#fef9c3' : '#fee2e2',
                          color: analyzeResult.score >= 70 ? 'var(--green)' : analyzeResult.score >= 45 ? '#a16207' : '#b91c1c',
                        }}
                      >
                        {analyzeResult.score}
                      </div>
                      <div>
                        <p className="font-bold">Match Score</p>
                        <p className="text-sm text-text-2">
                          {analyzeResult.score >= 70 ? 'Strong match for this role.' : analyzeResult.score >= 45 ? 'Moderate match — some gaps to address.' : 'Low match — significant improvements recommended.'}
                        </p>
                      </div>
                    </div>

                    {/* Strengths */}
                    {analyzeResult.strengths.length > 0 && (
                      <div className="space-y-2">
                        <p className="text-xs font-bold uppercase tracking-wide" style={{ color: 'var(--green)' }}>Strengths</p>
                        <ul className="space-y-1">
                          {analyzeResult.strengths.map((s, i) => (
                            <li key={i} className="flex items-start gap-2 text-sm">
                              <span style={{ color: 'var(--green)' }} className="mt-0.5 shrink-0">✓</span> {s}
                            </li>
                          ))}
                        </ul>
                      </div>
                    )}

                    {/* Weaknesses */}
                    {analyzeResult.weaknesses.length > 0 && (
                      <div className="space-y-2">
                        <p className="text-xs font-bold uppercase tracking-wide text-red-500">Areas to improve</p>
                        <ul className="space-y-1">
                          {analyzeResult.weaknesses.map((w, i) => (
                            <li key={i} className="flex items-start gap-2 text-sm">
                              <span className="text-red-400 mt-0.5 shrink-0">✗</span> {w}
                            </li>
                          ))}
                        </ul>
                      </div>
                    )}

                    {/* Recommendation */}
                    {analyzeResult.recommendation && (
                      <div className="card card-p space-y-1" style={{ background: 'var(--bg-2)' }}>
                        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Top recommendation</p>
                        <p className="text-sm leading-relaxed">{analyzeResult.recommendation}</p>
                      </div>
                    )}

                    <button
                      onClick={() => { setAnalyzeResult(null); setAnalyzeRole(''); setAnalyzeJD(''); setAnalyzeCvFile(null); }}
                      className="btn btn-ghost btn-sm"
                    >
                      Analyse again
                    </button>
                  </div>
                )}
              </div>
            )}

            {/* ── Tab: Upload & Parse ── */}
            {cvTab === 'upload' && (
              <div className="space-y-4">
                <div className="card card-p space-y-1">
                  <p className="section-title">Upload &amp; Auto-fill Profile</p>
                  <p className="text-sm text-text-2">
                    Upload your CV and our AI will read it and automatically populate your BPU profile — work history,
                    education, certifications, and more. Your CV is also stored so recruiters can view it. <strong>Pro members only.</strong>
                  </p>
                </div>

                <ProGate isPro={isPro} feature="CV upload &amp; AI parsing">
                  {/* Current CV */}
                  {cvUrl && (
                    <div className="alert alert-green flex items-center justify-between gap-4">
                      <span className="text-sm font-medium">CV on file</span>
                      <div className="flex items-center gap-2">
                        <a href={cvUrl} target="_blank" rel="noopener noreferrer" className="btn btn-outline btn-sm">
                          Download
                        </a>
                        <label htmlFor="cv-file" className="btn btn-outline btn-sm" style={{ cursor: uploading ? 'not-allowed' : 'pointer' }}>
                          Replace
                        </label>
                      </div>
                    </div>
                  )}

                  {/* Upload area */}
                  <div className="card" style={{ borderStyle: 'dashed' }}>
                    <label htmlFor="cv-file" className="block p-12 text-center cursor-pointer space-y-3" style={{ cursor: uploading ? 'not-allowed' : 'pointer' }}>
                      <div className="text-4xl">{uploading ? '⏳' : '📄'}</div>
                      <p className="text-base font-semibold">
                        {uploading ? 'Reading your CV…' : cvUrl ? 'Click to replace your CV' : 'Click to upload your CV'}
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
              </div>
            )}

            {/* ── Tab: Expert Review ── */}
            {cvTab === 'review' && (
              <div className="space-y-4">
                <div className="card card-p space-y-1">
                  <p className="section-title">Expert Review by a BPU Recruiter</p>
                  <p className="text-sm text-text-2">
                    A real BPU recruiter reads your CV and writes a personalised, actionable critique — covering
                    structure, language, keywords, and presentation. Typically returned within 3–5 business days. <strong>Pro members only.</strong>
                  </p>
                </div>

                {isPro ? (
                  <div className="space-y-3">
                    <div className="flex items-center justify-between">
                      <p className="text-sm text-text-2">Submit your CV for a written critique.</p>
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
                  </div>
                ) : (
                  <div className="card card-p text-center py-8 space-y-3">
                    <p className="text-sm text-text-2">Get written feedback from a BPU recruiter with a Pro membership.</p>
                    <a href="/upgrade" className="btn btn-amber btn-sm inline-flex mx-auto">Upgrade to Pro →</a>
                  </div>
                )}
              </div>
            )}
          </div>
        )}

        {/* ════ JOBS ════════════════════════════════════════ */}
        {tab === 'jobs' && (
          <div className="space-y-4 fade-up">
            <div>
              <h2 className="text-xl font-bold">Job matches</h2>
              <p className="section-sub">Daily AI recommendations matched to your profile.</p>
            </div>

            <ProGate isPro={isPro} feature="AI Job Matching">
              <div>
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
            </ProGate>
          </div>
        )}

        {/* ════ COURSES ═════════════════════════════════════ */}
        {tab === 'courses' && (
          <div className="space-y-4 fade-up">
            <div className="flex items-start justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold">My Courses</h2>
                <p className="section-sub">Courses you&apos;re enrolled in through BPU.</p>
              </div>
              <a href="/courses" className="btn btn-outline btn-sm shrink-0">Browse all courses →</a>
            </div>

            {courses.length === 0
              ? (
                <div className="card card-p text-center py-12 space-y-4">
                  <p className="text-3xl">🎓</p>
                  <div>
                    <p className="font-semibold">No enrolled courses yet</p>
                    <p className="text-sm text-text-2 mt-1">Browse BPU&apos;s accredited courses and enrol to see them here.</p>
                  </div>
                  <a href="/courses" className="btn btn-amber btn-sm inline-flex mx-auto">Browse courses →</a>
                </div>
              )
              : (
                <div className="space-y-3">
                  {courses.map(c => {
                    const statusColor = c.status === 'Completed' ? 'badge-green' : c.status === 'In Progress' ? 'badge-amber' : 'badge-gray';
                    return (
                      <div key={c.id} className="card card-p flex gap-4 items-start">
                        {c.image && (
                          <img
                            src={c.image}
                            alt={c.title}
                            className="rounded-lg object-cover shrink-0"
                            style={{ width: '72px', height: '72px' }}
                          />
                        )}
                        <div className="flex-1 min-w-0 space-y-1.5">
                          <div className="flex items-center gap-2 flex-wrap">
                            <span className="text-xs font-semibold text-text-3 uppercase tracking-wide">{c.category}</span>
                            <span className={`badge ${statusColor} ml-auto`}>{c.status}</span>
                          </div>
                          <p className="font-semibold text-sm leading-snug">{c.title}</p>
                          <p className="text-xs text-text-3">by {c.provider}{c.duration ? ` · ${c.duration}` : ''}</p>
                          {typeof c.progress === 'number' && c.progress > 0 && c.status !== 'Completed' && (
                            <div className="space-y-1">
                              <div className="h-1.5 w-full bg-border rounded-full overflow-hidden">
                                <div
                                  className="h-full rounded-full"
                                  style={{ width: `${c.progress}%`, background: 'var(--brand)' }}
                                />
                              </div>
                              <p className="text-xs text-text-3">{c.progress}% complete</p>
                            </div>
                          )}
                        </div>
                        <button
                          onClick={() => handleCourseOpen(c)}
                          className="btn btn-ghost btn-sm shrink-0"
                        >
                          {c.status === 'Completed' ? 'Review →' : 'Continue →'}
                        </button>
                      </div>
                    );
                  })}
                </div>
              )
            }
          </div>
        )}

        {/* ════ EVENTS ══════════════════════════════════════ */}
        {tab === 'events' && (
          <div className="space-y-4 fade-up">
            <div className="flex items-start justify-between gap-4">
              <div>
                <h2 className="text-xl font-bold">My Events</h2>
                <p className="section-sub">Events you&apos;ve registered for through BPU.</p>
              </div>
              <a href="/events" className="btn btn-outline btn-sm shrink-0">Browse all events →</a>
            </div>

            {events.length === 0
              ? (
                <div className="card card-p text-center py-12 space-y-4">
                  <p className="text-3xl">🗓️</p>
                  <div>
                    <p className="font-semibold">No registered events yet</p>
                    <p className="text-sm text-text-2 mt-1">Browse upcoming BPU events and register to see them here.</p>
                  </div>
                  <a href="/events" className="btn btn-amber btn-sm inline-flex mx-auto">Browse events →</a>
                </div>
              )
              : (
                <div className="space-y-3">
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
                    const isPast = ev.start_date ? new Date(ev.start_date) < new Date() : false;
                    return (
                      <div
                        key={ev.id}
                        className="card card-p flex gap-4 items-start"
                        style={{ opacity: isPast ? 0.6 : 1 }}
                      >
                        {ev.image && (
                          <img
                            src={ev.image}
                            alt={ev.title}
                            className="rounded-lg object-cover shrink-0"
                            style={{ width: '72px', height: '72px' }}
                          />
                        )}
                        <div className="flex-1 min-w-0 space-y-1">
                          <div className="flex items-center gap-2 flex-wrap">
                            <span className="badge badge-purple text-xs">
                              {ev.is_virtual ? 'Online' : 'In Person'}
                            </span>
                            {isPast && <span className="badge badge-gray text-xs">Past</span>}
                            <span className="text-xs font-semibold text-brand ml-auto">
                              {ev.cost === 'Free' || !ev.cost ? 'Free' : ev.cost}
                            </span>
                          </div>
                          <p className="font-semibold text-sm leading-snug">{ev.title}</p>
                          {start && (
                            <p className="text-xs text-text-2">{start}{time ? ` · ${time}` : ''}</p>
                          )}
                          {ev.venue && <p className="text-xs text-text-3 truncate">{ev.venue}</p>}
                        </div>
                        <a
                          href={ev.register_url || ev.url}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="btn btn-ghost btn-sm shrink-0"
                        >
                          View →
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

            {/* CV-parsed structured data */}
            {(experiences.length > 0 || educations.length > 0 || certifications.length > 0 || cvLanguages) && (
              <>
                <div className="divider" />
                <div className="flex items-center justify-between gap-2">
                  <p className="text-xs font-bold uppercase tracking-wide text-text-3">Parsed from your CV</p>
                  {cvParsedAt && <p className="text-xs text-text-3">Last updated: {cvParsedAt}</p>}
                </div>
                <p className="text-xs text-text-2">This information was automatically extracted from your uploaded CV. Re-upload your CV to refresh it.</p>

                {experiences.length > 0 && (
                  <div className="card card-p space-y-4">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Work Experience</p>
                    <div className="space-y-4">
                      {experiences.map((exp, i) => (
                        <div key={i} className="border-l-2 border-brand pl-4 space-y-1">
                          <p className="font-semibold text-sm">{exp.title}</p>
                          <p className="text-sm text-text-2">{exp.company}</p>
                          {(exp.start_date || exp.end_date) && (
                            <p className="text-xs text-text-3">
                              {exp.start_date || ''}{exp.start_date && (exp.end_date || exp.is_current) ? ' – ' : ''}{exp.is_current ? 'Present' : exp.end_date || ''}
                            </p>
                          )}
                          {exp.description && <p className="text-xs text-text-2 leading-relaxed">{exp.description}</p>}
                        </div>
                      ))}
                    </div>
                  </div>
                )}

                {educations.length > 0 && (
                  <div className="card card-p space-y-4">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Education</p>
                    <div className="space-y-3">
                      {educations.map((edu, i) => (
                        <div key={i} className="border-l-2 border-brand pl-4 space-y-1">
                          <p className="font-semibold text-sm">{edu.institution}</p>
                          {(edu.degree || edu.field_of_study) && (
                            <p className="text-sm text-text-2">{[edu.degree, edu.field_of_study].filter(Boolean).join(', ')}</p>
                          )}
                          {(edu.start_year || edu.end_year) && (
                            <p className="text-xs text-text-3">{edu.start_year || ''}{edu.start_year && edu.end_year ? ' – ' : ''}{edu.end_year || ''}</p>
                          )}
                        </div>
                      ))}
                    </div>
                  </div>
                )}

                {certifications.length > 0 && (
                  <div className="card card-p space-y-4">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Certifications</p>
                    <ul className="space-y-2">
                      {certifications.map((cert, i) => (
                        <li key={i} className="flex items-start gap-2 text-sm">
                          <span className="text-brand mt-0.5">✓</span>
                          <span>
                            <span className="font-medium">{cert.name}</span>
                            {cert.issuer && <span className="text-text-2"> — {cert.issuer}</span>}
                            {cert.year && <span className="text-text-3"> ({cert.year})</span>}
                          </span>
                        </li>
                      ))}
                    </ul>
                  </div>
                )}

                {cvLanguages && (
                  <div className="card card-p space-y-3">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Languages</p>
                    <p className="text-sm">{cvLanguages}</p>
                  </div>
                )}
              </>
            )}

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
