import { NextRequest, NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';
const CF7_FORM_ID = 58834;

export async function POST(request: NextRequest) {
    try {
        const body = await request.json();

        const form = new FormData();
        form.append('_wpcf7', String(CF7_FORM_ID));
        form.append('_wpcf7_version', '6.2.4.1');
        form.append('_wpcf7_locale', 'en_GB');
        form.append('_wpcf7_unit_tag', `wpcf7-f${CF7_FORM_ID}-o1`);
        form.append('_wpcf7_container_post', '0');

        for (const [key, value] of Object.entries(body)) {
            form.append(key, String(value ?? ''));
        }

        const res = await fetch(
            `${WP}/wp-json/contact-form-7/v1/contact-forms/${CF7_FORM_ID}/feedback`,
            { method: 'POST', body: form }
        );

        const data = await res.json().catch(() => ({}));
        return NextResponse.json(data, { status: res.ok ? 200 : res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
