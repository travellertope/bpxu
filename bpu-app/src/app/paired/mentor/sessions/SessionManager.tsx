'use client';

import { useState } from 'react';

interface SessionType {
    id: number;
    name: string;
    duration: number;
    description: string;
    price: number;
    type: string;
    visibility: string;
    group_booking: boolean;
    slot_capacity: number;
    cover_image: string;
}

interface FormData {
    name: string;
    duration: number;
    description: string;
    type: string;
    visibility: string;
    group_booking: boolean;
    slot_capacity: number;
}

const EMPTY_FORM: FormData = {
    name: '',
    duration: 60,
    description: '',
    type: 'one_off',
    visibility: 'visible',
    group_booking: false,
    slot_capacity: 1,
};

function SessionForm({
    initial,
    onSubmit,
    onCancel,
    submitLabel,
    loading,
}: {
    initial: FormData;
    onSubmit: (data: FormData) => void;
    onCancel: () => void;
    submitLabel: string;
    loading: boolean;
}) {
    const [form, setForm] = useState<FormData>(initial);

    function update<K extends keyof FormData>(key: K, value: FormData[K]) {
        setForm(prev => ({ ...prev, [key]: value }));
    }

    return (
        <form
            onSubmit={e => {
                e.preventDefault();
                onSubmit(form);
            }}
            className="space-y-4"
        >
            <div>
                <label className="field-label">Session name *</label>
                <input
                    type="text"
                    className="field-input w-full"
                    value={form.name}
                    onChange={e => update('name', e.target.value)}
                    placeholder="e.g. Career Strategy Call"
                    required
                />
            </div>

            <div className="grid grid-cols-2 gap-4">
                <div>
                    <label className="field-label">Duration</label>
                    <select
                        className="field-input w-full"
                        value={form.duration}
                        onChange={e => update('duration', Number(e.target.value))}
                    >
                        <option value={30}>30 minutes</option>
                        <option value={45}>45 minutes</option>
                        <option value={60}>60 minutes</option>
                    </select>
                </div>
                <div>
                    <label className="field-label">Type</label>
                    <select
                        className="field-input w-full"
                        value={form.type}
                        onChange={e => update('type', e.target.value)}
                    >
                        <option value="one_off">One-off</option>
                        <option value="recurring">Recurring</option>
                    </select>
                </div>
            </div>

            <div>
                <label className="field-label">Description</label>
                <textarea
                    className="field-input field-textarea w-full"
                    rows={3}
                    value={form.description}
                    onChange={e => update('description', e.target.value)}
                    placeholder="Describe what this session covers..."
                />
            </div>

            <div className="flex items-center gap-6">
                <label className="flex items-center gap-2 text-sm cursor-pointer">
                    <input
                        type="checkbox"
                        checked={form.visibility === 'visible'}
                        onChange={e => update('visibility', e.target.checked ? 'visible' : 'hidden')}
                    />
                    Visible to mentees
                </label>
                <label className="flex items-center gap-2 text-sm cursor-pointer">
                    <input
                        type="checkbox"
                        checked={form.group_booking}
                        onChange={e => update('group_booking', e.target.checked)}
                    />
                    Group booking
                </label>
            </div>

            {form.group_booking && (
                <div>
                    <label className="field-label">Slot capacity</label>
                    <input
                        type="number"
                        className="field-input"
                        min={2}
                        max={50}
                        value={form.slot_capacity}
                        onChange={e => update('slot_capacity', Number(e.target.value))}
                        style={{ width: 100 }}
                    />
                </div>
            )}

            <div className="flex gap-3 pt-2">
                <button type="submit" disabled={loading} className="btn btn-purple btn-sm">
                    {loading ? 'Saving...' : submitLabel}
                </button>
                <button type="button" onClick={onCancel} className="btn btn-ghost btn-sm">
                    Cancel
                </button>
            </div>
        </form>
    );
}

