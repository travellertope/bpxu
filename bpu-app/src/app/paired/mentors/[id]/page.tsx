import React from 'react';

export default function MentorProfile({ params }: { params: { id: string } }) {
  // Mock data for specific mentor
  const mentor = {
    id: params.id,
    name: 'Sarah Jenkins',
    title: 'Senior Product Manager',
    industry: 'Technology',
    experience: '8 years',
    skills: ['Product Strategy', 'Agile', 'UX Research', 'Roadmapping'],
    bio: 'I am a passionate Product Manager with nearly a decade of experience building consumer tech products. I love helping aspiring PMs break into the industry, navigate corporate politics, and build user-centric products. My mentorship style is direct, actionable, and supportive.',
    expertise: 'Consumer Mobile Apps, Fintech, B2B SaaS',
    initial: 'S',
    color: 'bg-indigo-500'
  };

  const availableSlots = [
    { id: 1, date: 'Oct 24, 2026', time: '14:00 - 15:00' },
    { id: 2, date: 'Oct 26, 2026', time: '10:00 - 11:00' },
    { id: 3, date: 'Oct 28, 2026', time: '16:00 - 17:00' }
  ];

  return (
    <div className="max-w-4xl mx-auto px-4 sm:px-6 py-12 space-y-10">
      
      {/* Profile Header Card */}
      <div className="paired-card p-8 md:p-12 relative overflow-hidden bg-white">
        <div className={`absolute top-0 left-0 w-full h-32 ${mentor.color} opacity-10`}></div>
        
        <div className="relative z-10 flex flex-col md:flex-row gap-8 items-center md:items-start text-center md:text-left">
          <div className={`w-32 h-32 rounded-full flex items-center justify-center text-5xl font-bold text-white shadow-xl border-4 border-white shrink-0 ${mentor.color}`}>
            {mentor.initial}
          </div>
          
          <div className="space-y-3 flex-1">
            <div>
              <h1 className="text-3xl font-extrabold text-indigo-900">{mentor.name}</h1>
              <p className="text-lg font-medium text-indigo-600 mt-1">{mentor.title}</p>
              <div className="flex items-center justify-center md:justify-start gap-4 text-sm text-indigo-500 mt-2 font-medium">
                <span>🏢 {mentor.industry}</span>
                <span>⏳ {mentor.experience} Exp</span>
              </div>
            </div>
            
            <div className="flex flex-wrap gap-2 justify-center md:justify-start pt-3">
              {mentor.skills.map(skill => (
                <span key={skill} className="px-3 py-1.5 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-100">
                  {skill}
                </span>
              ))}
            </div>
          </div>
          
          <div className="shrink-0 w-full md:w-auto">
             <button className="w-full button-paired min-h-[48px] shadow-lg shadow-indigo-500/20 text-lg px-8">
               Book Free Session
             </button>
             <p className="text-xs text-center text-indigo-400 mt-3">Usually responds in 24h</p>
          </div>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        {/* Left Col: Details */}
        <div className="md:col-span-2 space-y-8">
          <div className="paired-card p-8 bg-white">
            <h2 className="text-xl font-bold text-indigo-900 mb-4">About Me</h2>
            <p className="text-indigo-800/80 leading-relaxed text-sm">
              {mentor.bio}
            </p>
          </div>
          
          <div className="paired-card p-8 bg-white">
            <h2 className="text-xl font-bold text-indigo-900 mb-4">Fields of Expertise</h2>
            <p className="text-indigo-800/80 leading-relaxed text-sm font-medium">
              {mentor.expertise}
            </p>
          </div>
        </div>

        {/* Right Col: Booking Slots */}
        <div className="space-y-6">
          <div className="paired-card p-6 bg-white sticky top-24">
            <h3 className="font-bold text-indigo-900 mb-4">Available Sessions</h3>
            <div className="space-y-3">
              {availableSlots.map(slot => (
                <button key={slot.id} className="w-full text-left p-4 rounded-xl border border-indigo-100 hover:border-indigo-500 hover:bg-indigo-50 transition-all group">
                  <div className="font-semibold text-sm text-indigo-900 group-hover:text-indigo-700">{slot.date}</div>
                  <div className="text-xs text-indigo-500 mt-1">{slot.time} GMT</div>
                </button>
              ))}
            </div>
            <button className="w-full mt-4 py-2.5 text-sm font-bold text-indigo-600 bg-white border-2 border-indigo-100 rounded-lg hover:bg-indigo-50 transition">
              View More Times
            </button>
          </div>
        </div>

      </div>
    </div>
  );
}
