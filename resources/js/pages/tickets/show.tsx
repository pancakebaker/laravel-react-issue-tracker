import { TicketBadge } from '@/components/tickets/ticket-badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { formatDateOnly, formatDateTime } from '@/lib/ticket-format';
import { type BreadcrumbItem } from '@/types';
import { type Ticket } from '@/types/tickets';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, LoaderCircle, Pencil, Trash2 } from 'lucide-react';

interface ShowTicketProps {
    ticket: Ticket;
}

export default function ShowTicket({ ticket }: ShowTicketProps) {
    const { delete: destroy, processing } = useForm({});

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Tickets',
            href: '/tickets',
        },
        {
            title: ticket.title,
            href: `/tickets/${ticket.id}`,
        },
    ];

    const deleteTicket = () => {
        if (!window.confirm('Delete this ticket? This action cannot be undone.')) {
            return;
        }

        destroy(route('tickets.destroy', ticket.id));
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={ticket.title} />

            <main className="flex flex-1 flex-col gap-6 p-4">
                <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div className="space-y-3">
                        <Button variant="ghost" size="sm" asChild className="-ml-3">
                            <Link href={route('tickets.index')}>
                                <ArrowLeft />
                                Back to tickets
                            </Link>
                        </Button>
                        <div>
                            <h1 className="text-2xl font-semibold tracking-tight">{ticket.title}</h1>
                            <div className="mt-3 flex flex-wrap gap-2">
                                <TicketBadge kind="status" value={ticket.status} />
                                <TicketBadge kind="priority" value={ticket.priority} />
                            </div>
                        </div>
                    </div>

                    <div className="flex flex-col gap-2 sm:flex-row">
                        <Button variant="outline" asChild>
                            <Link href={route('tickets.edit', ticket.id)}>
                                <Pencil />
                                Edit
                            </Link>
                        </Button>
                        <Button variant="destructive" onClick={deleteTicket} disabled={processing}>
                            {processing ? <LoaderCircle className="h-4 w-4 animate-spin" /> : <Trash2 />}
                            Delete
                        </Button>
                    </div>
                </div>

                <section className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
                    <article className="space-y-3 border p-5">
                        <h2 className="text-lg font-medium">Description</h2>
                        <p className="text-muted-foreground whitespace-pre-wrap">{ticket.description}</p>
                    </article>

                    <aside className="border p-5">
                        <h2 className="text-lg font-medium">Details</h2>
                        <dl className="mt-4 space-y-4 text-sm">
                            <DetailItem label="Category" value={ticket.category.label} />
                            <DetailItem label="Assigned to" value={ticket.assigned_to ?? 'Unassigned'} />
                            <DetailItem label="Due date" value={formatDateOnly(ticket.due_date)} />
                            <DetailItem label="Created" value={formatDateTime(ticket.created_at)} />
                            <DetailItem label="Updated" value={formatDateTime(ticket.updated_at)} />
                        </dl>
                    </aside>
                </section>
            </main>
        </AppLayout>
    );
}

function DetailItem({ label, value }: { label: string; value: string }) {
    return (
        <div>
            <dt className="text-muted-foreground">{label}</dt>
            <dd className="mt-1 font-medium">{value}</dd>
        </div>
    );
}
