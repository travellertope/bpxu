'use client';

import { useState, useEffect } from 'react';

interface PayoutData {
    stripe_account_id: string | null;
    payout_enabled: boolean;
    onboarding_complete: boolean;
}

export default function PayoutSettings() {
    const [data, setData] = useState<PayoutData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [connecting, setConnecting] = useState(false);

    useEffect(() => {
        async function load() {
            try {
                const res = await fetch('/api/paired/mentor/payout-settings');
                const json = await res.json();
                if (!res.ok) throw new Error(json.error || 'Failed to load payout settings.');
                setData(json);
            } catch (e) {
                setError(e instanceof Error ? e.message : 'Failed to load payout settings.');
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    async function handleConnect() {
        setConnecting(true);
        setError('');
        try {
            const res = await fetch('/api/paired/mentor/payout-settings', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'create_onboarding_link' }),
            });
            const json = await res.json();
            if (!res.ok) throw new Error(json.error || 'Failed to create onboarding link.');
            if (json.onboarding_url) {
                window.location.href = json.onboarding_url;
            }
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to connect Stripe.');
            setConnecting(false);
        }
    }

    if (loading) {
        return (
            <div className="fade-up">
                <div className="text-center text-sm text-text-2 py-12">Loading payout settings...</div>
            </div>
        );
    }

    return (
        <div className="fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px', maxWidth: '640px' }}>
            <div>
                <h1 className="text-3xl font-extrabold tracking-tight">Payout Settings</h1>
                <p className="mt-2" style={{ color: 'var(--text-2)' }}>
                    Connect your Stripe account to receive payouts from mentoring sessions.
                </p>
            </div>

            {error && (
                <div className="alert alert-red">{error}</div>
            )}

            <div className="card card-p">
                <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                        <h2 className="text-lg font-bold">Stripe Connect</h2>
                        {data?.payout_enabled ? (
                            <span className="badge badge-green">Payouts enabled</span>
                        ) : (
                            <span className="badge badge-amber">Not connected</span>
                        )}
                    </div>

                    {data?.payout_enabled ? (
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                            <div className="flex items-center gap-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--ok)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <polyline points="22 4 12 14.01 9 11.01" />
                                </svg>
                                <span className="font-medium">Your Stripe account is connected</span>
                            </div>
                            {data.stripe_account_id && (
                                <p className="text-sm" style={{ color: 'var(--text-3)' }}>
                                    Account: {data.stripe_account_id}
                                </p>
                            )}
                        </div>
                    ) : (
                        <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                            <p className="text-sm" style={{ color: 'var(--text-2)' }}>
                                You need to connect a Stripe account before you can receive payouts.
                                Click the button below to set up your account through Stripe&apos;s secure onboarding process.
                            </p>
                            <button
                                onClick={handleConnect}
                                disabled={connecting}
                                className="btn btn-purple"
                            >
                                {connecting ? 'Redirecting to Stripe...' : 'Connect Stripe Account'}
                            </button>
                        </div>
                    )}

                    <div
                        className="text-sm rounded-lg p-4"
                        style={{ background: 'var(--purple-bg)', color: 'var(--text-2)' }}
                    >
                        <p className="font-medium mb-1" style={{ color: 'var(--text)' }}>Platform fee</p>
                        <p>
                            PAIRED retains a 10% platform fee on each session payment to cover
                            operational costs. The remaining 90% is paid out to your connected Stripe account.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    );
}
