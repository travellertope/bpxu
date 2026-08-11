const SITE_KEY = process.env.NEXT_PUBLIC_RECAPTCHA_SITE_KEY || '';
const SCRIPT_ID = 'recaptcha-script';

// How long to wait for google's script to define window.grecaptcha, and then
// for it to mint a token. The old code did neither wait: it read
// window.grecaptcha once and returned '' if it wasn't there yet, so a submit
// that landed before the async script finished loading sent an empty token and
// the server rejected it as a failed verification.
const READY_TIMEOUT_MS = 10_000;
const EXECUTE_TIMEOUT_MS = 10_000;

declare global {
    interface Window {
        grecaptcha?: {
            ready: (cb: () => void) => void;
            execute: (siteKey: string, opts: { action: string }) => Promise<string>;
        };
    }
}

/** Thrown when reCAPTCHA cannot produce a token — blocked, offline, or too slow. */
export class RecaptchaUnavailableError extends Error {
    constructor() {
        super('reCAPTCHA could not be loaded');
        this.name = 'RecaptchaUnavailableError';
    }
}

export const recaptchaConfigured = (): boolean => Boolean(SITE_KEY);

let scriptFailed = false;

/** Injects the reCAPTCHA v3 script once. Safe to call repeatedly. */
export function loadRecaptchaScript(): void {
    if (!SITE_KEY || typeof document === 'undefined') return;
    if (document.getElementById(SCRIPT_ID)) return;

    const script = document.createElement('script');
    script.id = SCRIPT_ID;
    script.src = `https://www.google.com/recaptcha/api.js?render=${SITE_KEY}`;
    script.async = true;
    // Blocked by an extension or unreachable network: fail fast instead of
    // making every submit sit through the full ready timeout.
    script.onerror = () => { scriptFailed = true; };
    document.head.appendChild(script);
}

const sleep = (ms: number) => new Promise(resolve => setTimeout(resolve, ms));

async function waitForGrecaptcha(timeoutMs: number): Promise<boolean> {
    const deadline = Date.now() + timeoutMs;
    while (Date.now() < deadline) {
        if (scriptFailed) return false;
        if (typeof window !== 'undefined' && window.grecaptcha?.execute) return true;
        await sleep(50);
    }
    return false;
}

function withTimeout<T>(promise: Promise<T>, ms: number, fallback: T): Promise<T> {
    return Promise.race([promise, sleep(ms).then(() => fallback)]);
}

/**
 * Mints a reCAPTCHA v3 token, waiting for the script to become available.
 *
 * Returns '' when no site key is configured — the server skips verification in
 * that case too. Throws RecaptchaUnavailableError when reCAPTCHA is configured
 * but unreachable, so the caller can say so instead of surfacing the server's
 * generic "verification failed".
 */
export async function getRecaptchaToken(action: string): Promise<string> {
    if (!SITE_KEY) return '';

    // The form's effect normally injects this already; calling again covers a
    // submit that somehow beat the effect.
    loadRecaptchaScript();

    if (!(await waitForGrecaptcha(READY_TIMEOUT_MS))) {
        throw new RecaptchaUnavailableError();
    }

    const token = await withTimeout(
        new Promise<string>(resolve => {
            window.grecaptcha!.ready(() => {
                window.grecaptcha!.execute(SITE_KEY, { action })
                    .then(resolve)
                    .catch(() => resolve(''));
            });
        }),
        EXECUTE_TIMEOUT_MS,
        '',
    );

    if (!token) throw new RecaptchaUnavailableError();
    return token;
}

/** User-facing copy for a reCAPTCHA that never loaded. */
export const RECAPTCHA_BLOCKED_MESSAGE =
    'Could not load reCAPTCHA. If you use an ad or tracker blocker, allow google.com/recaptcha for this site, then try again.';
