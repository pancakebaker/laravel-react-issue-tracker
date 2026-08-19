<?php

namespace Tests\Feature;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_home_to_the_login_page()
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_authenticated_users_are_redirected_from_home_to_tickets()
    {
        $this->actingAs(User::factory()->create());

        $this->get('/')->assertRedirect(route('tickets.index'));
    }

    public function test_guests_visiting_home_can_log_in_to_the_ticket_index()
    {
        $user = User::factory()->create();

        $this->get('/')->assertRedirect(route('login'));

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
        $this->get('/')->assertRedirect(route('tickets.index'));
    }

    public function test_guests_are_redirected_to_the_login_page()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $this->actingAs($user = User::factory()->create());

        $this->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('summary.total', 0)
                ->where('summary.open', 0)
                ->where('summary.in_progress', 0)
                ->where('summary.resolved', 0)
                ->has('recentTickets', 0)
            );
    }

    public function test_dashboard_shows_ticket_summary_and_five_most_recent_tickets()
    {
        $this->actingAs(User::factory()->create());

        $createdAt = now()->subDay();

        Ticket::factory()->create([
            'title' => 'Oldest ticket',
            'status' => TicketStatus::Open,
            'priority' => TicketPriority::Low,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        Ticket::factory()->create([
            'title' => 'Fifth recent ticket',
            'status' => TicketStatus::Open,
            'priority' => TicketPriority::Medium,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        Ticket::factory()->create([
            'title' => 'Fourth recent ticket',
            'status' => TicketStatus::InProgress,
            'priority' => TicketPriority::High,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        Ticket::factory()->create([
            'title' => 'Third recent ticket',
            'status' => TicketStatus::Resolved,
            'priority' => TicketPriority::Critical,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        Ticket::factory()->create([
            'title' => 'Second recent ticket',
            'status' => TicketStatus::InProgress,
            'priority' => TicketPriority::Medium,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        Ticket::factory()->create([
            'title' => 'Most recent ticket',
            'status' => TicketStatus::Resolved,
            'priority' => TicketPriority::High,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('dashboard')
                ->where('summary.total', 6)
                ->where('summary.open', 2)
                ->where('summary.in_progress', 2)
                ->where('summary.resolved', 2)
                ->has('recentTickets', 5)
                ->where('recentTickets.0.title', 'Most recent ticket')
                ->where('recentTickets.0.status.value', TicketStatus::Resolved->value)
                ->where('recentTickets.0.priority.value', TicketPriority::High->value)
                ->where('recentTickets.4.title', 'Fifth recent ticket')
            );
    }
}
