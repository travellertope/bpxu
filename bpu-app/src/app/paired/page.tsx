import { getBPUSession } from '@/lib/auth';

const features = [
  {
    icon: '🤝',
    title: 'AI-powered matching',
    desc: 'Our semantic engine analyses your BPU profile to surface mentors whose experience genuinely aligns with where you want to go.',
  },
  {
    icon: '📅',
    title: 'Easy scheduling',
    desc: 'Book free 1-on-1 video sessions directly from a mentor\'s profile. No back-and-forth emails.',
  },
  {
    icon: '🌍',
    title: 'UK-wide community',
    desc: 'Every mentor is a verified Black professional in the UK — people who have navigated the same spaces you are entering.',
  },
  {
    icon: '🎯',
    title: 'Career-specific guidance',
    desc: 'Filter by industry, role, and skills so every conversation moves your career forward.',
  },
];

const steps = [
  { num: '01', title: 'Create your BPU account', desc: 'Free to join. Your BPU profile powers the matching.' },
  { num: '02', title: 'Browse or get matched',   desc: 'Search the directory or let the AI suggest your top 3 mentors.' },
  { num: '03', title: 'Book a free session',     desc: 'Pick a time that works and get a calendar invite instantly.' },
  { num: '04', title: 'Grow your career',        desc: 'Walk away with actionable advice from someone who has been there.' },
];

export default async function PairedHome() {
  const session = await getBPUSession();

  return (
    <div>

      {/* ── Hero ─────────────────────────────────────────── */}
      <section className="py-20 px-6 text-center" style={{ background: 'linear-gradient(160deg, #f5f3ff 0%, #fafafa 60%)' }}>
        <div className="max-w-3xl mx-auto fade-up space-y-6">
          <span className="badge badge-purple text-sm">Now live · Free to join</span>
          <h1 className="text-5xl sm:text-6xl font-extrabold tracking-tight leading-tight" style={{ color: '#1e1b4b' }}>
            Find your perfect<br />
            <span style={{ color: 'var(--purple)' }}>mentor</span>
          </h1>
          <p className="text-lg text-text-2 max-w-xl mx-auto leading-relaxed">
            PAIRED connects ambitious Black professionals across the UK with experienced mentors who truly understand the journey.
          </p>
          <div className="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
            {session.authenticated ? (
              <a href="/paired/dashboard" className="btn btn-purple btn-lg">
                Go to my dashboard →
              </a>
            ) : (
              <>
                <a href="/register?returnTo=/paired/dashboard" className="btn btn-purple btn-lg">
                  Get started — it&apos;s free
                </a>
                <a href="/paired/mentors" className="btn btn-outline btn-lg">
                  Browse mentors
                </a>
              </>
            )}
          </div>
        </div>
      </section>

      {/* ── Features ─────────────────────────────────────── */}
      <section className="py-16 px-6">
        <div className="max-w-5xl mx-auto">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold">Why PAIRED?</h2>
            <p className="text-text-2 mt-2">Mentorship that actually fits your world.</p>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
            {features.map(f => (
              <div key={f.title} className="card card-p space-y-3 card-lift">
                <div className="text-3xl">{f.icon}</div>
                <p className="font-bold text-base">{f.title}</p>
                <p className="text-sm text-text-2 leading-relaxed">{f.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── How it works ─────────────────────────────────── */}
      <section className="py-16 px-6 bg-surface border-y border-border">
        <div className="max-w-4xl mx-auto">
          <div className="text-center mb-12">
            <h2 className="text-3xl font-bold">How it works</h2>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {steps.map(s => (
              <div key={s.num} className="text-center space-y-3">
                <div className="inline-flex items-center justify-center w-12 h-12 rounded-full font-extrabold text-lg" style={{ background: 'var(--purple-bg)', color: 'var(--purple)' }}>
                  {s.num}
                </div>
                <p className="font-bold">{s.title}</p>
                <p className="text-sm text-text-2 leading-relaxed">{s.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── CTA ──────────────────────────────────────────── */}
      <section className="py-20 px-6 text-center">
        <div className="max-w-xl mx-auto space-y-6">
          <h2 className="text-3xl font-bold">Ready to find your mentor?</h2>
          <p className="text-text-2">Join hundreds of Black professionals already growing with PAIRED.</p>
          <div className="flex flex-col sm:flex-row gap-3 justify-center">
            {session.authenticated ? (
              <a href="/paired/dashboard" className="btn btn-purple btn-lg">
                Open my dashboard →
              </a>
            ) : (
              <>
                <a href="/login?returnTo=/paired/dashboard" className="btn btn-purple btn-lg">
                  Sign in to PAIRED
                </a>
                <a href="/register?returnTo=/paired/dashboard" className="btn btn-outline btn-lg">
                  Create free account
                </a>
              </>
            )}
          </div>
        </div>
      </section>

    </div>
  );
}
