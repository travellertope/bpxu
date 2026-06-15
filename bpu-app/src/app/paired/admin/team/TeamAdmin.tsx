'use client';

import { useState, useEffect, useCallback } from 'react';

interface TeamMember {
    id: number;
    display_name: string;
    email: string;
    role: string;
    role_label: string;
    capabilities: string[];
    registered: string;
}

interface RoleInfo {
    slug: string;
    label: string;
    description: string;
    capabilities: string[];
}

interface TeamData {
    members: TeamMember[];
    counts: Record<string, number>;
    roles: RoleInfo[];
}

const CAP_LABELS: Record<string, string> = {
    bpu_view_dashboard: 'View Dashboard',
    bpu_manage_jobs: 'Manage Jobs',
    bpu_manage_applications: 'Manage Applications',
    bpu_manage_team: 'Manage Team',
    bpu_view_reports: 'View Reports',
};

const ROLE_COLORS: Record<string, string> = {
    administrator: 'badge-purple',
    bpu_editor: 'badge-blue',
    bpu_moderator: 'badge-amber',
};

export default function TeamAdmin() {
    const [data, setData] = useState<TeamData | null>(null);
    const [loading, setLoading] = useState(true);
    const [filter, setFilter] = useState('all');
    const [inviteOpen, setInviteOpen] = useState(false);
    const [inviteEmail, setInviteEmail] = useState('');
    const [inviteRole, setInviteRole] = useState('bpu_moderator');
    const [inviting, setInviting] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    const fetchTeam = useCallback(async () => {
        try {
            const res = await fetch('/api/paired/admin/team');
            if (!res.ok) throw new Error('Failed to fetch');
            const json = await res.json();
            setData(json);
        } catch {
            setError('Failed to load team data.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => { fetchTeam(); }, [fetchTeam]);

    const handleRoleChange = async (memberId: number, newRole: string) => {
        setError('');
        setSuccess('');
        try {
            const res = await fetch(`/api/paired/admin/team/${memberId}/role`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ role: newRole }),
            });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Failed to update role');
            setSuccess('Role updated successfully.');
            fetchTeam();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to update role.');
        }
    };

    const handleRemove = async (memberId: number, name: string) => {
        if (!confirm(`Remove ${name} from the admin team? They will lose all admin capabilities.`)) return;
        setError('');
        setSuccess('');
        try {
            const res = await fetch(`/api/paired/admin/team/${memberId}/remove`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({}),
            });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Failed to remove');
            setSuccess(`${name} removed from the team.`);
            fetchTeam();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to remove member.');
        }
    };

    const handleInvite = async (e: React.FormEvent) => {
        e.preventDefault();
        setInviting(true);
        setError('');
        setSuccess('');
        try {
            const res = await fetch('/api/paired/admin/team/invite', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: inviteEmail, role: inviteRole }),
            });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || 'Failed to invite');
            setSuccess(`${json.member?.display_name || inviteEmail} added as ${inviteRole === 'bpu_editor' ? 'Editor' : 'Moderator'}.`);
            setInviteEmail('');
            setInviteOpen(false);
            fetchTeam();
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Failed to invite member.');
        } finally {
            setInviting(false);
        }
    };

    if (loading) return <div className="card card-p text-center py-12 text-text-3">Loading team...</div>;
    if (!data) return <div className="card card-p text-center py-12 text-text-3">Failed to load.</div>;

    const members = filter === 'all' ? data.members : data.members.filter(m => m.role === filter);

    return (
        <div className="space-y-6">
            {error && <div className="card card-p" style={{ borderLeft: '4px solid #ef4444', background: 'rgba(239,68,68,0.08)' }}><p className="text-sm" style={{ color: '#ef4444' }}>{error}</p></div>}
            {success && <div className="card card-p" style={{ borderLeft: '4px solid #22c55e', background: 'rgba(34,197,94,0.08)' }}><p className="text-sm" style={{ color: '#22c55e' }}>{success}</p></div>}

            {/* Summary cards */}
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div className="card card-p text-center">
                    <p className="text-2xl font-bold">{data.counts.all}</p>
                    <p className="text-xs text-text-3">Total Members</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-2xl font-bold">{data.counts.administrator}</p>
                    <p className="text-xs text-text-3">Administrators</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-2xl font-bold">{data.counts.bpu_editor}</p>
                    <p className="text-xs text-text-3">Editors</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-2xl font-bold">{data.counts.bpu_moderator}</p>
                    <p className="text-xs text-text-3">Moderators</p>
                </div>
            </div>

            {/* Roles reference */}
            <div className="card card-p">
                <h3 className="font-semibold text-sm mb-3">Role Permissions</h3>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {data.roles.map(role => (
                        <div key={role.slug} className="p-3 rounded-lg" style={{ background: 'var(--surface-2)' }}>
                            <p className="font-semibold text-sm">{role.label}</p>
                            <p className="text-xs text-text-3 mb-2">{role.description}</p>
                            <div className="flex flex-wrap gap-1">
                                {role.capabilities.map(cap => (
                                    <span key={cap} className="text-xs px-2 py-0.5 rounded-full" style={{ background: 'var(--surface-3)', color: 'var(--text-2)' }}>
                                        {CAP_LABELS[cap] || cap}
                                    </span>
                                ))}
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Actions bar */}
            <div className="flex flex-wrap items-center justify-between gap-3">
                <div className="flex flex-wrap gap-2">
                    {[
                        { key: 'all', label: 'All', count: data.counts.all },
                        { key: 'administrator', label: 'Admins', count: data.counts.administrator },
                        { key: 'bpu_editor', label: 'Editors', count: data.counts.bpu_editor },
                        { key: 'bpu_moderator', label: 'Moderators', count: data.counts.bpu_moderator },
                    ].map(tab => (
                        <button
                            key={tab.key}
                            onClick={() => setFilter(tab.key)}
                            className={`btn btn-sm ${filter === tab.key ? 'btn-purple' : 'btn-ghost'}`}
                        >
                            {tab.label} <span className="ml-1 opacity-60">{tab.count}</span>
                        </button>
                    ))}
                </div>
                <button onClick={() => setInviteOpen(!inviteOpen)} className="btn btn-purple btn-sm">
                    Add Team Member +
                </button>
            </div>

            {/* Invite form */}
            {inviteOpen && (
                <div className="card card-p">
                    <h3 className="font-semibold text-sm mb-3">Add Team Member</h3>
                    <p className="text-xs text-text-3 mb-4">The user must have an existing account. Enter their email to add them to the admin team.</p>
                    <form onSubmit={handleInvite} className="flex flex-col sm:flex-row gap-3">
                        <input
                            type="email"
                            placeholder="Email address"
                            value={inviteEmail}
                            onChange={e => setInviteEmail(e.target.value)}
                            required
                            className="input flex-1"
                        />
                        <select
                            value={inviteRole}
                            onChange={e => setInviteRole(e.target.value)}
                            className="input w-full sm:w-40"
                        >
                            <option value="bpu_moderator">Moderator</option>
                            <option value="bpu_editor">Editor</option>
                        </select>
                        <div className="flex gap-2">
                            <button type="submit" disabled={inviting} className="btn btn-purple btn-sm">
                                {inviting ? 'Adding...' : 'Add'}
                            </button>
                            <button type="button" onClick={() => setInviteOpen(false)} className="btn btn-ghost btn-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            )}

            {/* Members table (desktop) */}
            <div className="card overflow-hidden hidden md:block">
                <table className="w-full text-sm">
                    <thead>
                        <tr className="border-b border-border">
                            <th className="text-left px-4 py-3 font-medium text-text-3">Name</th>
                            <th className="text-left px-4 py-3 font-medium text-text-3">Email</th>
                            <th className="text-left px-4 py-3 font-medium text-text-3">Role</th>
                            <th className="text-left px-4 py-3 font-medium text-text-3">Capabilities</th>
                            <th className="text-right px-4 py-3 font-medium text-text-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {members.map(m => (
                            <tr key={m.id} className="border-b border-border last:border-0 hover:bg-surface-2 transition-colors">
                                <td className="px-4 py-3 font-medium">{m.display_name}</td>
                                <td className="px-4 py-3 text-text-2">{m.email}</td>
                                <td className="px-4 py-3">
                                    {m.role === 'administrator' ? (
                                        <span className={`badge ${ROLE_COLORS[m.role]}`}>{m.role_label}</span>
                                    ) : (
                                        <select
                                            value={m.role}
                                            onChange={e => handleRoleChange(m.id, e.target.value)}
                                            className="input input-sm text-xs"
                                        >
                                            <option value="bpu_editor">Editor</option>
                                            <option value="bpu_moderator">Moderator</option>
                                        </select>
                                    )}
                                </td>
                                <td className="px-4 py-3">
                                    <div className="flex flex-wrap gap-1">
                                        {m.capabilities.map(cap => (
                                            <span key={cap} className="text-xs px-1.5 py-0.5 rounded" style={{ background: 'var(--surface-3)', color: 'var(--text-3)' }}>
                                                {CAP_LABELS[cap] || cap}
                                            </span>
                                        ))}
                                    </div>
                                </td>
                                <td className="px-4 py-3 text-right">
                                    {m.role !== 'administrator' && (
                                        <button
                                            onClick={() => handleRemove(m.id, m.display_name)}
                                            className="btn btn-ghost btn-sm text-xs"
                                            style={{ color: '#ef4444' }}
                                        >
                                            Remove
                                        </button>
                                    )}
                                </td>
                            </tr>
                        ))}
                        {members.length === 0 && (
                            <tr><td colSpan={5} className="px-4 py-8 text-center text-text-3">No team members found.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>

            {/* Members cards (mobile) */}
            <div className="md:hidden space-y-3">
                {members.map(m => (
                    <div key={m.id} className="card card-p space-y-3">
                        <div className="flex items-start justify-between gap-2">
                            <div>
                                <p className="font-semibold text-sm">{m.display_name}</p>
                                <p className="text-xs text-text-3">{m.email}</p>
                            </div>
                            <span className={`badge ${ROLE_COLORS[m.role] || 'badge-default'}`}>{m.role_label}</span>
                        </div>
                        <div className="flex flex-wrap gap-1">
                            {m.capabilities.map(cap => (
                                <span key={cap} className="text-xs px-1.5 py-0.5 rounded" style={{ background: 'var(--surface-3)', color: 'var(--text-3)' }}>
                                    {CAP_LABELS[cap] || cap}
                                </span>
                            ))}
                        </div>
                        {m.role !== 'administrator' && (
                            <div className="flex gap-2">
                                <select
                                    value={m.role}
                                    onChange={e => handleRoleChange(m.id, e.target.value)}
                                    className="input input-sm text-xs flex-1"
                                >
                                    <option value="bpu_editor">Editor</option>
                                    <option value="bpu_moderator">Moderator</option>
                                </select>
                                <button
                                    onClick={() => handleRemove(m.id, m.display_name)}
                                    className="btn btn-ghost btn-sm text-xs"
                                    style={{ color: '#ef4444' }}
                                >
                                    Remove
                                </button>
                            </div>
                        )}
                    </div>
                ))}
                {members.length === 0 && (
                    <div className="card card-p text-center py-8 text-text-3">No team members found.</div>
                )}
            </div>
        </div>
    );
}
