import { TicketBadge } from '@/components/tickets/ticket-badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { formatDateTime } from '@/lib/ticket-format';
import { type BreadcrumbItem } from '@/types';
import { type Ticket } from '@/types/tickets';
import { Head, Link } from '@inertiajs/react';
import { ArrowRight, Plus } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface DashboardSummary {
    total: number;
    open: number;
    in_progress: number;
    resolved: number;
}

type RecentTicket = Pick<Ticket, 'id' | 'title' | 'status' | 'priority' | 'created_at' | 'updated_at'>;

interface DashboardProps {
    summary: DashboardSummary;
    recentTickets: RecentTicket[];
}

export default function Dashboard({ summary, recentTickets }: DashboardProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />

            <main className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">Dashboard</h1>
                        <p className="text-muted-foreground mt-1 text-sm">Review current ticket volume and the newest issue activity.</p>
                    </div>

                    <Button asChild>
                        <Link href={route('tickets.create')}>
                            <Plus />
                            New ticket
                        </Link>
                    </Button>
                </div>

                <section className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ticket summary">
                    <SummaryCard label="Total Tickets" value={summary.total} href={route('tickets.index')} />
                    <SummaryCard label="Open Tickets" value={summary.open} href={route('tickets.index', { status: 'open' })} />
                    <SummaryCard label="In Progress Tickets" value={summary.in_progress} href={route('tickets.index', { status: 'in_progress' })} />
                    <SummaryCard label="Resolved Tickets" value={summary.resolved} href={route('tickets.index', { status: 'resolved' })} />
                </section>

                <section className="overflow-hidden border">
                    <div className="flex flex-col gap-3 border-b p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 className="text-lg font-medium">Recent Tickets</h2>
                            <p className="text-muted-foreground mt-1 text-sm">Latest tickets by created date.</p>
                        </div>
                        <Button variant="outline" size="sm" asChild>
                            <Link href={route('tickets.index')}>
                                View all
                                <ArrowRight />
                            </Link>
                        </Button>
                    </div>

                    {recentTickets.length > 0 ? (
                        <>
                            <div className="hidden overflow-x-auto lg:block">
                                <table className="w-full text-sm">
                                    <thead className="bg-muted/50 text-muted-foreground">
                                        <tr className="text-left">
                                            <th className="px-4 py-3 font-medium">Ticket</th>
                                            <th className="px-4 py-3 font-medium">Status</th>
                                            <th className="px-4 py-3 font-medium">Priority</th>
                                            <th className="px-4 py-3 font-medium">Created</th>
                                            <th className="px-4 py-3 font-medium">Updated</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y">
                                        {recentTickets.map((ticket) => (
                                            <tr key={ticket.id}>
                                                <td className="max-w-[26rem] px-4 py-3">
                                                    <div className="text-muted-foreground text-xs">#{ticket.id}</div>
                                                    <Link href={route('tickets.show', ticket.id)} className="font-medium hover:underline">
                                                        {ticket.title}
                                                    </Link>
                                                </td>
                                                <td className="px-4 py-3">
                                                    <TicketBadge kind="status" value={ticket.status} />
                                                </td>
                                                <td className="px-4 py-3">
                                                    <TicketBadge kind="priority" value={ticket.priority} />
                                                </td>
                                                <td className="px-4 py-3">{formatDateTime(ticket.created_at)}</td>
                                                <td className="px-4 py-3">{formatDateTime(ticket.updated_at)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            <div className="divide-y lg:hidden">
                                {recentTickets.map((ticket) => (
                                    <article key={ticket.id} className="space-y-4 p-4">
                                        <div className="space-y-2">
                                            <div className="text-muted-foreground text-xs">#{ticket.id}</div>
                                            <Link href={route('tickets.show', ticket.id)} className="font-medium hover:underline">
                                                {ticket.title}
                                            </Link>
                                            <div className="flex flex-wrap gap-2">
                                                <TicketBadge kind="status" value={ticket.status} />
                                                <TicketBadge kind="priority" value={ticket.priority} />
                                            </div>
                                        </div>
                                        <dl className="grid gap-2 text-sm sm:grid-cols-2">
                                            <MetaItem label="Created" value={formatDateTime(ticket.created_at)} />
                                            <MetaItem label="Updated" value={formatDateTime(ticket.updated_at)} />
                                        </dl>
                                    </article>
                                ))}
                            </div>
                        </>
                    ) : (
                        <div className="p-8 text-center">
                            <h2 className="text-lg font-medium">No tickets yet</h2>
                            <p className="text-muted-foreground mt-1 text-sm">Create the first ticket to start tracking issue activity.</p>
                            <Button className="mt-4" asChild>
                                <Link href={route('tickets.create')}>
                                    <Plus />
                                    New ticket
                                </Link>
                            </Button>
                        </div>
                    )}
                </section>
            </main>
        </AppLayout>
    );
}

function SummaryCard({ label, value, href }: { label: string; value: number; href: string }) {
    return (
        <Link href={href} className="focus-visible:ring-ring hover:bg-muted/50 border p-4 focus-visible:ring-2 focus-visible:outline-none">
            <div className="text-muted-foreground text-sm">{label}</div>
            <div className="mt-1 text-2xl font-semibold">{value}</div>
        </Link>
    );
}

function MetaItem({ label, value }: { label: string; value: string }) {
    return (
        <div>
            <dt className="text-muted-foreground">{label}</dt>
            <dd>{value}</dd>
        </div>
    );
}
