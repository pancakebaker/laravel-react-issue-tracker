<?php

use App\Enums\TicketStatus;
use App\Http\Controllers\TicketController;
use App\Models\Ticket;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return to_route('tickets.index');
})->middleware('auth')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard', function () {
        $statusCounts = Ticket::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $recentTickets = Ticket::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn (Ticket $ticket) => [
                'id' => $ticket->id,
                'title' => $ticket->title,
                'status' => [
                    'value' => $ticket->status->value,
                    'label' => $ticket->status->label(),
                ],
                'priority' => [
                    'value' => $ticket->priority->value,
                    'label' => $ticket->priority->label(),
                ],
                'created_at' => $ticket->created_at?->toISOString(),
                'updated_at' => $ticket->updated_at?->toISOString(),
            ])
            ->values();

        return Inertia::render('dashboard', [
            'summary' => [
                'total' => (int) $statusCounts->sum(),
                'open' => (int) ($statusCounts[TicketStatus::Open->value] ?? 0),
                'in_progress' => (int) ($statusCounts[TicketStatus::InProgress->value] ?? 0),
                'resolved' => (int) ($statusCounts[TicketStatus::Resolved->value] ?? 0),
            ],
            'recentTickets' => $recentTickets,
        ]);
    })->name('dashboard');

    Route::resource('tickets', TicketController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
