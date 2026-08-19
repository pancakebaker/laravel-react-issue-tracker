import { TicketBadge } from '@/components/tickets/ticket-badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { formatDateOnly, formatDateTime } from '@/lib/ticket-format';
import { type BreadcrumbItem } from '@/types';
import { type EnumOption, type PaginatedTickets, type TicketFilters, type TicketSummary } from '@/types/tickets';
import { Head, Link, router } from '@inertiajs/react';
import { Eye, Pencil, Plus, X } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: '/tickets',
    },
];

interface TicketIndexProps {
    tickets: PaginatedTickets;
    filters: TicketFilters;
    statuses: EnumOption[];
    priorities: EnumOption[];
    summary: TicketSummary;
}

export default function TicketIndex({ tickets, filters, statuses, priorities, summary }: TicketIndexProps) {
    const hasFilters = Boolean(filters.status || filters.priority);

    const updateFilter = (key: keyof TicketFilters, value: string) => {
        const nextFilters = {
            status: filters.status ?? undefined,
            priority: filters.priority ?? undefined,
            [key]: value || undefined,
        };

        router.get(route('tickets.index'), nextFilters, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tickets" />

            <main className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">Tickets</h1>
                        <p className="text-muted-foreground mt-1 text-sm">Track support requests, bugs, and feature follow-up.</p>
                    </div>

                    <Button asChild>
                        <Link href={route('tickets.create')}>
                            <Plus />
                            New ticket
                        </Link>
                    </Button>
                </div>

                <section className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ticket summary">
                    <SummaryLink
                        label="All"
                        count={summary.all}
                        href={route('tickets.index', summaryQuery(filters, null))}
                        active={!filters.status}
                    />
                    {statuses.map((status) => (
                        <SummaryLink
                            key={status.value}
                            label={status.label}
                            count={summary[status.value as keyof TicketSummary]}
                            href={route('tickets.index', summaryQuery(filters, status.value))}
                            active={filters.status === status.value}
                        />
                    ))}
                </section>

                <section className="flex flex-col gap-3 border-y py-4 lg:flex-row lg:items-end lg:justify-between">
                    <div className="grid gap-3 sm:grid-cols-2 lg:w-[32rem]">
                        <FilterSelect
                            label="Status"
                            value={filters.status ?? ''}
                            options={statuses}
                            onChange={(value) => updateFilter('status', value)}
                        />
                        <FilterSelect
                            label="Priority"
                            value={filters.priority ?? ''}
                            options={priorities}
                            onChange={(value) => updateFilter('priority', value)}
                        />
                    </div>

                    {hasFilters && (
                        <Button variant="outline" asChild>
                            <Link href={route('tickets.index')}>
                                <X />
                                Clear filters
                            </Link>
                        </Button>
                    )}
                </section>

                <section className="overflow-hidden border">
                    {tickets.data.length > 0 ? (
                        <>
                            <div className="hidden overflow-x-auto lg:block">
                                <table className="w-full text-sm">
                                    <thead className="bg-muted/50 text-muted-foreground">
                                        <tr className="text-left">
                                            <th className="px-4 py-3 font-medium">Title</th>
                                            <th className="px-4 py-3 font-medium">Status</th>
                                            <th className="px-4 py-3 font-medium">Priority</th>
                                            <th className="px-4 py-3 font-medium">Category</th>
                                            <th className="px-4 py-3 font-medium">Assigned</th>
                                            <th className="px-4 py-3 font-medium">Due</th>
                                            <th className="px-4 py-3 font-medium">Created</th>
                                            <th className="px-4 py-3 text-right font-medium">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y">
                                        {tickets.data.map((ticket) => (
                                            <tr key={ticket.id}>
                                                <td className="max-w-[22rem] px-4 py-3 font-medium">
                                                    <Link href={route('tickets.show', ticket.id)} className="hover:underline">
                                                        {ticket.title}
                                                    </Link>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <TicketBadge kind="status" value={ticket.status} />
                                                </td>
                                                <td className="px-4 py-3">
                                                    <TicketBadge kind="priority" value={ticket.priority} />
                                                </td>
                                                <td className="px-4 py-3">{ticket.category.label}</td>
                                                <td className="px-4 py-3">{ticket.assigned_to ?? 'Unassigned'}</td>
                                                <td className="px-4 py-3">{formatDateOnly(ticket.due_date)}</td>
                                                <td className="px-4 py-3">{formatDateTime(ticket.created_at)}</td>
                                                <td className="px-4 py-3">
                                                    <div className="flex justify-end gap-2">
                                                        <Button variant="ghost" size="sm" asChild>
                                                            <Link href={route('tickets.show', ticket.id)}>
                                                                <Eye />
                                                                View
                                                            </Link>
                                                        </Button>
                                                        <Button variant="ghost" size="sm" asChild>
                                                            <Link href={route('tickets.edit', ticket.id)}>
                                                                <Pencil />
                                                                Edit
                                                            </Link>
                                                        </Button>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            <div className="divide-y lg:hidden">
                                {tickets.data.map((ticket) => (
                                    <article key={ticket.id} className="space-y-4 p-4">
                                        <div className="space-y-2">
                                            <Link href={route('tickets.show', ticket.id)} className="font-medium hover:underline">
                                                {ticket.title}
                                            </Link>
                                            <div className="flex flex-wrap gap-2">
                                                <TicketBadge kind="status" value={ticket.status} />
                                                <TicketBadge kind="priority" value={ticket.priority} />
                                            </div>
                                        </div>
                                        <dl className="grid gap-2 text-sm sm:grid-cols-2">
                                            <MetaItem label="Category" value={ticket.category.label} />
                                            <MetaItem label="Assigned" value={ticket.assigned_to ?? 'Unassigned'} />
                                            <MetaItem label="Due" value={formatDateOnly(ticket.due_date)} />
                                            <MetaItem label="Created" value={formatDateTime(ticket.created_at)} />
                                        </dl>
                                        <div className="flex gap-2">
                                            <Button variant="outline" size="sm" asChild>
                                                <Link href={route('tickets.show', ticket.id)}>View</Link>
                                            </Button>
                                            <Button variant="outline" size="sm" asChild>
                                                <Link href={route('tickets.edit', ticket.id)}>Edit</Link>
                                            </Button>
                                        </div>
                                    </article>
                                ))}
                            </div>
                        </>
                    ) : (
                        <div className="p-8 text-center">
                            <h2 className="text-lg font-medium">No tickets found</h2>
                            <p className="text-muted-foreground mt-1 text-sm">
                                {hasFilters
                                    ? 'Try clearing filters or choosing different criteria.'
                                    : 'Create the first ticket to start tracking support work.'}
                            </p>
                        </div>
                    )}
                </section>

                <Pagination tickets={tickets} />
            </main>
        </AppLayout>
    );
}

interface FilterSelectProps {
    label: string;
    value: string;
    options: EnumOption[];
    onChange: (value: string) => void;
}

function FilterSelect({ label, value, options, onChange }: FilterSelectProps) {
    return (
        <label className="grid gap-2 text-sm font-medium">
            <span>{label}</span>
            <select
                value={value}
                onChange={(event) => onChange(event.target.value)}
                className="border-input bg-background ring-offset-background focus:ring-ring flex h-10 w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-offset-2 focus:outline-none"
            >
                <option value="">Any {label.toLowerCase()}</option>
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </label>
    );
}

interface SummaryLinkProps {
    label: string;
    count: number;
    href: string;
    active: boolean;
}

function SummaryLink({ label, count, href, active }: SummaryLinkProps) {
    return (
        <Link
            href={href}
            className={
                active
                    ? 'border-primary bg-muted focus-visible:ring-ring border p-4 focus-visible:ring-2 focus-visible:outline-none'
                    : 'focus-visible:ring-ring hover:bg-muted/50 border p-4 focus-visible:ring-2 focus-visible:outline-none'
            }
        >
            <div className="text-muted-foreground text-sm">{label}</div>
            <div className="mt-1 text-2xl font-semibold">{count}</div>
        </Link>
    );
}

function Pagination({ tickets }: { tickets: PaginatedTickets }) {
    if (tickets.last_page <= 1) {
        return null;
    }

    return (
        <nav className="flex flex-wrap items-center justify-between gap-3" aria-label="Ticket pagination">
            <p className="text-muted-foreground text-sm">
                Showing {tickets.from} to {tickets.to} of {tickets.total}
            </p>
            <div className="flex flex-wrap gap-2">
                {tickets.links.map((link, index) =>
                    link.url ? (
                        <Button key={`${link.label}-${index}`} variant={link.active ? 'default' : 'outline'} size="sm" asChild>
                            <Link href={link.url} preserveScroll preserveState>
                                {paginationLabel(link.label)}
                            </Link>
                        </Button>
                    ) : (
                        <Button key={`${link.label}-${index}`} variant="outline" size="sm" disabled>
                            {paginationLabel(link.label)}
                        </Button>
                    ),
                )}
            </div>
        </nav>
    );
}

function paginationLabel(label: string): string {
    return label.replace('&laquo;', '‹').replace('&raquo;', '›');
}

function summaryQuery(filters: TicketFilters, status: string | null): Record<string, string> {
    return {
        ...(status ? { status } : {}),
        ...(filters.priority ? { priority: filters.priority } : {}),
    };
}

function MetaItem({ label, value }: { label: string; value: string }) {
    return (
        <div>
            <dt className="text-muted-foreground">{label}</dt>
            <dd>{value}</dd>
        </div>
    );
}
