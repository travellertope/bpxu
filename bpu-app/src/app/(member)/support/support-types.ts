export type TicketStatus = 'open' | 'in_progress' | 'resolved' | 'closed';
export type TicketUrgency = 'normal' | 'high';

export interface TicketMessage {
    id: number;
    author: string;
    author_type: 'member' | 'staff';
    content: string;
    created_at: string;
}

export interface SupportTicket {
    id: number;
    ref: string;
    category: string;
    subject: string;
    urgency: TicketUrgency;
    status: TicketStatus;
    created_at: string;
    updated_at: string;
    message_count: number;
}

export interface SupportTicketDetail extends SupportTicket {
    messages: TicketMessage[];
}
