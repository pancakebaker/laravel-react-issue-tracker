<?php

namespace Database\Seeders;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $tickets = [
            [
                'title' => 'Customer cannot submit support request',
                'description' => 'The support form returns an error after the customer attaches a screenshot. Operations needs a fix or clear workaround.',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::Critical,
                'category' => TicketCategory::Bug,
                'assigned_to' => 'Maya Chen',
                'due_date' => now()->addDays(2)->toDateString(),
            ],
            [
                'title' => 'Add internal note field to tickets',
                'description' => 'Support leads want a private note area for follow-up details that should not be visible to customers.',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::Medium,
                'category' => TicketCategory::Feature,
                'assigned_to' => null,
                'due_date' => now()->addWeeks(3)->toDateString(),
            ],
            [
                'title' => 'Escalation email copy is outdated',
                'description' => 'The escalation notification references an old support address and should be updated before the next release.',
                'status' => TicketStatus::InProgress,
                'priority' => TicketPriority::Low,
                'category' => TicketCategory::Support,
                'assigned_to' => 'Jordan Patel',
                'due_date' => null,
            ],
            [
                'title' => 'Ticket list search occasionally times out',
                'description' => 'Searching by long customer names can time out during peak hours. Review the query and add safeguards if needed.',
                'status' => TicketStatus::InProgress,
                'priority' => TicketPriority::High,
                'category' => TicketCategory::Bug,
                'assigned_to' => 'Elliot Ramos',
                'due_date' => now()->addDays(7)->toDateString(),
            ],
            [
                'title' => 'Create saved filter for unresolved tickets',
                'description' => 'Support managers asked for a quick way to return to open and in-progress tickets without rebuilding filters manually.',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::Medium,
                'category' => TicketCategory::Feature,
                'assigned_to' => 'Priya Shah',
                'due_date' => now()->addWeeks(4)->toDateString(),
            ],
            [
                'title' => 'Incorrect category shown after ticket update',
                'description' => 'A resolved ticket briefly displayed the previous category after saving. Verify whether this was stale page state or persisted data.',
                'status' => TicketStatus::Resolved,
                'priority' => TicketPriority::Medium,
                'category' => TicketCategory::Bug,
                'assigned_to' => 'Maya Chen',
                'due_date' => now()->subDays(1)->toDateString(),
            ],
            [
                'title' => 'Document password reset support script',
                'description' => 'Support needs a short checklist for helping users through password reset issues without escalating every case.',
                'status' => TicketStatus::Resolved,
                'priority' => TicketPriority::Low,
                'category' => TicketCategory::Support,
                'assigned_to' => null,
                'due_date' => null,
            ],
            [
                'title' => 'Attachment preview fails for large PNG files',
                'description' => 'PNG files over the expected size limit fail without a helpful message. Add a clearer error path.',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::High,
                'category' => TicketCategory::Bug,
                'assigned_to' => 'Nora Williams',
                'due_date' => now()->addDays(5)->toDateString(),
            ],
            [
                'title' => 'Support queue needs source channel label',
                'description' => 'Agents want to see whether a ticket came from email, phone, or the web form while triaging.',
                'status' => TicketStatus::InProgress,
                'priority' => TicketPriority::Medium,
                'category' => TicketCategory::Feature,
                'assigned_to' => 'Jordan Patel',
                'due_date' => now()->addWeeks(2)->toDateString(),
            ],
            [
                'title' => 'Follow up on billing address correction',
                'description' => 'A customer requested confirmation that their corrected billing address was saved successfully.',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::Low,
                'category' => TicketCategory::Support,
                'assigned_to' => null,
                'due_date' => now()->addDays(10)->toDateString(),
            ],
            [
                'title' => 'Critical outage ticket should remain visible',
                'description' => 'Resolved outage tickets need to remain easy to find for post-incident review and reporting.',
                'status' => TicketStatus::Resolved,
                'priority' => TicketPriority::Critical,
                'category' => TicketCategory::Bug,
                'assigned_to' => 'Elliot Ramos',
                'due_date' => now()->subDays(3)->toDateString(),
            ],
            [
                'title' => 'Clarify SLA wording on support template',
                'description' => 'The current support response template uses unclear SLA language and should be revised for customer-facing replies.',
                'status' => TicketStatus::InProgress,
                'priority' => TicketPriority::High,
                'category' => TicketCategory::Support,
                'assigned_to' => 'Priya Shah',
                'due_date' => now()->addDays(12)->toDateString(),
            ],
            [
                'title' => 'Add category chips to ticket details',
                'description' => 'A small visual category marker would make ticket detail pages easier to scan during review meetings.',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::Low,
                'category' => TicketCategory::Feature,
                'assigned_to' => 'Nora Williams',
                'due_date' => null,
            ],
            [
                'title' => 'Resolved ticket reopened by customer reply',
                'description' => 'A customer reply on a resolved support case did not reopen the ticket as expected. Confirm the desired behavior.',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::High,
                'category' => TicketCategory::Support,
                'assigned_to' => 'Maya Chen',
                'due_date' => now()->addDays(4)->toDateString(),
            ],
            [
                'title' => 'Export includes archived test ticket',
                'description' => 'The CSV export included a ticket that should have been excluded from the active support review.',
                'status' => TicketStatus::Resolved,
                'priority' => TicketPriority::Medium,
                'category' => TicketCategory::Bug,
                'assigned_to' => null,
                'due_date' => now()->subWeek()->toDateString(),
            ],
            [
                'title' => 'Improve empty state message',
                'description' => 'When no tickets match selected filters, the empty state should explain that filters may be hiding results.',
                'status' => TicketStatus::InProgress,
                'priority' => TicketPriority::Low,
                'category' => TicketCategory::Feature,
                'assigned_to' => 'Jordan Patel',
                'due_date' => now()->addWeeks(5)->toDateString(),
            ],
            [
                'title' => 'Urgent customer cannot view invoice',
                'description' => 'A priority customer reports that the invoice page loads without line items. Support needs a quick investigation.',
                'status' => TicketStatus::Open,
                'priority' => TicketPriority::Critical,
                'category' => TicketCategory::Support,
                'assigned_to' => 'Elliot Ramos',
                'due_date' => now()->addDay()->toDateString(),
            ],
            [
                'title' => 'Feature request intake needs triage label',
                'description' => 'Product wants newly submitted feature requests to have a clearer triage path before they are accepted.',
                'status' => TicketStatus::Resolved,
                'priority' => TicketPriority::Medium,
                'category' => TicketCategory::Feature,
                'assigned_to' => 'Priya Shah',
                'due_date' => null,
            ],
        ];

        foreach ($tickets as $ticket) {
            Ticket::factory()->create($ticket);
        }
    }
}
