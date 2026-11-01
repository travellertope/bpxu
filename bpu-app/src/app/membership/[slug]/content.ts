export interface Step {
    title: string;
    description: string;
}

export interface Feature {
    title: string;
    description: string;
}

export interface PageContent {
    title: string;
    badge: string;
    description: string;
    steps: Step[];
    features: Feature[];
    cta: {
        label: string;
        href: string;
        requiresAuth: boolean;
    };
    /** Shown to guests instead of the primary CTA */
    guestCta?: {
        label: string;
        href: string;
    };
}

export const PAGES: Record<string, PageContent> = {
    'work-placement': {
        title: 'Work Placements',
        badge: 'Black Interns & Talents Programme',
        description:
            'Combining the best of the Black Interns Programme and the Black Talents Programme to create a transformative placement experience that shapes the trajectory of your professional future.',
        steps: [
            {
                title: 'Apply',
                description: 'Submit your application with your CV, areas of interest, and career goals. Our team reviews every application personally.',
            },
            {
                title: 'Get Matched',
                description: 'We match you with partner organisations aligned with your skills, ambitions, and industry preferences.',
            },
            {
                title: 'Complete Your Placement',
                description: 'Gain real-world experience, expand your professional network, and build the foundation for your career.',
            },
        ],
        features: [
            {
                title: 'Paid Placements',
                description: 'All placements through BPU are paid opportunities at partner organisations committed to inclusion.',
            },
            {
                title: 'Structured Support',
                description: 'Dedicated BPU support throughout your placement — from preparation to completion.',
            },
            {
                title: 'Partner Network',
                description: 'Access to a curated network of DEI-committed employers across finance, law, tech, and more.',
            },
            {
                title: 'Career Progression',
                description: 'Many of our placement alumni have secured permanent roles at their host organisations.',
            },
        ],
        cta: {
            label: 'Apply for a Placement',
            href: '/(member)/work-placement',
            requiresAuth: true,
        },
        guestCta: {
            label: 'Sign in to Apply',
            href: '/login?returnTo=/membership/work-placement',
        },
    },

    'interview-prep': {
        title: 'Interview Prep',
        badge: 'Personalised Coaching',
        description:
            'Be equipped with the tools and knowledge to excel in interviews. Our members receive a personalised coaching approach tailored to their specific role, industry, and interview stage.',
        steps: [
            {
                title: 'Submit Your Request',
                description: 'Tell us about your upcoming interview — the organisation, role, interview type, and any specific areas where you need support.',
            },
            {
                title: 'Get Matched with a Coach',
                description: 'Our team pairs you with a coach who has direct experience in your industry and with your interview format.',
            },
            {
                title: 'Complete Your Session',
                description: 'Work through tailored mock scenarios, answer frameworks, and feedback so you walk into the room ready.',
            },
        ],
        features: [
            {
                title: 'Industry-Specific Coaching',
                description: 'Coaches with real experience in your sector — legal, finance, technology, consulting, and more.',
            },
            {
                title: 'All Interview Formats',
                description: 'Competency-based, technical, case study, assessment centre, and partner-level interviews covered.',
            },
            {
                title: 'Mock Scenarios',
                description: 'Practise with real questions from our network of hiring organisations before you sit the actual interview.',
            },
            {
                title: 'Structured Feedback',
                description: 'Written feedback after every session highlighting strengths, gaps, and specific improvements.',
            },
        ],
        cta: {
            label: 'Book a Session',
            href: '/(member)/interview-prep',
            requiresAuth: true,
        },
        guestCta: {
            label: 'Sign in to Book',
            href: '/login?returnTo=/membership/interview-prep',
        },
    },

    'cv-clinic': {
        title: 'CV Clinic',
        badge: 'AI-Powered + Expert Review',
        description:
            'Your CV is the most critical tool in your job search. Get AI-powered instant analysis combined with personalised expert feedback to create a standout CV that lands interviews.',
        steps: [
            {
                title: 'Upload Your CV',
                description: 'Upload your current CV in PDF or Word format. Our AI analyses it against thousands of successful applications in seconds.',
            },
            {
                title: 'Receive Instant Analysis',
                description: 'Get an immediate breakdown of structure, keyword optimisation, impact statements, and areas to improve.',
            },
            {
                title: 'Request Expert Review',
                description: 'Optionally request a human review from our careers professionals for deeper, personalised feedback.',
            },
            {
                title: 'Implement & Resubmit',
                description: 'Apply the feedback, reupload your revised CV, and track your improvement score over time.',
            },
        ],
        features: [
            {
                title: 'Instant AI Analysis',
                description: 'Immediate feedback on structure, ATS optimisation, keyword density, and impact language.',
            },
            {
                title: 'Human Expert Review',
                description: 'Professional careers advisors who understand the unique barriers faced by Black professionals.',
            },
            {
                title: 'Version History',
                description: 'Track every iteration of your CV and see how your score improves with each revision.',
            },
            {
                title: 'Role-Specific Tips',
                description: 'Tailored guidance based on your target industry and seniority level.',
            },
        ],
        cta: {
            label: 'Open CV Clinic',
            href: '/(member)/cv-clinic',
            requiresAuth: true,
        },
        guestCta: {
            label: 'Sign in to Get Started',
            href: '/login?returnTo=/cv-clinic',
        },
    },

    'mentorship': {
        title: 'Mentorship Programme',
        badge: 'PAIRED Platform',
        description:
            'Connect with experienced Black professionals who are eager to share their insights, open doors, and actively support your growth. Our PAIRED platform makes meaningful mentorship accessible.',
        steps: [
            {
                title: 'Build Your Profile',
                description: 'Tell us about your career stage, goals, industry, and what you are looking to gain from a mentorship relationship.',
            },
            {
                title: 'Browse & Connect',
                description: 'Explore our community of mentors matched to your profile. Read their stories, experience, and areas of expertise.',
            },
            {
                title: 'Book Your Sessions',
                description: 'Schedule sessions directly through the platform. Build a relationship that goes beyond a single conversation.',
            },
        ],
        features: [
            {
                title: 'Curated Mentor Network',
                description: 'Senior professionals across finance, law, technology, creative industries, and the public sector.',
            },
            {
                title: 'Smart Matching',
                description: 'Our algorithm surfaces the mentors most relevant to your goals, industry, and career stage.',
            },
            {
                title: 'Flexible Scheduling',
                description: 'Book sessions at times that work for both of you. Meet virtually or in person — your choice.',
            },
            {
                title: 'Long-Term Relationships',
                description: 'PAIRED is built for ongoing mentorship, not one-off advice calls. Grow with your mentor over time.',
            },
        ],
        cta: {
            label: 'Find a Mentor',
            href: '/paired',
            requiresAuth: true,
        },
        guestCta: {
            label: 'Sign in to Browse Mentors',
            href: '/login?returnTo=/paired',
        },
    },

    'support': {
        title: 'Member Support',
        badge: 'BPU Support Team',
        description:
            'Direct access to the BPU team for any question about your membership, our services, or your career journey. We are here to make sure you get the most out of your membership.',
        steps: [
            {
                title: 'Browse the FAQ',
                description: 'Find instant answers to the most common questions about membership, services, and the platform.',
            },
            {
                title: 'Submit a Ticket',
                description: 'Raise a support request directly to the BPU team. Select a category and describe your issue in detail.',
            },
            {
                title: 'We Respond Within 48 Hours',
                description: 'Our team reviews every ticket personally and responds with clear, actionable guidance.',
            },
        ],
        features: [
            {
                title: 'Membership Queries',
                description: 'Account access, upgrade options, payment questions, and free tier eligibility — all handled here.',
            },
            {
                title: 'Career Services Support',
                description: 'Help navigating CV Clinic, Interview Prep, Job Board, and our other career tools.',
            },
            {
                title: 'Events & Bookings',
                description: 'Questions about upcoming events, waitlists, registration issues, and past event recaps.',
            },
            {
                title: 'Technical Issues',
                description: 'Platform bugs, login problems, or anything that is not working as expected — report it here.',
            },
        ],
        cta: {
            label: 'Get Support',
            href: '/(member)/support',
            requiresAuth: true,
        },
        guestCta: {
            label: 'Sign in to Raise a Ticket',
            href: '/login?returnTo=/membership/support',
        },
    },

    'ambassadorship': {
        title: 'Ambassadorship',
        badge: 'Community Leadership',
        description:
            'A special initiative for members who are passionate about empowering others and actively contributing to the growth of our community. As BPU scales, Ambassadors are the heartbeat that keeps it personal.',
        steps: [
            {
                title: 'Apply',
                description: 'Submit a short application sharing your motivation, your connection to the BPU community, and how you would represent it.',
            },
            {
                title: 'Interview',
                description: 'A short conversation with the BPU team to learn more about you, your goals, and how we can support your Ambassador journey.',
            },
            {
                title: 'Begin Your Role',
                description: 'Get onboarded, meet your fellow Ambassadors, and start making a direct impact on the community around you.',
            },
        ],
        features: [
            {
                title: 'Community Leadership',
                description: 'Be the first point of contact for new members in your region or industry, welcoming them and connecting them to the right resources.',
            },
            {
                title: 'Direct Platform Input',
                description: 'Ambassadors have a direct line to the BPU team and contribute to decisions about platform direction and new initiatives.',
            },
            {
                title: 'Exclusive Benefits',
                description: 'Early access to new features, priority event invitations, and recognition across our platforms.',
            },
            {
                title: 'Professional Development',
                description: 'The Ambassador role itself is a career-building experience — build leadership, communication, and community management skills.',
            },
        ],
        cta: {
            label: 'Apply to Become an Ambassador',
            href: '/(member)/ambassadorship',
            requiresAuth: true,
        },
        guestCta: {
            label: 'Sign in to Apply',
            href: '/login?returnTo=/membership/ambassadorship',
        },
    },
};
