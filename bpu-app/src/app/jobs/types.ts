export interface ScreeningQuestion {
    id: string;
    question: string;
    required: boolean;
}

export interface Employer {
    id: number;
    name: string;
    logo_url: string;
    website: string;
    tagline: string;
    twitter: string;
    video: string;
    description: string;
}

export interface Job {
    id: number;
    title: string;
    company: string;
    location: string;
    employment_type: string;
    industry: string;
    job_type: 'inbound' | 'outbound';
    salary_min?: number;
    salary_max?: number;
    description: string;
    apply_url?: string;
    expires?: string;
    date_posted: string;
    status: 'published' | 'pending';
    remote?: boolean;
    featured?: boolean;
    filled?: boolean;
    impressions?: number;
    clicks?: number;
    applications?: number;
    screening_questions?: ScreeningQuestion[];
    employer?: Employer | null;
}
