<?php

namespace Tests\Feature;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_casts_domain_values_to_enums_and_dates(): void
    {
        $ticket = Ticket::factory()->create([
            'status' => TicketStatus::InProgress,
            'priority' => TicketPriority::Critical,
            'category' => TicketCategory::Bug,
            'due_date' => '2026-09-15',
        ]);

        $ticket->refresh();

        $this->assertSame(TicketStatus::InProgress, $ticket->status);
        $this->assertSame(TicketPriority::Critical, $ticket->priority);
        $this->assertSame(TicketCategory::Bug, $ticket->category);
        $this->assertSame('2026-09-15', $ticket->due_date->toDateString());

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'in_progress',
            'priority' => 'critical',
            'category' => 'bug',
            'due_date' => '2026-09-15 00:00:00',
        ]);
    }

    public function test_ticket_factory_creates_valid_domain_values(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertInstanceOf(TicketStatus::class, $ticket->status);
        $this->assertInstanceOf(TicketPriority::class, $ticket->priority);
        $this->assertInstanceOf(TicketCategory::class, $ticket->category);
        $this->assertNotEmpty($ticket->title);
        $this->assertNotEmpty($ticket->description);
    }
}
