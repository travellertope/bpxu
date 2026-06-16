'use client';

import { useState, useEffect, useCallback } from 'react';

interface Employer {
    id: number;
    name: string;
    logo_url: string;
    website: string;
    description: string;
    job_count: number;
}

export default function EmployersAdmin() {
    const [employers, setEmployers] = useState<Employer[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');

    const fetchEmployers = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const res = await fetch('/api/paired/admin/employers');
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load employers.');
            setEmployers(data.employers || []);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load employers.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchEmployers();
    }, [fetchEmployers]);

    function handleSearch(e: React.FormEvent) {
        e.preventDefault();
        setSearch(searchInput.trim());
    }

    const filtered = search
        ? employers.filter(em =>
            em.name.toLowerCase().includes(search.toLowerCase()) ||
            (em.website && em.website.toLowerCase().includes(search.toLowerCase()))
        )
        : employers;

    const totalJobs = employers.reduce((sum, em) => sum + (em.job_count || 0), 0);

    return (
        <div className="space-y-5">
            {/* Summary cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div className="card card-p text-center">
                    <p className="text-xs text-text-3 mb-1">Total Employers</p>
                    <p className="text-2xl font-bold">{employers.length}</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-xs text-text-3 mb-1">Total Jobs Posted</p>
                    <p className="text-2xl font-bold">{totalJobs}</p>
                </div>
            </div>

            {/* Search */}
            <form onSubmit={handleSearch} className="flex gap-2">
                <input
                    type="text"
                    className="field-input flex-1"
                    placeholder="Search by company name or website..."
                    value={searchInput}
                    onChange={e => setSearchInput(e.target.value)}
                />
                <button type="submit" className="btn btn-purple btn-sm">Search</button>
                {search && (
                    <button
                        type="button"
                        className="btn btn-ghost btn-sm"
                        onClick={() => { setSearch(''); setSearchInput(''); }}
                    >
                        Clear
                    </button>
                )}
            </form>

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading employers...</div>
            ) : error ? (
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : filtered.length === 0 ? (
                <div className="card card-p text-center py-10">
                    <p className="font-semibold text-text-2">No employers found</p>
                    <p className="text-sm text-text-3 mt-1">
                        {search ? 'Try adjusting your search.' : 'No employer accounts exist yet.'}
                    </p>
                </div>
            ) : (
                <>
                    <p className="text-sm text-text-3">{filtered.length} employer{filtered.length !== 1 ? 's' : ''}</p>

                    {/* Desktop table */}
                    <div className="card hidden md:block" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Company</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Website</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">About</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Jobs</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.map(em => (
                                    <tr key={em.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3">
                                            <div className="flex items-center gap-3">
                                                {em.logo_url ? (
                                                    <img
                                                        src={em.logo_url}
                                                        alt={em.name}
                                                        style={{ width: 36, height: 36, objectFit: 'contain', borderRadius: 6, background: 'var(--surface)', border: '1px solid var(--border)' }}
                                                    />
                                                ) : (
                                                    <div style={{ width: 36, height: 36, borderRadius: 6, background: 'var(--surface)', border: '1px solid var(--border)', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                                        <span className="text-xs text-text-3 font-bold">{em.name.charAt(0).toUpperCase()}</span>
                                                    </div>
                                                )}
                                                <span className="text-sm font-semibold">{em.name}</span>
                                            </div>
                                        </td>
                                        <td className="p-3">
                                            {em.website ? (
                                                <a
                                                    href={em.website}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="text-sm hover:underline"
                                                    style={{ color: 'var(--link)' }}
                                                >
                                                    {em.website.replace(/^https?:\/\//, '')}
                                                </a>
                                            ) : (
                                                <span className="text-sm text-text-3">—</span>
                                            )}
                                        </td>
                                        <td className="p-3">
                                            <span className="text-sm text-text-2 line-clamp-2" style={{ maxWidth: 300 }}>
                                                {em.description || '—'}
                                            </span>
                                        </td>
                                        <td className="p-3 text-center">
                                            <a
                                                href={`/admin/jobs?employer=${em.id}`}
                                                className="text-sm font-semibold hover:underline"
                                                style={{ color: 'var(--link)' }}
                                            >
                                                {em.job_count ?? 0}
                                            </a>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile cards */}
                    <div className="md:hidden space-y-3">
                        {filtered.map(em => (
                            <div key={em.id} className="card card-p space-y-3">
                                <div className="flex items-center gap-3">
                                    {em.logo_url ? (
                                        <img
                                            src={em.logo_url}
                                            alt={em.name}
                                            style={{ width: 40, height: 40, objectFit: 'contain', borderRadius: 8, background: 'var(--surface)', border: '1px solid var(--border)' }}
                                        />
                                    ) : (
                                        <div style={{ width: 40, height: 40, borderRadius: 8, background: 'var(--surface)', border: '1px solid var(--border)', display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                                            <span className="text-sm text-text-3 font-bold">{em.name.charAt(0).toUpperCase()}</span>
                                        </div>
                                    )}
                                    <div>
                                        <p className="text-sm font-semibold">{em.name}</p>
                                        {em.website && (
                                            <a
                                                href={em.website}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="text-xs hover:underline"
                                                style={{ color: 'var(--link)' }}
                                            >
                                                {em.website.replace(/^https?:\/\//, '')}
                                            </a>
                                        )}
                                    </div>
                                </div>
                                {em.description && (
                                    <p className="text-xs text-text-2">{em.description}</p>
                                )}
                                <div className="flex items-center justify-between">
                                    <span className="text-xs text-text-3">Jobs posted</span>
                                    <a
                                        href={`/admin/jobs?employer=${em.id}`}
                                        className="text-sm font-semibold hover:underline"
                                        style={{ color: 'var(--link)' }}
                                    >
                                        {em.job_count ?? 0}
                                    </a>
                                </div>
                            </div>
                        ))}
                    </div>
                </>
            )}
        </div>
    );
}
