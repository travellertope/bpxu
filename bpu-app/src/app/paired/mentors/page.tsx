const mockMentors = [
  { id: 1, name: 'Sarah Jenkins',  title: 'Senior Product Manager', industry: 'Technology',  exp: '8 yrs',  skills: ['Product Strategy','Agile','UX'],          color: '#6366f1' },
  { id: 2, name: 'Marcus Adebayo', title: 'VP of Engineering',       industry: 'Fintech',     exp: '12 yrs', skills: ['System Design','Leadership','Go'],        color: '#8b5cf6' },
  { id: 3, name: 'Chloe Okafor',   title: 'Marketing Director',      industry: 'E-commerce',  exp: '10 yrs', skills: ['Growth','SEO','Brand'],                  color: '#ec4899' },
  { id: 4, name: 'David Smith',    title: 'Data Scientist',          industry: 'Healthcare',  exp: '5 yrs',  skills: ['ML','Python','SQL'],                     color: '#3b82f6' },
  { id: 5, name: 'Amira Hassan',   title: 'UX Researcher',           industry: 'Consulting',  exp: '7 yrs',  skills: ['User Interviews','Prototyping'],          color: '#14b8a6' },
  { id: 6, name: 'Elias Ndiaye',   title: 'Financial Analyst',       industry: 'Banking',     exp: '6 yrs',  skills: ['Financial Modelling','Excel','Risk'],     color: '#f59e0b' },
];

export default function MentorDirectory() {
  return (
    <div className="wrap py-12 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>

      {/* Header */}
      <div style={{ textAlign: 'center', display: 'flex', flexDirection: 'column', gap: '12px' }}>
        <h1 className="text-4xl font-extrabold tracking-tight">Browse mentors</h1>
        <p className="text-text-2" style={{ maxWidth: '480px', marginLeft: 'auto', marginRight: 'auto' }}>
          Connect with experienced Black professionals ready to guide your career.
        </p>
      </div>

      {/* Search bar */}
      <div className="flex flex-col sm:flex-row gap-3" style={{ maxWidth: '640px', marginLeft: 'auto', marginRight: 'auto', width: '100%' }}>
        <input
          type="text"
          placeholder="Search by name, role or skill…"
          className="field-input flex-1"
        />
        <select className="field-input sm:w-48">
          <option value="">All industries</option>
          <option>Technology</option>
          <option>Fintech</option>
          <option>Healthcare</option>
          <option>Consulting</option>
          <option>Banking</option>
          <option>E-commerce</option>
        </select>
        <button className="btn btn-purple">Search</button>
      </div>

      {/* Grid */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {mockMentors.map(m => (
          <div key={m.id} className="card card-p card-lift flex flex-col gap-4">
            <div className="flex items-center gap-4">
              <div
                className="avatar avatar-md text-white shrink-0"
                style={{ background: m.color }}
              >
                {m.name[0]}
              </div>
              <div className="min-w-0">
                <p className="font-bold truncate">{m.name}</p>
                <p className="text-sm text-text-2 truncate">{m.title}</p>
                <p className="text-xs text-text-3 mt-0.5">{m.industry} · {m.exp}</p>
              </div>
            </div>

            <div className="flex flex-wrap gap-1.5">
              {m.skills.map(s => (
                <span key={s} className="badge badge-purple text-xs">{s}</span>
              ))}
            </div>

            <a
              href={`/paired/mentors/${m.id}`}
              className="btn btn-outline btn-sm mt-auto"
            >
              View profile →
            </a>
          </div>
        ))}
      </div>
    </div>
  );
}
