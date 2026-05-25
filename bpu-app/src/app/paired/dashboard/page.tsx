import React from 'react';
import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';

export default async function PairedDashboard() {
  const session = await getBPUSession();
  
  if (!session.authenticated || !session.user) {
    const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';
    const APP_URL = process.env.NEXT_PUBLIC_APP_URL || 'https://app.blackprofessionals.uk';
    const loginUrl = `${WP_BACKEND_URL}/?bpu_sso_handoff=1&redirect_to=${encodeURIComponent(`${APP_URL}/api/auth/callback?from=paired`)}`;
    redirect(loginUrl);
  }

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10 animate-fadeInUp">
      
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
          <h1 className="text-3xl font-bold text-indigo-900">My Mentorship Dashboard</h1>
          <p className="text-sm text-indigo-600 mt-1">Welcome back, {session.user.display_name}. Track your sessions and find your next mentor.</p>
        </div>
        <a href="/paired" className="button-paired shadow-md">
          Find a Mentor
        </a>
      </div>

      {/* Stats Row */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div className="paired-card p-6 bg-white">
          <div className="text-sm font-semibold text-indigo-400 uppercase tracking-wide">Upcoming Sessions</div>
          <div className="text-4xl font-extrabold text-indigo-900 mt-2">1</div>
        </div>
        <div className="paired-card p-6 bg-white">
          <div className="text-sm font-semibold text-indigo-400 uppercase tracking-wide">Past Sessions</div>
          <div className="text-4xl font-extrabold text-indigo-900 mt-2">3</div>
        </div>
        <div className="paired-card p-6 bg-white">
          <div className="text-sm font-semibold text-indigo-400 uppercase tracking-wide">Hours Mentored</div>
          <div className="text-4xl font-extrabold text-indigo-900 mt-2">4.5</div>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        {/* Left Col: Sessions */}
        <div className="lg:col-span-2 space-y-8">
          
          <section className="paired-card p-8 bg-white space-y-6">
            <h2 className="text-xl font-bold text-indigo-900">Upcoming Sessions</h2>
            
            <div className="border border-indigo-100 rounded-xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-5 bg-indigo-50/50">
              <div className="flex items-center gap-4">
                <div className="w-14 h-14 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold text-xl shrink-0 shadow-sm">
                  S
                </div>
                <div>
                  <h3 className="font-bold text-indigo-900">Sarah Jenkins</h3>
                  <p className="text-xs font-medium text-indigo-600">Senior Product Manager</p>
                  <p className="text-xs text-indigo-400 mt-1">Tomorrow, 14:00 - 15:00 GMT</p>
                </div>
              </div>
              <div className="flex flex-col sm:flex-row gap-3">
                <button className="px-4 py-2 text-xs font-bold text-indigo-600 bg-white border border-indigo-200 rounded-lg hover:bg-indigo-50 transition">Reschedule</button>
                <button className="px-4 py-2 text-xs font-bold text-white bg-indigo-600 rounded-lg shadow hover:bg-indigo-700 transition">Join Call</button>
              </div>
            </div>
          </section>

          <section className="paired-card p-8 bg-white space-y-6">
            <h2 className="text-xl font-bold text-indigo-900">Past Sessions</h2>
            
            <div className="space-y-4">
              {[
                { name: 'Marcus Adebayo', title: 'VP of Engineering', date: 'Oct 10, 2026', initial: 'M', color: 'bg-purple-500' },
                { name: 'Sarah Jenkins', title: 'Senior Product Manager', date: 'Sep 28, 2026', initial: 'S', color: 'bg-indigo-500' }
              ].map((past, i) => (
                <div key={i} className="border border-indigo-100 rounded-xl p-4 flex items-center justify-between hover:bg-indigo-50/50 transition">
                  <div className="flex items-center gap-4">
                    <div className={`w-10 h-10 rounded-full ${past.color} flex items-center justify-center text-white font-bold text-sm shrink-0`}>
                      {past.initial}
                    </div>
                    <div>
                      <h3 className="font-bold text-sm text-indigo-900">{past.name}</h3>
                      <p className="text-xs text-indigo-500">{past.date}</p>
                    </div>
                  </div>
                  <button className="text-xs font-semibold text-indigo-600 hover:underline">Book Again</button>
                </div>
              ))}
            </div>
          </section>

        </div>

        {/* Right Col: AI Matches */}
        <div className="space-y-6">
          <section className="paired-card p-6 bg-gradient-to-b from-indigo-900 to-indigo-950 text-white border-none shadow-xl">
            <div className="flex items-center gap-2 mb-4">
              <span className="text-lg">✨</span>
              <h3 className="font-bold text-white">AI Mentor Matches</h3>
            </div>
            <p className="text-xs text-indigo-200 mb-6 leading-relaxed">
              Based on your BPU profile, our semantic engine recommends these mentors for you.
            </p>
            
            <div className="space-y-4">
              {[
                { name: 'Chloe Okafor', title: 'Marketing Director', match: '98%', initial: 'C', color: 'bg-pink-500' },
                { name: 'David Smith', title: 'Data Scientist', match: '94%', initial: 'D', color: 'bg-blue-500' }
              ].map((match, i) => (
                <a href="#" key={i} className="block p-4 rounded-xl bg-white/10 hover:bg-white/20 transition border border-white/10 group">
                  <div className="flex justify-between items-start">
                    <div className="flex items-center gap-3">
                      <div className={`w-10 h-10 rounded-full ${match.color} flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-md`}>
                        {match.initial}
                      </div>
                      <div>
                        <h4 className="font-bold text-sm text-white group-hover:text-indigo-100">{match.name}</h4>
                        <p className="text-xs text-indigo-300">{match.title}</p>
                      </div>
                    </div>
                    <span className="text-xs font-bold text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded-md">{match.match}</span>
                  </div>
                </a>
              ))}
            </div>
          </section>
        </div>

      </div>
    </div>
  );
}
