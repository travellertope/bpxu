import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';

const upcomingSessions = [
  { mentor: 'Sarah Jenkins', role: 'Senior Product Manager', when: 'Tomorrow, 14:00 – 15:00 GMT', initial: 'S', color: '#6366f1' },
];
const pastSessions = [
  { mentor: 'Marcus Adebayo', role: 'VP of Engineering',      date: '10 Oct 2026', initial: 'M', color: '#8b5cf6' },
  { mentor: 'Sarah Jenkins',  role: 'Senior Product Manager', date: '28 Sep 2026', initial: 'S', color: '#6366f1' },
];
const aiMatches = [
  { name: 'Chloe Okafor', role: 'Marketing Director', match: '98%', color: '#ec4899' },
  { name: 'David Smith',  role: 'Data Scientist',      match: '94%', color: '#3b82f6' },
];

export default async function PairedDashboard() {
  const session = await getBPUSession();

  if (!session.authenticated || !session.user) {
    redirect('/login?returnTo=/paired/dashboard');
  }

  const user = session.user!;

  return (
    <div className="max-w-6xl mx-auto px-4 sm:px-6 py-10 space-y-8 fade-up">

      {/* ── Page header ─────────────────────────────────── */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 className="text-2xl font-bold">My sessions</h1>
          <p className="text-sm text-text-2 mt-1">Welcome back, {user.display_name}</p>
        </div>
        <a href="/paired/mentors" className="btn btn-purple btn-sm shrink-0">
          Find a mentor
        </a>
      </div>

      {/* ── Stats ───────────────────────────────────────── */}
      <div className="grid grid-cols-3 gap-4">
        {[
          { val: 1,   label: 'Upcoming'      },
          { val: 3,   label: 'Past sessions' },
          { val: 4.5, label: 'Hours mentored' },
        ].map(s => (
          <div key={s.label} className="card card-p text-center">
            <div className="stat-val">{s.val}</div>
            <div className="stat-label">{s.label}</div>
          </div>
        ))}
      </div>

      {/* ── Main grid ───────────────────────────────────── */}
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {/* Left: sessions */}
        <div className="lg:col-span-2 space-y-6">

          {/* Upcoming */}
          <div className="card card-p space-y-4">
            <p className="section-title">Upcoming sessions</p>
            {upcomingSessions.map(s => (
              <div key={s.mentor} className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-lg" style={{ background: 'var(--purple-bg)' }}>
                <div className="flex items-center gap-3">
                  <div className="avatar avatar-md" style={{ background: s.color }}>{s.initial}</div>
                  <div>
                    <p className="font-bold text-sm">{s.mentor}</p>
                    <p className="text-xs text-text-2">{s.role}</p>
                    <p className="text-xs text-text-3 mt-0.5">{s.when}</p>
                  </div>
                </div>
                <div className="flex gap-2 shrink-0">
                  <button className="btn btn-outline btn-sm">Reschedule</button>
                  <button className="btn btn-purple btn-sm">Join call</button>
                </div>
              </div>
            ))}
          </div>

          {/* Past */}
          <div className="card card-p space-y-4">
            <p className="section-title">Past sessions</p>
            <div className="space-y-2">
              {pastSessions.map(s => (
                <div key={s.date} className="flex items-center justify-between p-3 rounded-lg hover:bg-bg transition-colors">
                  <div className="flex items-center gap-3">
                    <div className="avatar avatar-sm" style={{ background: s.color }}>{s.initial}</div>
                    <div>
                      <p className="text-sm font-semibold">{s.mentor}</p>
                      <p className="text-xs text-text-3">{s.date}</p>
                    </div>
                  </div>
                  <button className="btn btn-ghost btn-sm text-xs">Book again</button>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Right: AI matches */}
        <div>
          <div className="card card-p space-y-4" style={{ background: '#1e1b4b', borderColor: '#312e81', color: '#fff' }}>
            <div className="space-y-1">
              <p className="font-bold">✨ AI matches</p>
              <p className="text-xs" style={{ color: '#c4b5fd' }}>
                Based on your BPU profile, our engine recommends these mentors.
              </p>
            </div>
            <div className="space-y-3">
              {aiMatches.map(m => (
                <a
                  key={m.name}
                  href={`/paired/mentors/1`}
                  className="flex items-center justify-between p-3 rounded-lg transition-colors"
                  style={{ background: 'rgba(255,255,255,0.08)' }}
                  onMouseEnter={e => (e.currentTarget.style.background = 'rgba(255,255,255,0.14)')}
                  onMouseLeave={e => (e.currentTarget.style.background = 'rgba(255,255,255,0.08)')}
                >
                  <div className="flex items-center gap-3">
                    <div className="avatar avatar-sm" style={{ background: m.color }}>{m.name[0]}</div>
                    <div>
                      <p className="text-sm font-bold text-white">{m.name}</p>
                      <p className="text-xs" style={{ color: '#c4b5fd' }}>{m.role}</p>
                    </div>
                  </div>
                  <span className="badge badge-green text-xs">{m.match}</span>
                </a>
              ))}
            </div>
            <a href="/paired/mentors" className="btn btn-sm w-full justify-center" style={{ background: 'rgba(255,255,255,0.12)', color: '#e9d5ff', border: '1px solid rgba(255,255,255,0.15)' }}>
              Browse all mentors
            </a>
          </div>
        </div>

      </div>
    </div>
  );
}
