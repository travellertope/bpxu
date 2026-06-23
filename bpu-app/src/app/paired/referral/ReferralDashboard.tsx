'use client';

import { useState, useEffect } from 'react';

interface Referral {
    id: number;
    referee_name: string;
    created_at: string;
    is_mentor: boolean;
    status: string;
}

interface ReferralStats {
    total_referrals: number;
    points: number;
}

export default function ReferralDashboard() {
    const [code, setCode] = useState('');
    const [shareLink, setShareLink] = useState('');
    const [stats, setStats] = useState<ReferralStats>({ total_referrals: 0, points: 0 });
    const [referrals, setReferrals] = useState<Referral[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [copied, setCopied] = useState<'code' | 'link' | null>(null);

    useEffect(() => {
        async function load() {
            try {
                const [codeRes, statsRes] = await Promise.all([
                    fetch('/api/paired/referral/code'),
                    fetch('/api/paired/referral/stats'),
                ]);
                const codeData = await codeRes.json();
                const statsData = await statsRes.json();
                if (!codeRes.ok) throw new Error(codeData.error || 'Failed to load referral code.');
                if (!statsRes.ok) throw new Error(statsData.error || 'Failed to load referral stats.');
                setCode(codeData.code || '');
                // Use the canonical link from the API (points to /register?ref=CODE)
                setShareLink(codeData.link || '');
                setStats({
                    total_referrals: statsData.total_referrals || 0,
                    points: statsData.points || 0,
                });
                setReferrals(statsData.referrals || []);
            } catch (e) {
                setError(e instanceof Error ? e.message : 'Failed to load referral data.');
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    async function copyToClipboard(text: string, type: 'code' | 'link') {
        try {
            await navigator.clipboard.writeText(text);
            setCopied(type);
            setTimeout(() => setCopied(null), 2000);
        } catch {
            /* clipboard not available */
        }
    }

    function shareOnLinkedIn() {
        const url = encodeURIComponent(shareLink);
        const text = encodeURIComponent(
            'Join me on PAIRED - a mentorship platform connecting Black professionals with experienced mentors.'
        );
        window.open(
            `https://www.linkedin.com/sharing/share-offsite/?url=${url}&summary=${text}`,
            '_blank',
            'noopener,noreferrer'
        );
    }

    function shareOnX() {
        const url = encodeURIComponent(shareLink);
        const text = encodeURIComponent(
            'Join me on PAIRED - a mentorship platform connecting Black professionals with experienced mentors.'
        );
        window.open(
            `https://x.com/intent/tweet?url=${url}&text=${text}`,
            '_blank',
            'noopener,noreferrer'
        );
    }

    if (loading) {
        return (
            <div className="fade-up">
                <div className="text-center text-sm py-12" style={{ color: 'var(--text-2)' }}>
                    Loading referral dashboard...
                </div>
            </div>
        );
    }

    return (
        <div className="fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px', maxWidth: '800px' }}>
            <div>
                <h1 className="text-3xl font-extrabold tracking-tight">Referral Dashboard</h1>
                <p className="mt-2" style={{ color: 'var(--text-2)' }}>
                    Invite others to join PAIRED and earn referral points.
                </p>
            </div>

            {error && <div className="alert alert-red">{error}</div>}

            {/* Referral code and link */}
            <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                <h2 className="text-lg font-bold">Your Referral Code</h2>

                {/* Code display */}
                <div style={{ display: 'flex', alignItems: 'center', gap: '12px' }}>
                    <div
                        className="font-mono text-lg font-bold px-4 py-2 rounded-lg flex-1 text-center"
                        style={{ background: 'var(--bg)', border: '1px solid var(--border)', letterSpacing: '0.1em' }}
                    >
                        {code || '---'}
                    </div>
                    <button
                        onClick={() => copyToClipboard(code, 'code')}
                        className="btn btn-outline btn-sm"
                        disabled={!code}
                    >
                        {copied === 'code' ? 'Copied!' : 'Copy'}
                    </button>
                </div>

                {/* Shareable link */}
                <div>
                    <label className="field-label">Shareable Link</label>
                    <div className="flex items-center gap-2 mt-1">
                        <input
                            type="text"
                            readOnly
                            value={shareLink}
                            className="field-input flex-1 text-sm"
                            style={{ color: 'var(--text-2)' }}
                        />
                        <button
                            onClick={() => copyToClipboard(shareLink, 'link')}
                            className="btn btn-outline btn-sm"
                            disabled={!shareLink}
                        >
                            {copied === 'link' ? 'Copied!' : 'Copy Link'}
                        </button>
                    </div>
                </div>

                {/* Share buttons */}
                <div className="flex flex-wrap gap-3">
                    <button onClick={() => copyToClipboard(shareLink, 'link')} className="btn btn-purple btn-sm">
                        Copy Link
                    </button>
                    <button onClick={shareOnLinkedIn} className="btn btn-outline btn-sm">
                        Share on LinkedIn
                    </button>
                    <button onClick={shareOnX} className="btn btn-outline btn-sm">
                        Share on X
                    </button>
                </div>
            </div>

            {/* Stats */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                <div className="card card-p text-center">
                    <p className="stat-val">{stats.total_referrals}</p>
                    <p className="stat-label">Total Referrals</p>
                </div>
                <div className="card card-p text-center">
                    <p className="stat-val">{stats.points}</p>
                    <p className="stat-label">Points Earned</p>
                </div>
            </div>

            {/* Referrals table */}
            <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                <h2 className="text-lg font-bold">Your Referrals</h2>

                {referrals.length === 0 ? (
                    <div className="text-center py-10" style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                        <p className="font-semibold" style={{ color: 'var(--text-2)' }}>No referrals yet</p>
                        <p className="text-sm" style={{ color: 'var(--text-3)' }}>
                            Share your referral link to start earning points.
                        </p>
                    </div>
                ) : (
                    <div style={{ overflowX: 'auto' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)' }}>
                                    <th className="text-left text-xs font-medium py-3 px-2" style={{ color: 'var(--text-3)' }}>Name</th>
                                    <th className="text-left text-xs font-medium py-3 px-2" style={{ color: 'var(--text-3)' }}>Date</th>
                                    <th className="text-left text-xs font-medium py-3 px-2" style={{ color: 'var(--text-3)' }}>Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                {referrals.map(ref => (
                                    <tr key={ref.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="py-3 px-2 text-sm font-medium">{ref.referee_name}</td>
                                        <td className="py-3 px-2 text-sm" style={{ color: 'var(--text-2)' }}>
                                            {new Date(ref.created_at).toLocaleDateString('en-GB', {
                                                day: 'numeric', month: 'short', year: 'numeric',
                                            })}
                                        </td>
                                        <td className="py-3 px-2">
                                            <span className={`badge ${ref.is_mentor ? 'badge-purple' : 'badge-amber'}`}>
                                                {ref.is_mentor ? 'Mentor' : 'Mentee'}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
}
