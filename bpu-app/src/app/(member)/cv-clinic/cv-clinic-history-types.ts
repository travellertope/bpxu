import { InterviewQuestion, Answer } from './interview-prep-types';

export interface AnalysisHistoryEntry {
    id: string;
    date: string;
    role: string;
    jd_snippet: string;
    score: number;
    strengths: string[];
    weaknesses: string[];
    recommendation: string;
}

export interface PrepHistoryEntry {
    id: string;
    date: string;
    jd_snippet: string;
    question_count: number;
    answered_count: number;
    questions: InterviewQuestion[];
    answers: Record<string, Answer>;
}

export interface CVClinicHistory {
    analyses: AnalysisHistoryEntry[];
    prep_sessions: PrepHistoryEntry[];
}
