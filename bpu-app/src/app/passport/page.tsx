import PassportHubClient from './PassportHubClient';

export const metadata = {
    title: 'BPU Digital Passport | Black Professionals UK',
    description: 'A verified snapshot of professional credentials for Black professionals across the UK.',
};

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function PassportHubPage() {
    let initialMembers = [];
    try {
        const res = await fetch(
            `${WP_URL}/wp-json/bpu/v1/members?per_page=12&page=1`,
            { next: { revalidate: 60 } },
        );
        if (res.ok) initialMembers = await res.json();
    } catch { /* render without initial data if WP is unreachable */ }

    return <PassportHubClient initialMembers={initialMembers} />;
}
