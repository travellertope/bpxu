'use client';

import { Suspense, useEffect, useState } from 'react';
import { useSearchParams } from 'next/navigation';

declare global {
    interface Window {
        grecaptcha: {
            ready: (cb: () => void) => void;
            execute: (siteKey: string, opts: { action: string }) => Promise<string>;
        };
    }
}

const SITE_KEY = process.env.NEXT_PUBLIC_RECAPTCHA_SITE_KEY || '';

function ForgotPasswordForm() {
    const searchParams = useSearchParams();
    const returnTo = searchParams.get('returnTo') || '/login';

    const [email, setEmail]     = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError]     = useState('');
    const [sent, setSent]       = useState(false);

    useEffect(() => {
        if (!SITE_KEY || document.getElementById('recaptcha-script')) return;
        const script = document.createElement('script');
        script.id  = 'recaptcha-script';
        script.src = `https://www.google.com/recaptcha/api.js?render=${SITE_KEY}`;
        script.async = true;
        document.head.appendChild(script);
    }, []);

    async function getRecaptchaToken(action: string): Promise<string> {
        if (!SITE_KEY || typeof window === 'undefined' || !window.grecaptcha) return '';
        return new Promise(resolve => {
            window.grecaptcha.ready(() => {
                window.grecaptcha.execute(SITE_KEY, { action }).then(resolve).catch(() => resolve(''));
            });
        });
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        setLoading(true);
        try {
            const recaptcha_token = await getRecaptchaToken('forgot_password');
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

                    {sent ? (
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
                    ) : (
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
