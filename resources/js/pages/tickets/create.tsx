import { TicketForm } from '@/components/tickets/ticket-form';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { type EnumOption } from '@/types/tickets';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Tickets',
        href: '/tickets',
    },
    {
        title: 'Create',
        href: '/tickets/create',
    },
];

interface CreateTicketProps {
    statuses: EnumOption[];
    priorities: EnumOption[];
    categories: EnumOption[];
}

export default function CreateTicket({ statuses, priorities, categories }: CreateTicketProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create ticket" />

            <main className="flex flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-2xl font-semibold tracking-tight">Create ticket</h1>
                    <p className="text-muted-foreground mt-1 text-sm">Capture a support request, bug, or feature item for follow-up.</p>
                </div>

                <TicketForm mode="create" statuses={statuses} priorities={priorities} categories={categories} />
            </main>
        </AppLayout>
    );
}
