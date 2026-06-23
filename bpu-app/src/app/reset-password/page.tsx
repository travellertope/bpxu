'use client';

import { Suspense, useEffect, useState } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';

function ResetPasswordForm() {
    const searchParams = useSearchParams();
    const router = useRouter();
    const token = searchParams.get('token') || '';

    const [password, setPassword]   = useState('');
    const [confirm, setConfirm]     = useState('');
    const [loading, setLoading]     = useState(false);
    const [error, setError]         = useState('');
    const [success, setSuccess]     = useState(false);
    const [showPass, setShowPass]   = useState(false);

    useEffect(() => {
        if (!token) {
            setError('Missing reset token. Please request a new reset link.');
        }
    }, [token]);

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');

        if (password.length < 8) {
            setError('Password must be at least 8 characters.');
            return;
        }
        if (password !== confirm) {
            setError('Passwords do not match.');
            return;
        }

        setLoading(true);
        try {
            const res = await fetch('/api/auth/reset-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ token, password }),
            });
            const data = await res.json();
            if (!res.ok) {
                setError(data.error || 'Reset failed. The link may have expired.');
                setLoading(false);
                return;
            }
            setSuccess(true);
            setTimeout(() => router.push('/login'), 3000);
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setLoading(false);
        }
    }

    const passwordStrength = (p: string) => {
        if (!p) return null;
        if (p.length < 8) return { label: 'Too short', color: 'var(--err)' };
        const hasUpper = /[A-Z]/.test(p);
        const hasNum   = /[0-9]/.test(p);
        const hasSpec  = /[^a-zA-Z0-9]/.test(p);
        const score    = [p.length >= 12, hasUpper, hasNum, hasSpec].filter(Boolean).length;
        if (score <= 1) return { label: 'Weak', color: 'var(--err)' };
        if (score === 2) return { label: 'Fair', color: '#f59e0b' };
        if (score === 3) return { label: 'Good', color: '#3b82f6' };
        return { label: 'Strong', color: 'var(--ok)' };
    };

    const strength = passwordStrength(password);

    return (
        <main className="min-h-screen flex">

            {/* ── Left panel ──────────────────────────────────────────── */}
            <div className="hidden lg:flex lg:w-[58%] relative flex-col overflow-hidden">
                <div
                    className="absolute inset-0 bg-cover bg-center bg-no-repeat"
                    style={{ backgroundImage: `url('https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=1400&q=80')` }}
                />
                <div
                    className="absolute inset-0"
                    style={{ background: 'linear-gradient(145deg,rgba(0,0,0,0.88) 0%,rgba(200,16,46,0.40) 100%)' }}
                />
                <div className="relative z-10 flex flex-col justify-between h-full p-14">
                    <img
                        src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png"
                        alt="Black Professionals United"
                        className="h-10 w-auto self-start brightness-0 invert"
                    />
                    <div>
                        <h2 className="text-5xl font-extrabold text-white leading-[1.1] mb-5 tracking-tight">
                            Set a new<br />password.
                        </h2>
                        <p className="text-white/70 text-lg leading-relaxed max-w-xs">
                            Choose a strong password to keep your BPU account secure.
                        </p>
                    </div>
                    <p className="text-white/25 text-xs">Photo: Christina @ wocintechchat.com / Unsplash</p>
                </div>
            </div>

            {/* ── Right panel: form ───────────────────────────────────── */}
            <div className="flex-1 flex flex-col items-center justify-center p-8 bg-white">
                <div className="w-full max-w-sm fade-up">

                    {/* Logo — mobile only */}
                    <div className="lg:hidden text-center mb-8">
                        <img
                            src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png"
                            alt="Black Professionals United"
                            className="h-12 w-auto mx-auto mb-2"
                        />
                        <p className="text-xs text-text-3 font-medium uppercase tracking-widest">Member Portal</p>
                    </div>

                    {success ? (
                        <div className="space-y-6 text-center">
                            <div className="text-5xl">✅</div>
                            <div>
                                <h1 className="text-2xl font-bold text-text">Password updated!</h1>
                                <p className="text-sm text-text-2 mt-2">
                                    Your password has been changed. Redirecting you to sign in…
                                </p>
                            </div>
                            <a href="/login" className="btn btn-amber btn-lg w-full justify-center block">
                                Sign in now →
                            </a>
                        </div>
                    ) : (
                        <>
                            <div className="mb-8">
                                <h1 className="text-2xl font-bold text-text">Choose a new password</h1>
                                <p className="text-sm text-text-2 mt-1">Must be at least 8 characters.</p>
                            </div>

                            {error && (
                                <div className="alert alert-red text-sm mb-5">
                                    {error}
                                    {error.includes('expired') || error.includes('Invalid') ? (
                                        <span>
                                            {' '}
                                            <a href="/forgot-password" className="font-semibold underline">
                                                Request a new link
                                            </a>
                                        </span>
                                    ) : null}
                                </div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div>
                                    <label htmlFor="password" className="field-label">New password</label>
                                    <div className="relative">
                                        <input
                                            id="password"
                                            type={showPass ? 'text' : 'password'}
                                            autoComplete="new-password"
                                            className="field-input w-full pr-12"
                                            placeholder="••••••••"
                                            value={password}
                                            onChange={e => setPassword(e.target.value)}
                                            required disabled={loading || !token}
                                        />
                                        <button
                                            type="button"
                                            onClick={() => setShowPass(v => !v)}
                                            className="absolute right-3 top-1/2 -translate-y-1/2 text-text-3 hover:text-text text-sm"
                                            tabIndex={-1}
                                        >
                                            {showPass ? 'Hide' : 'Show'}
                                        </button>
                                    </div>
                                    {strength && (
                                        <p className="text-xs mt-1" style={{ color: strength.color }}>
                                            {strength.label}
                                        </p>
                                    )}
                                </div>

                                <div>
                                    <label htmlFor="confirm" className="field-label">Confirm new password</label>
                                    <input
                                        id="confirm"
                                        type={showPass ? 'text' : 'password'}
                                        autoComplete="new-password"
                                        className="field-input w-full"
                                        placeholder="••••••••"
                                        value={confirm}
                                        onChange={e => setConfirm(e.target.value)}
                                        required disabled={loading || !token}
                                    />
                                    {confirm && password !== confirm && (
                                        <p className="text-xs mt-1" style={{ color: 'var(--err)' }}>Passwords do not match</p>
                                    )}
                                </div>

                                <button
                                    type="submit"
                                    className="btn btn-amber btn-lg w-full justify-center mt-2"
                                    disabled={loading || !token}
                                >
                                    {loading ? 'Updating…' : 'Set new password →'}
                                </button>
                            </form>

                            <p className="mt-6 text-center text-sm text-text-2">
                                <a href="/login" className="font-semibold text-brand hover:underline">
                                    Back to sign in
                                </a>
                            </p>
                        </>
                    )}
                </div>
            </div>

        </main>
    );
}

export default function ResetPasswordPage() {
    return (
        <Suspense fallback={
            <main className="min-h-screen flex items-center justify-center bg-bg">
                <div className="text-sm text-text-2">Loading…</div>
            </main>
        }>
            <ResetPasswordForm />
        </Suspense>
    );
}
