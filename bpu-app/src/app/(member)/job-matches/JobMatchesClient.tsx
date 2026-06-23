'use client';

import { JobListing } from '@/lib/api';

function ProGate({ children, isPro, feature }: { children: React.ReactNode; isPro: boolean; feature: string }) {
    if (isPro) return <>{children}</>;
    return (
        <div style={{ position: 'relative', minHeight: '220px' }}>
            <div style={{ filter: 'blur(4px)', pointerEvents: 'none', userSelect: 'none', opacity: 0.5 }} aria-hidden="true">
                {children}
            </div>
            <div
                className="absolute inset-0 flex flex-col items-center justify-center gap-3"
                style={{ background: 'rgba(0,0,0,0.75)', borderRadius: 'var(--radius)', padding: '2rem', color: '#ffffff' }}
            >
                <p style={{ fontSize: '2rem', lineHeight: 1, color: '#f59e0b' }}>★</p>
                <p style={{ fontWeight: 700, fontSize: '1rem', textAlign: 'center', color: '#ffffff' }}>{feature}</p>
                <p style={{ fontSize: '0.875rem', textAlign: 'center', color: 'rgba(255,255,255,0.75)' }}>Requires BPU Pro membership</p>
                <a href="/upgrade" className="btn btn-amber btn-sm">Upgrade to Pro →</a>
            </div>
        </div>
    );
}

interface JobMatchesClientProps {
    jobs: JobListing[];
    isPro: boolean;
}

export default function JobMatchesClient({ jobs, isPro }: JobMatchesClientProps) {
    return (
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
                                            href={`/go/${j.id}`}
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
    );
}
