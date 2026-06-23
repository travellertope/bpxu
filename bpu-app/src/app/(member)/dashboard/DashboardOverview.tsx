'use client';

import React from 'react';
import { BPUUser, ACFProfile } from '@/lib/auth';
import { JobListing, CourseItem, CVReview, EventItem } from '@/lib/api';

interface Props {
  user: BPUUser;
  jobs: JobListing[];
  courses: CourseItem[];
  reviews: CVReview[];
  events: EventItem[];
}

const TAB_HREFS: Record<string, string> = {
  courses: '/courses',
  events: '/events',
  cv: '/cv-clinic',
  jobs: '/job-matches',
  profile: '/profile',
};

export default function DashboardOverview({ user, jobs, courses, reviews, events }: Props) {
  const isPro = user.is_pro;
  const profile: ACFProfile = user.profile;
  const cvUrl = user.cv_url || '';
  const firstName = profile.first_name || user.display_name.split(' ')[0];

  const inProgress = courses.filter(c => c.status === 'In Progress').length;
  const completed  = courses.filter(c => c.status === 'Completed').length;
  const upcomingEvents = events.filter(ev => ev.start_date && new Date(ev.start_date) >= new Date()).length;

  const stats = [
    {
      val:   courses.length,
      label: courses.length === 0 ? 'Enrolled courses' : inProgress > 0 ? `${inProgress} in progress` : completed > 0 ? `${completed} completed` : 'Enrolled',
      sub:   'Courses',
      href:  '/courses',
    },
    {
      val:   upcomingEvents,
      label: upcomingEvents === 1 ? 'Upcoming event' : 'Upcoming events',
      sub:   'Events',
      href:  '/events',
    },
    {
      val:   reviews.length,
      label: reviews.length === 1 ? 'Review received' : 'Reviews received',
      sub:   'CV Clinic',
      href:  '/cv-clinic',
    },
  ];

  return (
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
          <a href="/profile" className="btn btn-outline btn-sm">Edit profile</a>
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
        {stats.map(s => (
          <a
            key={s.sub}
            href={s.href}
            className="card card-p text-center hover:border-brand transition-colors cursor-pointer"
            style={{ background: 'none', width: '100%', textDecoration: 'none', color: 'inherit', display: 'block' }}
          >
            <div className="stat-val">{s.val}</div>
            <div className="stat-label">{s.label}</div>
            <div className="text-xs text-text-3 mt-1">{s.sub}</div>
          </a>
        ))}
      </div>

      {/* Quick previews */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Top jobs */}
        <div className="card card-p space-y-4">
          <div className="flex items-center justify-between">
            <p className="section-title">Top job matches</p>
            <a href="/job-matches" className="text-xs text-brand-dark font-semibold hover:underline">View all</a>
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
                  <a href="/cv-clinic" className="underline font-semibold">Replace</a>
                </div>
              </div>
            : <div className="alert alert-amber text-sm">No CV uploaded yet. Upload a PDF to auto-fill your profile.</div>
          }
          <a href="/cv-clinic" className="btn btn-outline btn-sm">
            Go to CV Clinic
          </a>
          {reviews.length > 0 && (
            <p className="text-sm text-text-2">You have <span className="font-bold text-text">{reviews.length}</span> professional review{reviews.length !== 1 ? 's' : ''}.</p>
          )}
        </div>
      </div>
    </div>
  );
}
