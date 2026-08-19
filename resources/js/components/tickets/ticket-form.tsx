import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { type EnumOption, type Ticket } from '@/types/tickets';
import { Link, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { type FormEventHandler } from 'react';

interface TicketFormData {
    [key: string]: string;
    title: string;
    description: string;
    status: string;
    priority: string;
    category: string;
    assigned_to: string;
    due_date: string;
}

interface TicketFormProps {
    mode: 'create' | 'edit';
    statuses: EnumOption[];
    priorities: EnumOption[];
    categories: EnumOption[];
    ticket?: Ticket;
}

export function TicketForm({ mode, statuses, priorities, categories, ticket }: TicketFormProps) {
    const { data, setData, post, put, processing, errors } = useForm<TicketFormData>({
        title: ticket?.title ?? '',
        description: ticket?.description ?? '',
        status: ticket?.status.value ?? statuses[0]?.value ?? '',
        priority: ticket?.priority.value ?? priorities[0]?.value ?? '',
        category: ticket?.category.value ?? categories[0]?.value ?? '',
        assigned_to: ticket?.assigned_to ?? '',
        due_date: ticket?.due_date ?? '',
    });

    const submit: FormEventHandler = (event) => {
        event.preventDefault();

        if (mode === 'edit' && ticket) {
            put(route('tickets.update', ticket.id), {
                preserveScroll: true,
            });

            return;
        }

        post(route('tickets.store'), {
            preserveScroll: true,
        });
    };

    return (
        <form onSubmit={submit} className="space-y-6">
            <div className="grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
                <div className="space-y-6">
                    <div className="grid gap-2">
                        <Label htmlFor="title">Title</Label>
                        <Input
                            id="title"
                            value={data.title}
                            onChange={(event) => setData('title', event.target.value)}
                            required
                            autoFocus
                            maxLength={180}
                            placeholder="Briefly describe the issue"
                        />
                        <InputError message={errors.title} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="description">Description</Label>
                        <textarea
                            id="description"
                            value={data.description}
                            onChange={(event) => setData('description', event.target.value)}
                            required
                            rows={10}
                            placeholder="Add enough detail for the next person to understand the request"
                            className="border-input bg-background ring-offset-background placeholder:text-muted-foreground focus-visible:ring-ring min-h-40 w-full rounded-md border px-3 py-2 text-sm focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                        />
                        <InputError message={errors.description} />
                    </div>
                </div>

                <div className="space-y-5">
                    <SelectField
                        id="status"
                        label="Status"
                        value={data.status}
                        options={statuses}
                        error={errors.status}
                        onChange={(value) => setData('status', value)}
                    />

                    <SelectField
                        id="priority"
                        label="Priority"
                        value={data.priority}
                        options={priorities}
                        error={errors.priority}
                        onChange={(value) => setData('priority', value)}
                    />

                    <SelectField
                        id="category"
                        label="Category"
                        value={data.category}
                        options={categories}
                        error={errors.category}
                        onChange={(value) => setData('category', value)}
                    />

                    <div className="grid gap-2">
                        <Label htmlFor="assigned_to">Assigned to</Label>
                        <Input
                            id="assigned_to"
                            value={data.assigned_to}
                            onChange={(event) => setData('assigned_to', event.target.value)}
                            maxLength={120}
                            placeholder="Optional"
                        />
                        <InputError message={errors.assigned_to} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="due_date">Due date</Label>
                        <Input id="due_date" type="date" value={data.due_date} onChange={(event) => setData('due_date', event.target.value)} />
                        <InputError message={errors.due_date} />
                    </div>
                </div>
            </div>

            <div className="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
                <Button type="button" variant="outline" asChild>
                    <Link href={mode === 'edit' && ticket ? route('tickets.show', ticket.id) : route('tickets.index')}>Cancel</Link>
                </Button>
                <Button type="submit" disabled={processing}>
                    {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
                    {mode === 'edit' ? 'Save ticket' : 'Create ticket'}
                </Button>
            </div>
        </form>
    );
}

interface SelectFieldProps {
    id: keyof Pick<TicketFormData, 'status' | 'priority' | 'category'>;
    label: string;
    value: string;
    options: EnumOption[];
    error?: string;
    onChange: (value: string) => void;
}

function SelectField({ id, label, value, options, error, onChange }: SelectFieldProps) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={id}>{label}</Label>
            <select
                id={id}
                value={value}
                onChange={(event) => onChange(event.target.value)}
                className="border-input bg-background ring-offset-background focus:ring-ring flex h-10 w-full rounded-md border px-3 py-2 text-sm focus:ring-2 focus:ring-offset-2 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50"
            >
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
            <InputError message={error} />
        </div>
    );
}
