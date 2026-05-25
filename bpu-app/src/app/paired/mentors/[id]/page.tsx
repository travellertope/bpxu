const mockMentor = {
  name: 'Sarah Jenkins',
  title: 'Senior Product Manager',
  industry: 'Technology',
  exp: '8 years',
  skills: ['Product Strategy', 'Agile', 'UX Research', 'Roadmapping'],
  bio: 'I am a passionate Product Manager with nearly a decade of experience building consumer tech products. I love helping aspiring PMs break into the industry, navigate corporate politics, and build user-centric products. My mentorship style is direct, actionable, and supportive.',
  expertise: 'Consumer Mobile Apps · Fintech · B2B SaaS',
  color: '#6366f1',
};

const slots = [
  { id: 1, date: 'Mon 27 Oct', time: '14:00 – 15:00 GMT' },
  { id: 2, date: 'Wed 29 Oct', time: '10:00 – 11:00 GMT' },
  { id: 3, date: 'Fri 31 Oct', time: '16:00 – 17:00 GMT' },
];

export default async function MentorProfile({
  params,
}: {
  params: Promise<{ id: string }>;
}) {
  await params;

  return (
    <div className="wrap py-12 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>

      {/* ── Profile header ──────────────────────────────── */}
      <div className="card card-p-lg">
        <div className="flex flex-col md:flex-row gap-8 items-center md:items-start">
          {/* Avatar */}
          <div
            className="avatar avatar-xl text-white shrink-0"
            style={{ background: mockMentor.color }}
          >
            {mockMentor.name[0]}
          </div>

          {/* Info */}
          <div className="flex-1 text-center md:text-left space-y-4">
            <div>
              <h1 className="text-3xl font-extrabold">{mockMentor.name}</h1>
              <p className="text-lg text-text-2 mt-1">{mockMentor.title}</p>
              <p className="text-sm text-text-3 mt-1">{mockMentor.industry} · {mockMentor.exp} experience</p>
            </div>
            <div className="flex flex-wrap gap-2 justify-center md:justify-start">
              {mockMentor.skills.map(s => (
                <span key={s} className="badge badge-purple">{s}</span>
              ))}
            </div>
          </div>

          {/* CTA */}
          <div className="shrink-0 w-full md:w-auto text-center space-y-2">
            <button className="btn btn-purple btn-lg w-full md:w-auto">
              Book free session
            </button>
            <p className="text-xs text-text-3">Responds within 24 h</p>
          </div>
        </div>
      </div>

      {/* ── Body ────────────────────────────────────────── */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6">

        {/* Left: details */}
        <div className="md:col-span-2 space-y-6">
          <div className="card card-p space-y-3">
            <p className="section-title">About</p>
            <p className="text-sm text-text-2 leading-relaxed">{mockMentor.bio}</p>
          </div>
          <div className="card card-p space-y-3">
            <p className="section-title">Areas of expertise</p>
            <p className="text-sm text-text-2">{mockMentor.expertise}</p>
          </div>
        </div>

        {/* Right: booking */}
        <div>
          <div className="card card-p sticky top-20 space-y-4">
            <p className="section-title">Available slots</p>
            <div className="space-y-2">
              {slots.map(s => (
                <button
                  key={s.id}
                  className="w-full text-left card card-p border-border hover:border-purple transition-colors"
                  style={{ padding: '12px 14px' }}
                >
                  <p className="text-sm font-semibold">{s.date}</p>
                  <p className="text-xs text-text-2 mt-0.5">{s.time}</p>
                </button>
              ))}
            </div>
            <button className="btn btn-outline btn-sm w-full">
              View more times
            </button>
          </div>
        </div>

      </div>
    </div>
  );
}
