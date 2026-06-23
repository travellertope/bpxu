export type QuestionType = 'star' | 'freetext' | 'rating';

export interface InterviewQuestion {
    question: string;
    type: QuestionType;
    hint: string;
    aim: string;
}

export interface StarAnswer     { situation: string; task: string; action: string; result: string; }
export interface FreetextAnswer { text: string; }
export interface RatingAnswer   { score: number; notes: string; }

// Discriminated union — type + data are always consistent.
export type Answer =
    | { type: 'star';     data: StarAnswer }
    | { type: 'freetext'; data: FreetextAnswer }
    | { type: 'rating';   data: RatingAnswer };

export function emptyAnswer(type: QuestionType): Answer {
    if (type === 'star')   return { type: 'star',     data: { situation: '', task: '', action: '', result: '' } };
    if (type === 'rating') return { type: 'rating',   data: { score: 0, notes: '' } };
    return { type: 'freetext', data: { text: '' } };
}

export function isAnswered(a: Answer): boolean {
    if (a.type === 'star') {
        const { situation, task, action, result } = a.data;
        return !!(situation || task || action || result);
    }
    if (a.type === 'rating') return a.data.score > 0;
    return !!a.data.text.trim();
}

export function formatAnswerForCopy(q: InterviewQuestion, a: Answer): string {
    const lines: string[] = [`Q: ${q.question}`];
    if (a.type === 'star') {
        const { situation, task, action, result } = a.data;
        lines.push(`  Situation: ${situation || '—'}`);
        lines.push(`  Task:      ${task      || '—'}`);
        lines.push(`  Action:    ${action    || '—'}`);
        lines.push(`  Result:    ${result    || '—'}`);
    } else if (a.type === 'rating') {
        lines.push(`  Self-rating: ${a.data.score}/5`);
        if (a.data.notes) lines.push(`  Notes: ${a.data.notes}`);
    } else {
        lines.push(`  ${a.data.text || '—'}`);
    }
    if (q.aim) lines.push(`  [Interviewer focus: ${q.aim}]`);
    return lines.join('\n');
}

export const RATING_LABELS = ['', 'Needs work', 'Developing', 'Competent', 'Confident', 'Excellent'] as const;
