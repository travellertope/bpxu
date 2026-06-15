'use client';

import { useState, useEffect, useCallback } from 'react';
import { decodeHtml } from '@/lib/utils';

interface Mentor {
    id: number;
    display_name: string;
    email: string;
    avatar_url?: string;
    industry?: string;
    booking_count?: number;
    average_rating?: number;
    status: string; // 'active' | 'inactive'
}

interface EditData {
    display_name: string;
    industry: string;
    email: string;
}

const INDUSTRIES = [
    'Technology', 'Finance & Banking', 'Healthcare', 'Education', 'Legal',
    'Marketing & Advertising', 'Media & Entertainment', 'Engineering',
    'Consulting', 'Non-Profit', 'Government & Public Sector', 'Real Estate',
    'Retail & E-Commerce', 'Energy & Utilities', 'Construction',
    'Hospitality & Tourism', 'Transport & Logistics', 'Telecommunications',
    'Creative & Design', 'Other',
];

export default function MentorAdmin() {
    const [mentors, setMentors] = useState<Mentor[]>([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [search, setSearch] = useState('');
    const [page, setPage] = useState(1);
    const [editingMentor, setEditingMentor] = useState<Mentor | null>(null);
    const [editData, setEditData] = useState<EditData>({ display_name: '', industry: '', email: '' });
    const [editSaving, setEditSaving] = useState(false);
    const [editError, setEditError] = useState('');
    const [deactivatingId, setDeactivatingId] = useState<number | null>(null);
    const [confirmDeactivate, setConfirmDeactivate] = useState<Mentor | null>(null);
    const [actionLoading, setActionLoading] = useState(false);
    const perPage = 20;

    const fetchMentors = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({
                page: String(page),
                per_page: String(perPage),
            });
            if (search.trim()) params.set('search', search.trim());

            const res = await fetch(`/api/paired/admin/mentors?${params}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load mentors.');
            setMentors(data.mentors || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load mentors.');
        } finally {
            setLoading(false);
        }
    }, [page, search]);

    useEffect(() => {
        fetchMentors();
    }, [fetchMentors]);

    function handleSearch(e: React.FormEvent) {
        e.preventDefault();
        setPage(1);
        fetchMentors();
    }

    function openEdit(mentor: Mentor) {
        setEditingMentor(mentor);
        setEditData({
            display_name: mentor.display_name,
            industry: mentor.industry || '',
            email: mentor.email,
        });
        setEditError('');
    }

    async function saveEdit() {
        if (!editingMentor) return;
        setEditSaving(true);
        setEditError('');

        try {
            const res = await fetch(`/api/paired/admin/mentors/${editingMentor.id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(editData),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to update mentor.');
            setMentors(prev =>
                prev.map(m =>
                    m.id === editingMentor.id
                        ? { ...m, display_name: editData.display_name, industry: editData.industry, email: editData.email }
                        : m
                )
            );
            setEditingMentor(null);
        } catch (e) {
            setEditError(e instanceof Error ? e.message : 'Failed to save.');
        } finally {
            setEditSaving(false);
        }
    }

    async function toggleStatus(mentor: Mentor) {
        setActionLoading(true);
        setDeactivatingId(mentor.id);
        const newStatus = mentor.status === 'active' ? 'inactive' : 'active';

        try {
            const res = await fetch(`/api/paired/admin/mentors/${mentor.id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to update status.');
            setMentors(prev =>
                prev.map(m => m.id === mentor.id ? { ...m, status: newStatus } : m)
            );
            setConfirmDeactivate(null);
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Action failed.');
        } finally {
            setActionLoading(false);
            setDeactivatingId(null);
        }
    }

    const totalPages = Math.ceil(total / perPage);

    return (
        <div className="space-y-6">
            {/* Search */}
            <form onSubmit={handleSearch} className="flex gap-2">
                <input
                    type="text"
                    className="field-input flex-1"
                    placeholder="Search by name or email..."
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                />
                <button type="submit" className="btn btn-purple btn-sm">Search</button>
            </form>

            {/* Content */}
            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading mentors...</div>
            ) : error ? (
                <div className="card card-p text-center text-sm py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : mentors.length === 0 ? (
                <div className="card card-p text-center py-10">
                    <p className="text-text-2 font-semibold">No mentors found</p>
                    <p className="text-sm text-text-3 mt-1">Try adjusting your search.</p>
                </div>
            ) : (
                <>
                    <p className="text-sm text-text-3">{total} mentor{total !== 1 ? 's' : ''} found</p>

                    {/* Desktop Table */}
                    <div className="card hidden md:block" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Name</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Email</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Industry</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Bookings</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Rating</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Status</th>
                                    <th className="text-right text-xs font-semibold text-text-3 p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {mentors.map((m) => (
                                    <tr key={m.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3">
                                            <div className="flex items-center gap-2">
                                                {m.avatar_url ? (
                                                    <img src={m.avatar_url} alt="" className="rounded-full object-cover" style={{ width: 28, height: 28 }} />
                                                ) : (
                                                    <div className="avatar text-white" style={{ background: '#7c3aed', width: 28, height: 28, fontSize: '0.7rem' }}>
                                                        {decodeHtml(m.display_name)?.[0] || '?'}
                                                    </div>
                                                )}
                                                <span className="text-sm font-semibold">{decodeHtml(m.display_name)}</span>
                                            </div>
                                        </td>
                                        <td className="p-3 text-sm text-text-2">{m.email}</td>
                                        <td className="p-3 text-sm text-text-2">{m.industry || '-'}</td>
                                        <td className="p-3 text-sm text-text-2 text-center">{m.booking_count ?? 0}</td>
                                        <td className="p-3 text-sm text-text-2 text-center">
                                            {m.average_rating != null && m.average_rating > 0 ? (
                                                <span className="flex items-center justify-center gap-1">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" strokeWidth="2">
                                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                    </svg>
                                                    {m.average_rating.toFixed(1)}
                                                </span>
                                            ) : '-'}
                                        </td>
                                        <td className="p-3 text-center">
                                            <span className={`badge ${m.status === 'active' ? 'badge-green' : 'badge-amber'}`}>
                                                {m.status}
                                            </span>
                                        </td>
                                        <td className="p-3 text-right">
                                            <div className="flex items-center justify-end gap-1">
                                                <a href={`/paired/mentors/${m.id}`} className="btn btn-ghost btn-sm text-xs">View</a>
                                                <button onClick={() => openEdit(m)} className="btn btn-ghost btn-sm text-xs">Edit</button>
                                                <button
                                                    onClick={() => m.status === 'active' ? setConfirmDeactivate(m) : toggleStatus(m)}
                                                    disabled={deactivatingId === m.id}
                                                    className="btn btn-ghost btn-sm text-xs"
                                                    style={{ color: m.status === 'active' ? 'var(--err)' : 'var(--ok)' }}
                                                >
                                                    {deactivatingId === m.id ? '...' : m.status === 'active' ? 'Deactivate' : 'Activate'}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile Cards */}
                    <div className="md:hidden space-y-4">
                        {mentors.map((m) => (
                            <div key={m.id} className="card card-p">
                                <div className="flex items-start gap-3 mb-3">
                                    {m.avatar_url ? (
                                        <img src={m.avatar_url} alt="" className="rounded-full object-cover" style={{ width: 40, height: 40 }} />
                                    ) : (
                                        <div className="avatar text-white" style={{ background: '#7c3aed', width: 40, height: 40, fontSize: '0.875rem' }}>
                                            {decodeHtml(m.display_name)?.[0] || '?'}
                                        </div>
                                    )}
                                    <div className="flex-1 min-w-0">
                                        <p className="font-bold text-sm truncate">{decodeHtml(m.display_name)}</p>
                                        <p className="text-xs text-text-2 truncate">{m.email}</p>
                                    </div>
                                    <span className={`badge ${m.status === 'active' ? 'badge-green' : 'badge-amber'}`}>
                                        {m.status}
                                    </span>
                                </div>
                                <div className="flex items-center gap-4 text-xs text-text-3 mb-3">
                                    {m.industry && <span>{m.industry}</span>}
                                    <span>{m.booking_count ?? 0} bookings</span>
                                    {m.average_rating != null && m.average_rating > 0 && (
                                        <span className="flex items-center gap-1">
                                            <svg width="10" height="10" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" strokeWidth="2">
                                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                            </svg>
                                            {m.average_rating.toFixed(1)}
                                        </span>
                                    )}
                                </div>
                                <div className="flex gap-2">
                                    <a href={`/paired/mentors/${m.id}`} className="btn btn-ghost btn-sm text-xs">View</a>
                                    <button onClick={() => openEdit(m)} className="btn btn-ghost btn-sm text-xs">Edit</button>
                                    <button
                                        onClick={() => m.status === 'active' ? setConfirmDeactivate(m) : toggleStatus(m)}
                                        disabled={deactivatingId === m.id}
                                        className="btn btn-ghost btn-sm text-xs"
                                        style={{ color: m.status === 'active' ? 'var(--err)' : 'var(--ok)' }}
                                    >
                                        {deactivatingId === m.id ? '...' : m.status === 'active' ? 'Deactivate' : 'Activate'}
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <div className="flex items-center justify-center gap-2 pt-4">
                            <button
                                onClick={() => setPage(p => Math.max(1, p - 1))}
                                disabled={page === 1}
                                className="btn btn-outline btn-sm"
                            >
                                Previous
                            </button>
                            <span className="text-sm text-text-2">
                                Page {page} of {totalPages}
                            </span>
                            <button
                                onClick={() => setPage(p => Math.min(totalPages, p + 1))}
                                disabled={page === totalPages}
                                className="btn btn-outline btn-sm"
                            >
                                Next
                            </button>
                        </div>
                    )}
                </>
            )}

            {/* Edit Modal */}
            {editingMentor && (
                <div
                    style={{
                        position: 'fixed',
                        inset: 0,
                        background: 'rgba(0,0,0,0.5)',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        zIndex: 100,
                        padding: 16,
                    }}
                    onClick={() => setEditingMentor(null)}
                >
                    <div
                        className="card card-p"
                        style={{ width: '100%', maxWidth: 480 }}
                        onClick={(e) => e.stopPropagation()}
                    >
                        <h2 className="text-lg font-bold mb-4">Edit Mentor</h2>
                        {editError && <div className="alert alert-red mb-4">{editError}</div>}

                        <div className="space-y-4">
                            <div>
                                <label htmlFor="edit-name" className="field-label mb-1 block">Display Name</label>
                                <input
                                    id="edit-name"
                                    type="text"
                                    className="field-input"
                                    value={editData.display_name}
                                    onChange={(e) => setEditData(prev => ({ ...prev, display_name: e.target.value }))}
                                />
                            </div>
                            <div>
                                <label htmlFor="edit-email" className="field-label mb-1 block">Email</label>
                                <input
                                    id="edit-email"
                                    type="email"
                                    className="field-input"
                                    value={editData.email}
                                    onChange={(e) => setEditData(prev => ({ ...prev, email: e.target.value }))}
                                />
                            </div>
                            <div>
                                <label htmlFor="edit-industry" className="field-label mb-1 block">Industry</label>
                                <select
                                    id="edit-industry"
                                    className="field-input"
                                    value={editData.industry}
                                    onChange={(e) => setEditData(prev => ({ ...prev, industry: e.target.value }))}
                                >
                                    <option value="">Select industry</option>
                                    {INDUSTRIES.map((ind) => (
                                        <option key={ind} value={ind}>{ind}</option>
                                    ))}
                                </select>
                            </div>
                        </div>

                        <div className="flex gap-2 mt-6">
                            <button
                                onClick={saveEdit}
                                disabled={editSaving}
                                className="btn btn-purple flex-1"
                            >
                                {editSaving ? 'Saving...' : 'Save Changes'}
                            </button>
                            <button
                                onClick={() => setEditingMentor(null)}
                                className="btn btn-outline"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Deactivate Confirmation Modal */}
            {confirmDeactivate && (
                <div
                    style={{
                        position: 'fixed',
                        inset: 0,
                        background: 'rgba(0,0,0,0.5)',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        zIndex: 100,
                        padding: 16,
                    }}
                    onClick={() => setConfirmDeactivate(null)}
                >
                    <div
                        className="card card-p"
                        style={{ width: '100%', maxWidth: 400 }}
                        onClick={(e) => e.stopPropagation()}
                    >
                        <h2 className="text-lg font-bold mb-2">Deactivate Mentor</h2>
                        <p className="text-sm text-text-2 mb-6">
                            Are you sure you want to deactivate <strong>{decodeHtml(confirmDeactivate.display_name)}</strong>?
                            They will no longer appear in the mentor directory.
                        </p>
                        <div className="flex gap-2">
                            <button
                                onClick={() => toggleStatus(confirmDeactivate)}
                                disabled={actionLoading}
                                className="btn flex-1"
                                style={{ background: 'var(--err)', color: '#fff', border: 'none' }}
                            >
                                {actionLoading ? 'Processing...' : 'Yes, Deactivate'}
                            </button>
                            <button
                                onClick={() => setConfirmDeactivate(null)}
                                className="btn btn-outline"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
