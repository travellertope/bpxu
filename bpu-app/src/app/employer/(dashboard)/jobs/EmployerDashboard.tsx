'use client';

import { useState } from 'react';
import Link from 'next/link';
import { Job } from '@/app/jobs/types';

interface Props { initialJobs: Job[]; companyName: string; }

export default function EmployerDashboard({ initialJobs, companyName }: Props) {
    const [jobs] = useState<Job[]>(initialJobs);

    const statusBadge = (s?: string) => {
        if (s === 'publish') return <span className="badge badge-green">Live</span>;
        return <span className="badge badge-amber">Pending review</span>;
    };

    return (
        <div className="fade-up space-y-6">
                <div className="flex items-center justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold">Your job listings</h1>
                        <p className="section-sub">{jobs.length} job{jobs.length !== 1 ? 's' : ''} posted</p>
                    </div>
                    <a href="/employer/jobs/new" className="btn btn-amber shrink-0">Post a job +</a>
                </div>

                {jobs.length === 0 ? (
                    <div className="card card-p text-center py-16 space-y-4">
                        <p className="text-4xl">📋</p>
                        <p className="font-semibold">No jobs posted yet</p>
                        <p className="text-sm text-text-2">Post your first job to start receiving applications from BPU members.</p>
                        <a href="/employer/jobs/new" className="btn btn-amber inline-flex mx-auto">Post a job →</a>
                    </div>
                ) : (
                    <div className="space-y-3">
                        {/* Stats summary */}
                        <div className="grid grid-cols-3 gap-4 mb-6">
                            {[
                                { label: 'Total impressions', value: jobs.reduce((a, j) => a + (j.impressions ?? 0), 0) },
                                { label: 'Total clicks', value: jobs.reduce((a, j) => a + (j.clicks ?? 0), 0) },
                                { label: 'Total applications', value: jobs.reduce((a, j) => a + (j.applications ?? 0), 0) },
                            ].map(s => (
                                <div key={s.label} className="card card-p text-center space-y-1">
                                    <p className="text-2xl font-bold">{s.value}</p>
                                    <p className="text-xs text-text-2">{s.label}</p>
                                </div>
                            ))}
                        </div>

                        {jobs.map(job => (
                            <div key={job.id} className="card card-p flex flex-col sm:flex-row sm:items-center gap-4">
                                <div className="flex-1 min-w-0 space-y-1">
                                    <div className="flex items-center gap-2 flex-wrap">
                                        <p className="font-semibold truncate">{job.title}</p>
                                        {statusBadge((job as Job & { post_status?: string }).post_status)}
                                        <span className={`badge ${job.job_type === 'inbound' ? 'badge-green' : 'badge-amber'}`}>
                                            {job.job_type === 'inbound' ? 'Inbound' : 'Outbound'}
                                        </span>
                                    </div>
                                    <p className="text-sm text-text-2">{job.company} · {job.location}</p>
                                </div>

                                <div className="flex items-center gap-6 text-center shrink-0">
                                    <div>
                                        <p className="font-bold text-sm">{job.impressions ?? 0}</p>
                                        <p className="text-xs text-text-3">Views</p>
                                    </div>
                                    <div>
                                        <p className="font-bold text-sm">{job.clicks ?? 0}</p>
                                        <p className="text-xs text-text-3">Clicks</p>
                                    </div>
                                    <div>
                                        <p className="font-bold text-sm">{job.applications ?? 0}</p>
                                        <p className="text-xs text-text-3">Applied</p>
                                    </div>
                                    <Link href={`/jobs/${job.id}`} className="btn btn-ghost btn-sm">View →</Link>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
        </div>
    );
}
