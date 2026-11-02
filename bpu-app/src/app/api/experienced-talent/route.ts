import { NextRequest, NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';
const CF7_FORM_ID = '39266';

export async function POST(request: NextRequest) {
    try {
        const formData = await request.formData();
        const res = await fetch(
            `${WP}/wp-json/contact-form-7/v1/contact-forms/${CF7_FORM_ID}/feedback`,
            {
                method: 'POST',
                body: formData,
            },
        );
        const data = await res.json().catch(() => ({}));
        if (data.status === 'mail_sent' || data.status === 'mail_failed') {
            return NextResponse.json(data, { status: 200 });
        }
        return NextResponse.json(data, { status: res.ok ? 200 : res.status });
    } catch {
        return NextResponse.json({ status: 'error', message: 'Internal server error' }, { status: 500 });
    }
}