export default function SessionManager({ initial }: { initial: SessionType[] }) {
    const [sessions, setSessions] = useState<SessionType[]>(initial);
    const [showCreate, setShowCreate] = useState(false);
    const [editingId, setEditingId] = useState<number | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [deletingId, setDeletingId] = useState<number | null>(null);

    function flash(msg: string, type: 'success' | 'error') {
        if (type === 'success') {
            setSuccess(msg);
            setError('');
        } else {
            setError(msg);
            setSuccess('');
        }
        setTimeout(() => {
            setSuccess('');
            setError('');
        }, 4000);
    }

    async function handleCreate(data: FormData) {
        setLoading(true);
        try {
            const res = await fetch('/api/paired/mentor/sessions', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });
            const result = await res.json();
            if (!res.ok) {
                flash(result.error || 'Failed to create session.', 'error');
                return;
            }
            const created = result.session || result;
            setSessions(prev => [...prev, created]);
            setShowCreate(false);
            flash('Session type created.', 'success');
        } catch {
            flash('Something went wrong. Please try again.', 'error');
        } finally {
            setLoading(false);
        }
    }

    async function handleUpdate(id: number, data: FormData) {
        setLoading(true);
        try {
            const res = await fetch(`/api/paired/mentor/sessions/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });
            const result = await res.json();
            if (!res.ok) {
                flash(result.error || 'Failed to update session.', 'error');
                return;
            }
            const updated = result.session || result;
            setSessions(prev => prev.map(s => (s.id === id ? { ...s, ...updated } : s)));
            setEditingId(null);
            flash('Session type updated.', 'success');
        } catch {
            flash('Something went wrong. Please try again.', 'error');
        } finally {
            setLoading(false);
        }
    }

    async function handleDelete(id: number) {
        setLoading(true);
        try {
            const res = await fetch(`/api/paired/mentor/sessions/${id}`, {
                method: 'DELETE',
            });
            if (!res.ok) {
                const result = await res.json().catch(() => ({}));
                flash(result.error || 'Failed to delete session.', 'error');
                return;
            }
            setSessions(prev => prev.filter(s => s.id !== id));
            setDeletingId(null);
            flash('Session type deleted.', 'success');
        } catch {
            flash('Something went wrong. Please try again.', 'error');
        } finally {
            setLoading(false);
        }
    }

    return (
        <div>
            {/* Top button — shown when sessions exist and form is not open */}
            {sessions.length > 0 && !showCreate && (
                <div className="flex justify-end mb-4">
                    <button
                        className="btn btn-purple"
                        onClick={() => { setShowCreate(true); setEditingId(null); setDeletingId(null); }}
                    >
                        + New Session Type
                    </button>
                </div>
            )}

            {error && <div className="alert alert-red mb-6">{error}</div>}
            {success && <div className="alert alert-green mb-6">{success}</div>}

            {/* Create form */}
            {showCreate && (
                <div className="card card-p mb-6">
                    <h2 className="text-lg font-semibold mb-4">Create new session type</h2>
                    <SessionForm
                        initial={EMPTY_FORM}
                        onSubmit={handleCreate}
                        onCancel={() => setShowCreate(false)}
                        submitLabel="Create Session"
                        loading={loading}
                    />
                </div>
            )}

            {/* Session cards */}
            {sessions.length === 0 && !showCreate ? (
                <div className="card card-p text-center py-16">
                    <p className="text-text-3 text-sm mb-4">
                        No session types yet. Create your first one to start receiving bookings.
                    </p>
                    <button
                        className="btn btn-purple btn-sm"
                        onClick={() => setShowCreate(true)}
                    >
                        + Create session type
                    </button>
                </div>
            ) : (
                <div className="grid gap-4">
                    {sessions.map(session => (
                        <div key={session.id} className="card card-p card-lift">
                            {editingId === session.id ? (
                                <div>
                                    <h3 className="text-lg font-semibold mb-4">Edit session type</h3>
                                    <SessionForm
                                        initial={{
                                            name: session.name,
                                            duration: session.duration,
                                            description: session.description || '',
                                            type: session.type || 'one_off',
                                            visibility: session.visibility || 'visible',
                                            group_booking: session.group_booking || false,
                                            slot_capacity: session.slot_capacity || 1,
                                        }}
                                        onSubmit={data => handleUpdate(session.id, data)}
                                        onCancel={() => setEditingId(null)}
                                        submitLabel="Save Changes"
                                        loading={loading}
                                    />
                                </div>
                            ) : deletingId === session.id ? (
                                <div className="space-y-3">
                                    <p className="text-sm font-medium">
                                        Delete &ldquo;{session.name}&rdquo;? This cannot be undone.
                                    </p>
                                    <div className="flex gap-3">
                                        <button
                                            className="btn btn-sm"
                                            style={{ backgroundColor: 'var(--err)', color: '#fff' }}
                                            disabled={loading}
                                            onClick={() => handleDelete(session.id)}
                                        >
                                            {loading ? 'Deleting...' : 'Yes, delete'}
                                        </button>
                                        <button
                                            className="btn btn-ghost btn-sm"
                                            onClick={() => setDeletingId(null)}
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            ) : (
                                <div className="flex items-start justify-between gap-4">
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2 flex-wrap mb-1">
                                            <h3 className="text-base font-semibold">{session.name}</h3>
                                            <span className="badge badge-purple">{session.duration} min</span>
                                            <span className="badge">
                                                {session.type === 'recurring' ? 'Recurring' : 'One-off'}
                                            </span>
                                            {session.visibility === 'hidden' && (
                                                <span className="badge badge-amber">Hidden</span>
                                            )}
                                            {session.group_booking && (
                                                <span className="badge badge-green">
                                                    Group ({session.slot_capacity})
                                                </span>
                                            )}
                                        </div>
                                        {session.description && (
                                            <p className="text-sm text-text-2 mt-1 line-clamp-2">
                                                {session.description}
                                            </p>
                                        )}
                                    </div>
                                    <div className="flex gap-2 shrink-0">
                                        <button
                                            className="btn btn-outline btn-sm"
                                            onClick={() => {
                                                setEditingId(session.id);
                                                setDeletingId(null);
                                            }}
                                        >
                                            Edit
                                        </button>
                                        <button
                                            className="btn btn-ghost btn-sm"
                                            onClick={() => {
                                                setDeletingId(session.id);
                                                setEditingId(null);
                                            }}
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
