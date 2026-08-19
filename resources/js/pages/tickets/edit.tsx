import { TicketForm } from '@/components/tickets/ticket-form';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { type EnumOption, type Ticket } from '@/types/tickets';
import { Head } from '@inertiajs/react';

interface EditTicketProps {
    ticket: Ticket;
    statuses: EnumOption[];
    priorities: EnumOption[];
    categories: EnumOption[];
}

export default function EditTicket({ ticket, statuses, priorities, categories }: EditTicketProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Tickets',
            href: '/tickets',
        },
        {
            title: ticket.title,
            href: `/tickets/${ticket.id}`,
        },
        {
            title: 'Edit',
            href: `/tickets/${ticket.id}/edit`,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit ${ticket.title}`} />

            <main className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">Edit ticket</h1>
                    <p className="text-muted-foreground mt-1 text-sm">Update the ticket details and triage fields.</p>
                </div>

                <TicketForm mode="edit" ticket={ticket} statuses={statuses} priorities={priorities} categories={categories} />
            </main>
        </AppLayout>
    );
}
