<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TicketCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_users_cannot_access_ticket_management(): void
    {
        $ticket = Ticket::factory()->create();

        $this->get(route('tickets.index'))->assertRedirect(route('login'));
        $this->get(route('tickets.create'))->assertRedirect(route('login'));
        $this->post(route('tickets.store'), $this->validPayload())->assertRedirect(route('login'));
        $this->get(route('tickets.show', $ticket))->assertRedirect(route('login'));
        $this->get(route('tickets.edit', $ticket))->assertRedirect(route('login'));
        $this->put(route('tickets.update', $ticket), $this->validPayload())->assertRedirect(route('login'));
        $this->delete(route('tickets.destroy', $ticket))->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_list_tickets(): void
    {
        $user = User::factory()->create();

        Ticket::factory()->count(2)->create();

        $response = $this->actingAs($user)->inertiaGet(route('tickets.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tickets/index')
                ->has('tickets.data', 2)
                ->has('statuses', 3)
                ->has('priorities', 4)
                ->where('filters.status', null)
                ->where('filters.priority', null));
    }

    public function test_authenticated_users_can_view_a_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'title' => 'Attachment upload fails',
            'status' => TicketStatus::Open,
        ]);

        $response = $this->actingAs($user)->inertiaGet(route('tickets.show', $ticket));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tickets/show')
                ->where('ticket.id', $ticket->id)
                ->where('ticket.title', 'Attachment upload fails')
                ->where('ticket.status.value', 'open')
                ->where('ticket.status.label', 'Open'));
    }

    public function test_authenticated_users_can_access_the_create_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->inertiaGet(route('tickets.create'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tickets/create')
                ->has('statuses', 3)
                ->has('priorities', 4)
                ->has('categories', 3)
                ->where('statuses.1.value', 'in_progress')
                ->where('statuses.1.label', 'In Progress'));
    }

    public function test_authenticated_users_can_create_a_valid_ticket(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('tickets.store'), $this->validPayload([
                'title' => 'New support ticket',
                'status' => 'in_progress',
            ]));

        $ticket = Ticket::firstWhere('title', 'New support ticket');

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Ticket created successfully.')
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertDatabaseHas('tickets', [
            'title' => 'New support ticket',
            'status' => 'in_progress',
            'priority' => 'high',
            'category' => 'support',
        ]);
    }

    public function test_invalid_ticket_creation_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('tickets.create'))
            ->post(route('tickets.store'), [
                'title' => '',
                'description' => '',
                'status' => 'not-a-status',
                'priority' => 'not-a-priority',
                'category' => 'not-a-category',
                'assigned_to' => str_repeat('A', 121),
                'due_date' => 'not-a-date',
            ]);

        $response
            ->assertSessionHasErrors(['title', 'description', 'status', 'priority', 'category', 'assigned_to', 'due_date'])
            ->assertRedirect(route('tickets.create'));

        $this->assertDatabaseCount('tickets', 0);
    }

    public function test_authenticated_users_can_access_the_edit_page(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($user)->inertiaGet(route('tickets.edit', $ticket));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tickets/edit')
                ->where('ticket.id', $ticket->id)
                ->has('statuses', 3)
                ->has('priorities', 4)
                ->has('categories', 3));
    }

    public function test_authenticated_users_can_update_a_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'title' => 'Original title',
            'priority' => TicketPriority::Low,
        ]);

        $response = $this
            ->actingAs($user)
            ->put(route('tickets.update', $ticket), $this->validPayload([
                'title' => 'Updated title',
                'priority' => 'critical',
            ]));

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', 'Ticket updated successfully.')
            ->assertRedirect(route('tickets.show', $ticket));

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Updated title',
            'priority' => 'critical',
        ]);
    }

    public function test_invalid_ticket_updates_are_rejected(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create([
            'title' => 'Original title',
            'status' => TicketStatus::Open,
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('tickets.edit', $ticket))
            ->put(route('tickets.update', $ticket), $this->validPayload([
                'title' => '',
                'status' => 'invalid',
            ]));

        $response
            ->assertSessionHasErrors(['title', 'status'])
            ->assertRedirect(route('tickets.edit', $ticket));

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Original title',
            'status' => 'open',
        ]);
    }

    public function test_authenticated_users_can_delete_a_ticket(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($user)->delete(route('tickets.destroy', $ticket));

        $response
            ->assertSessionHas('success', 'Ticket deleted successfully.')
            ->assertRedirect(route('tickets.index'));

        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
        ]);
    }

    public function test_tickets_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();
        Ticket::factory()->create(['title' => 'Open ticket', 'status' => TicketStatus::Open]);
        Ticket::factory()->create(['title' => 'Resolved ticket', 'status' => TicketStatus::Resolved]);

        $response = $this->actingAs($user)->inertiaGet(route('tickets.index', ['status' => 'open']));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tickets/index')
                ->has('tickets.data', 1)
                ->where('tickets.data.0.title', 'Open ticket')
                ->where('filters.status', 'open'));
    }

    public function test_tickets_can_be_filtered_by_priority(): void
    {
        $user = User::factory()->create();
        Ticket::factory()->create(['title' => 'Low priority ticket', 'priority' => TicketPriority::Low]);
        Ticket::factory()->create(['title' => 'Critical priority ticket', 'priority' => TicketPriority::Critical]);

        $response = $this->actingAs($user)->inertiaGet(route('tickets.index', ['priority' => 'critical']));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tickets/index')
                ->has('tickets.data', 1)
                ->where('tickets.data.0.title', 'Critical priority ticket')
                ->where('filters.priority', 'critical'));
    }

    public function test_invalid_filters_are_ignored(): void
    {
        $user = User::factory()->create();
        Ticket::factory()->count(2)->create();

        $response = $this->actingAs($user)->inertiaGet(route('tickets.index', [
            'status' => 'invalid',
            'priority' => 'invalid',
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('tickets.data', 2)
                ->where('filters.status', null)
                ->where('filters.priority', null));
    }

    public function test_pagination_works(): void
    {
        $user = User::factory()->create();
        Ticket::factory()->count(12)->create();

        $response = $this->actingAs($user)->inertiaGet(route('tickets.index'));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('tickets.data', 10)
                ->where('tickets.total', 12)
                ->where('tickets.per_page', 10)
                ->where('tickets.current_page', 1));
    }

    public function test_filter_query_parameters_are_preserved_across_pagination(): void
    {
        $user = User::factory()->create();
        Ticket::factory()->count(12)->create([
            'status' => TicketStatus::Open,
            'priority' => TicketPriority::High,
        ]);
        Ticket::factory()->count(2)->create([
            'status' => TicketStatus::Resolved,
            'priority' => TicketPriority::Low,
        ]);

        $response = $this->actingAs($user)->inertiaGet(route('tickets.index', [
            'status' => 'open',
            'priority' => 'high',
        ]));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('tickets.total', 12)
                ->where('tickets.next_page_url', fn (?string $url) => str_contains($url ?? '', 'status=open') && str_contains($url ?? '', 'priority=high')));
    }

    public function test_index_returns_summary_counts_for_the_full_dataset(): void
    {
        $user = User::factory()->create();
        Ticket::factory()->count(2)->create(['status' => TicketStatus::Open]);
        Ticket::factory()->count(3)->create(['status' => TicketStatus::InProgress]);
        Ticket::factory()->count(4)->create(['status' => TicketStatus::Resolved]);

        $response = $this->actingAs($user)->inertiaGet(route('tickets.index', ['status' => 'open']));

        $response
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('tickets.total', 2)
                ->where('summary.all', 9)
                ->where('summary.open', 2)
                ->where('summary.in_progress', 3)
                ->where('summary.resolved', 4));
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Customer cannot upload invoice attachment',
            'description' => 'The upload button appears to submit, but no file is attached to the ticket.',
            'status' => 'open',
            'priority' => 'high',
            'category' => 'support',
            'assigned_to' => 'Maya Chen',
            'due_date' => '2026-09-15',
        ], $overrides);
    }

    private function inertiaGet(string $uri): TestResponse
    {
        $this->withoutVite();

        return $this->get($uri);
    }
}
