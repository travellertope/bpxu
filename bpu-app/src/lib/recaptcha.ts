const RECAPTCHA_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
const MIN_SCORE = 0.5;

export type RecaptchaFailure =
    | 'missing-token'   // client sent nothing — script blocked, or site key not configured
    | 'misconfigured'   // secret set but no site key, or Google says our secret is bad
    | 'low-score'       // valid token, but scored below the threshold
    | 'rejected'        // Google rejected the token (expired, duplicate, wrong domain)
    | 'network';        // could not reach Google

export type RecaptchaResult = { ok: true } | { ok: false; reason: RecaptchaFailure };

/** Error codes that mean "our configuration is wrong", not "this user is a bot". */
const CONFIG_ERROR_CODES = new Set([
    'invalid-input-secret',
    'missing-input-secret',
    'bad-request',
]);

/**
 * Verifies a reCAPTCHA v3 token server-side.
 *
 * Passes when RECAPTCHA_SECRET_KEY is not configured, so development and test
 * environments work without keys. Failures are logged with Google's own
 * error-codes — without them a domain mismatch, an expired token and a bot all
 * look identical from the outside, which is what made this hard to diagnose.
 */
export async function verifyRecaptcha(token: string | undefined): Promise<RecaptchaResult> {
    const secret = process.env.RECAPTCHA_SECRET_KEY;
    if (!secret) return { ok: true }; // Skip verification when key is not configured

    if (!token) {
        // NEXT_PUBLIC_* is readable server-side too. A secret with no site key
        // means the browser can never mint a token, so every submit fails —
        // a deployment problem, not a user problem. Call it out explicitly.
        if (!process.env.NEXT_PUBLIC_RECAPTCHA_SITE_KEY) {
            console.error(
                '[recaptcha] RECAPTCHA_SECRET_KEY is set but NEXT_PUBLIC_RECAPTCHA_SITE_KEY is not. ' +
                'The browser cannot produce a token, so every login/register/reset will fail. ' +
                'Set the site key (and rebuild, it is inlined at build time) or unset the secret.',
            );
            return { ok: false, reason: 'misconfigured' };
        }
        console.error('[recaptcha] no token submitted — script likely blocked or still loading client-side');
        return { ok: false, reason: 'missing-token' };
    }

    let data: { success: boolean; score?: number; 'error-codes'?: string[] };
    try {
        const res = await fetch(RECAPTCHA_VERIFY_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `secret=${encodeURIComponent(secret)}&response=${encodeURIComponent(token)}`,
        });
        data = await res.json();
    } catch (err) {
        console.error('[recaptcha] could not reach the siteverify endpoint:', err);
        return { ok: false, reason: 'network' };
    }

    if (!data.success) {
        const codes = data['error-codes'] ?? [];
        console.error('[recaptcha] siteverify rejected the token. error-codes:', codes.join(', ') || '(none)');
        if (codes.some(c => CONFIG_ERROR_CODES.has(c))) {
            return { ok: false, reason: 'misconfigured' };
        }
        return { ok: false, reason: 'rejected' };
    }

    const score = data.score ?? 1;
    if (score < MIN_SCORE) {
        console.error(`[recaptcha] score ${score} is below the ${MIN_SCORE} threshold`);
        return { ok: false, reason: 'low-score' };
    }

    return { ok: true };
}

/** User-facing copy for a verification failure. Keeps config details in the logs. */
export function recaptchaErrorMessage(reason: RecaptchaFailure): string {
    switch (reason) {
        case 'missing-token':
            return 'Could not verify you are human. If you use an ad or tracker blocker, allow google.com/recaptcha for this site, then try again.';
        case 'misconfigured':
            return 'Sign-in is temporarily unavailable due to a server configuration problem. Please contact support.';
        case 'network':
            return 'Could not reach reCAPTCHA. Please check your connection and try again.';
        case 'low-score':
        case 'rejected':
        default:
            return 'reCAPTCHA verification failed. Please try again.';
    }
}
