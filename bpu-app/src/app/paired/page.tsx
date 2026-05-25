import React from 'react';

const mockMentors = [
  { id: 1, name: 'Sarah Jenkins', title: 'Senior Product Manager', industry: 'Technology', experience: '8 years', skills: ['Product Strategy', 'Agile', 'UX'], initial: 'S', color: 'bg-indigo-500' },
  { id: 2, name: 'Marcus Adebayo', title: 'VP of Engineering', industry: 'Fintech', experience: '12 years', skills: ['System Design', 'Leadership', 'Go'], initial: 'M', color: 'bg-purple-500' },
  { id: 3, name: 'Chloe Okafor', title: 'Marketing Director', industry: 'E-commerce', experience: '10 years', skills: ['Growth Marketing', 'SEO', 'Brand'], initial: 'C', color: 'bg-pink-500' },
  { id: 4, name: 'David Smith', title: 'Data Scientist', industry: 'Healthcare', experience: '5 years', skills: ['Machine Learning', 'Python', 'SQL'], initial: 'D', color: 'bg-blue-500' },
  { id: 5, name: 'Amira Hassan', title: 'UX Researcher', industry: 'Consulting', experience: '7 years', skills: ['User Interviews', 'Prototyping'], initial: 'A', color: 'bg-teal-500' },
  { id: 6, name: 'Elias Ndiaye', title: 'Financial Analyst', industry: 'Banking', experience: '6 years', skills: ['Financial Modeling', 'Excel', 'Risk'], initial: 'E', color: 'bg-indigo-600' }
];

export default function PairedDirectory() {
  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      
      {/* Hero Section */}
      <div className="text-center mb-16 space-y-6 animate-fadeInUp">
        <h1 className="text-5xl font-extrabold tracking-tight text-indigo-900 sm:text-6xl">
          Find Your Perfect <span className="paired-gradient-text">Mentor</span>
        </h1>
        <p className="mt-4 text-lg text-indigo-700/80 max-w-2xl mx-auto leading-relaxed">
          Connect with experienced Black professionals across industries who are ready to guide you on your career journey.
        </p>
        
        {/* Search & Filter */}
        <div className="max-w-3xl mx-auto mt-8 flex flex-col sm:flex-row gap-4 justify-center">
          <input 
            type="text" 
            placeholder="Search by name, role, or skill..." 
            className="flex-1 px-5 py-3 rounded-xl border border-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-sm"
          />
          <select className="px-5 py-3 rounded-xl border border-indigo-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white shadow-sm text-indigo-900">
            <option value="">All Industries</option>
            <option value="Technology">Technology</option>
            <option value="Finance">Finance</option>
            <option value="Healthcare">Healthcare</option>
          </select>
          <button className="button-paired shadow-lg shadow-indigo-500/30 min-h-[48px]">
            Search
          </button>
        </div>
      </div>

      {/* Mentors Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        {mockMentors.map((mentor) => (
          <div key={mentor.id} className="paired-card p-6 flex flex-col items-center text-center space-y-5 bg-white relative overflow-hidden group">
            {/* Background decorative blob */}
            <div className={`absolute -top-10 -right-10 w-32 h-32 rounded-full ${mentor.color} opacity-5 group-hover:scale-150 transition-transform duration-500`}></div>
            
            <div className={`w-24 h-24 rounded-full flex items-center justify-center text-3xl font-bold text-white shadow-md ${mentor.color}`}>
              {mentor.initial}
            </div>
            
            <div className="space-y-1 w-full">
              <h3 className="text-xl font-bold text-indigo-900">{mentor.name}</h3>
              <p className="text-sm font-medium text-indigo-600">{mentor.title}</p>
              <p className="text-xs text-indigo-400">{mentor.industry} • {mentor.experience}</p>
            </div>

            <div className="flex flex-wrap gap-2 justify-center pt-2">
              {mentor.skills.map(skill => (
                <span key={skill} className="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-semibold border border-indigo-100">
                  {skill}
                </span>
              ))}
            </div>

            <a href={`/paired/mentors/${mentor.id}`} className="mt-4 w-full px-4 py-2.5 text-sm font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors border border-indigo-100">
              View Profile
            </a>
          </div>
        ))}
      </div>
      
    </div>
  );
}
