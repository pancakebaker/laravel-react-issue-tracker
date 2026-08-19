<?php

namespace App\Http\Controllers;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $status = TicketStatus::tryFrom((string) $request->query('status'));
        $priority = TicketPriority::tryFrom((string) $request->query('priority'));

        $tickets = Ticket::query()
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($priority, fn ($query) => $query->where('priority', $priority))
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Ticket $ticket) => $this->ticketData($ticket));

        return Inertia::render('tickets/index', [
            'tickets' => $tickets,
            'filters' => [
                'status' => $status?->value,
                'priority' => $priority?->value,
            ],
            'statuses' => TicketStatus::options(),
            'priorities' => TicketPriority::options(),
            'summary' => $this->summaryCounts(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('tickets/create', [
            'statuses' => TicketStatus::options(),
            'priorities' => TicketPriority::options(),
            'categories' => TicketCategory::options(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = Ticket::create($request->validated());

        return to_route('tickets.show', $ticket)
            ->with('success', 'Ticket created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): Response
    {
        return Inertia::render('tickets/show', [
            'ticket' => $this->ticketData($ticket),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket): Response
    {
        return Inertia::render('tickets/edit', [
            'ticket' => $this->ticketData($ticket),
            'statuses' => TicketStatus::options(),
            'priorities' => TicketPriority::options(),
            'categories' => TicketCategory::options(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->update($request->validated());

        return to_route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): RedirectResponse
    {
        $ticket->delete();

        return to_route('tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function ticketData(Ticket $ticket): array
    {
        return [
            'id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => [
                'value' => $ticket->status->value,
                'label' => $ticket->status->label(),
            ],
            'priority' => [
                'value' => $ticket->priority->value,
                'label' => $ticket->priority->label(),
            ],
            'category' => [
                'value' => $ticket->category->value,
                'label' => $ticket->category->label(),
            ],
            'assigned_to' => $ticket->assigned_to,
            'due_date' => $ticket->due_date?->toDateString(),
            'created_at' => $ticket->created_at?->toISOString(),
            'updated_at' => $ticket->updated_at?->toISOString(),
        ];
    }

    /**
     * @return array{all: int, open: int, in_progress: int, resolved: int}
     */
    private function summaryCounts(): array
    {
        $statusCounts = Ticket::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'all' => Ticket::count(),
            TicketStatus::Open->value => (int) ($statusCounts[TicketStatus::Open->value] ?? 0),
            TicketStatus::InProgress->value => (int) ($statusCounts[TicketStatus::InProgress->value] ?? 0),
            TicketStatus::Resolved->value => (int) ($statusCounts[TicketStatus::Resolved->value] ?? 0),
        ];
    }
}
