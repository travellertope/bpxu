'use client';

import { Suspense, useEffect, useState } from 'react';
import { useSearchParams } from 'next/navigation';
import {
    getRecaptchaToken,
    loadRecaptchaScript,
    RecaptchaUnavailableError,
    RECAPTCHA_BLOCKED_MESSAGE,
} from '@/lib/recaptcha-client';

function ForgotPasswordForm() {
    const searchParams = useSearchParams();
    const returnTo = searchParams.get('returnTo') || '/login';

    const [mode, setMode]       = useState<'email' | 'sms'>('email');
    const [email, setEmail]     = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError]     = useState('');
    const [sent, setSent]       = useState(false);

    // SMS OTP flow
    const [smsStep, setSmsStep]           = useState<'request' | 'verify' | 'done'>('request');
    const [code, setCode]                 = useState('');
    const [smsPassword, setSmsPassword]   = useState('');
    const [smsConfirm, setSmsConfirm]     = useState('');

    useEffect(() => { loadRecaptchaScript(); }, []);

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        setLoading(true);
        try {
            let recaptcha_token: string;
            try {
                recaptcha_token = await getRecaptchaToken('forgot_password');
            } catch (err) {
                if (err instanceof RecaptchaUnavailableError) {
                    setError(RECAPTCHA_BLOCKED_MESSAGE);
                    setLoading(false);
                    return;
                }
                throw err;
            }
            const res = await fetch('/api/auth/forgot-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, recaptcha_token }),
            });
            const data = await res.json();
            if (!res.ok) {
                setError(data.error || 'Something went wrong.');
                setLoading(false);
                return;
            }
            setSent(true);
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setLoading(false);
        }
    }

    async function handleSendCode(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        setLoading(true);
        try {
            let recaptcha_token: string;
            try {
                recaptcha_token = await getRecaptchaToken('forgot_password_sms');
            } catch (err) {
                if (err instanceof RecaptchaUnavailableError) {
                    setError(RECAPTCHA_BLOCKED_MESSAGE);
                    setLoading(false);
                    return;
                }
                throw err;
            }
            const res = await fetch('/api/auth/forgot-password-sms', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, recaptcha_token }),
            });
            const data = await res.json();
            if (!res.ok) {
                setError(data.error || 'Something went wrong.');
                setLoading(false);
                return;
            }
            setSmsStep('verify');
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setLoading(false);
        }
    }

    async function handleVerifyCode(e: React.FormEvent) {
        e.preventDefault();
        setError('');

        if (smsPassword.length < 8) {
            setError('Password must be at least 8 characters.');
            return;
        }
        if (smsPassword !== smsConfirm) {
            setError('Passwords do not match.');
            return;
        }

        setLoading(true);
        try {
            const res = await fetch('/api/auth/verify-reset-code', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, code, password: smsPassword }),
            });
            const data = await res.json();
            if (!res.ok) {
                setError(data.error || 'Invalid or expired code.');
                setLoading(false);
                return;
            }
            setSmsStep('done');
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setLoading(false);
        }
    }

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
                            Forgotten<br />your password?
                        </h2>
                        <p className="text-white/70 text-lg leading-relaxed max-w-xs">
                            No worries — enter your email and we&apos;ll send you a secure reset link.
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

                    {mode === 'email' && sent ? (
                        <div className="space-y-6 text-center">
                            <div className="text-5xl">📬</div>
                            <div>
                                <h1 className="text-2xl font-bold text-text">Check your inbox</h1>
                                <p className="text-sm text-text-2 mt-2">
                                    If an account exists for <strong>{email}</strong>, we&apos;ve sent a password reset link. It expires in 1 hour.
                                </p>
                            </div>
                            <p className="text-sm text-text-3">
                                Didn&apos;t get it? Check your spam folder, or{' '}
                                <button
                                    onClick={() => { setSent(false); setEmail(''); }}
                                    className="font-semibold text-brand hover:underline"
                                >
                                    try again
                                </button>.
                            </p>
                            <a href={returnTo} className="btn btn-outline btn-lg w-full justify-center block">
                                Back to sign in
                            </a>
                        </div>
                    ) : mode === 'email' ? (
                        <>
                            <div className="mb-8">
                                <h1 className="text-2xl font-bold text-text">Reset your password</h1>
                                <p className="text-sm text-text-2 mt-1">
                                    Enter your email and we&apos;ll send you a reset link.
                                </p>
                            </div>

                            {error && (
                                <div className="alert alert-red text-sm mb-5">{error}</div>
                            )}

                            <form onSubmit={handleSubmit} className="space-y-4">
                                <div>
                                    <label htmlFor="email" className="field-label">Email address</label>
                                    <input
                                        id="email" type="email" autoComplete="email"
                                        className="field-input w-full"
                                        placeholder="you@example.com"
                                        value={email}
                                        onChange={e => setEmail(e.target.value)}
                                        required disabled={loading}
                                    />
                                </div>

                                <button
                                    type="submit"
                                    className="btn btn-amber btn-lg w-full justify-center mt-2"
                                    disabled={loading}
                                >
                                    {loading ? 'Sending…' : 'Send reset link →'}
                                </button>
                            </form>

                            <p className="mt-6 text-center text-sm text-text-2">
                                Remembered it?{' '}
                                <a href={returnTo} className="font-semibold text-brand hover:underline">
                                    Back to sign in
                                </a>
                            </p>

                            <p className="mt-3 text-center text-sm text-text-2">
                                <button
                                    type="button"
                                    onClick={() => { setError(''); setMode('sms'); }}
                                    className="font-semibold text-brand hover:underline"
                                >
                                    Text me a code instead →
                                </button>
                            </p>
                        </>
                    ) : smsStep === 'done' ? (
                        <div className="space-y-6 text-center">
                            <div className="text-5xl">✅</div>
                            <div>
                                <h1 className="text-2xl font-bold text-text">Password updated!</h1>
                                <p className="text-sm text-text-2 mt-2">
                                    Your password has been changed. You can now sign in.
                                </p>
                            </div>
                            <a href="/login" className="btn btn-amber btn-lg w-full justify-center block">
                                Sign in now →
                            </a>
                        </div>
                    ) : smsStep === 'request' ? (
                        <>
                            <div className="mb-8">
                                <h1 className="text-2xl font-bold text-text">Reset via text message</h1>
                                <p className="text-sm text-text-2 mt-1">
                                    Enter your email and we&apos;ll text a 6-digit code to the phone number on your account.
                                </p>
                            </div>

                            {error && (
                                <div className="alert alert-red text-sm mb-5">{error}</div>
                            )}

                            <form onSubmit={handleSendCode} className="space-y-4">
                                <div>
                                    <label htmlFor="sms-email" className="field-label">Email address</label>
                                    <input
                                        id="sms-email" type="email" autoComplete="email"
                                        className="field-input w-full"
                                        placeholder="you@example.com"
                                        value={email}
                                        onChange={e => setEmail(e.target.value)}
                                        required disabled={loading}
                                    />
                                </div>

                                <button
                                    type="submit"
                                    className="btn btn-amber btn-lg w-full justify-center mt-2"
                                    disabled={loading}
                                >
                                    {loading ? 'Sending…' : 'Send code →'}
                                </button>
                            </form>

                            <p className="mt-6 text-center text-sm text-text-2">
                                <button
                                    type="button"
                                    onClick={() => { setError(''); setMode('email'); }}
                                    className="font-semibold text-brand hover:underline"
                                >
                                    Use email reset link instead
                                </button>
                            </p>
                        </>
                    ) : (
                        <>
                            <div className="mb-8">
                                <h1 className="text-2xl font-bold text-text">Enter your code</h1>
                                <p className="text-sm text-text-2 mt-1">
                                    Enter the 6-digit code we texted you, and choose a new password.
                                </p>
                            </div>

                            {error && (
                                <div className="alert alert-red text-sm mb-5">{error}</div>
                            )}

                            <form onSubmit={handleVerifyCode} className="space-y-4">
                                <div>
                                    <label htmlFor="code" className="field-label">Verification code</label>
                                    <input
                                        id="code" type="text" inputMode="numeric" autoComplete="one-time-code"
                                        maxLength={6}
                                        className="field-input w-full tracking-widest text-center"
                                        placeholder="000000"
                                        value={code}
                                        onChange={e => setCode(e.target.value.replace(/\D/g, '').slice(0, 6))}
                                        required disabled={loading}
                                    />
                                </div>

                                <div>
                                    <label htmlFor="sms-password" className="field-label">New password</label>
                                    <input
                                        id="sms-password" type="password" autoComplete="new-password"
                                        className="field-input w-full"
                                        placeholder="••••••••"
                                        value={smsPassword}
                                        onChange={e => setSmsPassword(e.target.value)}
                                        required disabled={loading}
                                    />
                                </div>

                                <div>
                                    <label htmlFor="sms-confirm" className="field-label">Confirm new password</label>
                                    <input
                                        id="sms-confirm" type="password" autoComplete="new-password"
                                        className="field-input w-full"
                                        placeholder="••••••••"
                                        value={smsConfirm}
                                        onChange={e => setSmsConfirm(e.target.value)}
                                        required disabled={loading}
                                    />
                                    {smsConfirm && smsPassword !== smsConfirm && (
                                        <p className="text-xs mt-1" style={{ color: 'var(--err)' }}>Passwords do not match</p>
                                    )}
                                </div>

                                <button
                                    type="submit"
                                    className="btn btn-amber btn-lg w-full justify-center mt-2"
                                    disabled={loading}
                                >
                                    {loading ? 'Verifying…' : 'Reset password →'}
                                </button>
                            </form>

                            <p className="mt-6 text-center text-sm text-text-2">
                                Didn&apos;t get a code?{' '}
                                <button
                                    type="button"
                                    onClick={() => { setError(''); setCode(''); setSmsStep('request'); }}
                                    className="font-semibold text-brand hover:underline"
                                >
                                    Try again
                                </button>
                            </p>
                        </>
                    )}
                </div>
            </div>

        </main>
    );
}

export default function ForgotPasswordPage() {
    return (
        <Suspense fallback={
            <main className="min-h-screen flex items-center justify-center bg-bg">
                <div className="text-sm text-text-2">Loading…</div>
            </main>
        }>
            <ForgotPasswordForm />
        </Suspense>
    );
}
