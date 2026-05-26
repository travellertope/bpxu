const RECAPTCHA_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
const MIN_SCORE = 0.5;

/**
 * Verifies a reCAPTCHA v3 token server-side.
 * Returns true when the score meets the threshold, or when RECAPTCHA_SECRET_KEY
 * is not configured (development / test environments).
 */
export async function verifyRecaptcha(token: string | undefined): Promise<boolean> {
    const secret = process.env.RECAPTCHA_SECRET_KEY;
    if (!secret) return true; // Skip verification when key is not configured

    if (!token) return false;

    try {
        const res = await fetch(RECAPTCHA_VERIFY_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `secret=${encodeURIComponent(secret)}&response=${encodeURIComponent(token)}`,
        });

        const data = await res.json() as { success: boolean; score?: number };
        return data.success && (data.score ?? 1) >= MIN_SCORE;
    } catch {
        return false;
    }
}
