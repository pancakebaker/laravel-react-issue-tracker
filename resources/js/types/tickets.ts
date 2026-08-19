export interface EnumOption {
    value: string;
    label: string;
}

export type TicketEnumValue = EnumOption;

export interface Ticket {
    id: number;
    title: string;
    description: string;
    status: TicketEnumValue;
    priority: TicketEnumValue;
    category: TicketEnumValue;
    assigned_to: string | null;
    due_date: string | null;
    created_at: string | null;
    updated_at: string | null;
}

export interface TicketFilters {
    status: string | null;
    priority: string | null;
}

export interface TicketSummary {
    all: number;
    open: number;
    in_progress: number;
    resolved: number;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedTickets {
    data: Ticket[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}
