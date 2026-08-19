<?php

namespace Database\Factories;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement([
                'Unable to upload attachment',
                'Customer portal login error',
                'Export report includes stale records',
                'Request for saved ticket views',
                'Billing notification copy update',
                'Intermittent search timeout',
                'Add priority indicator to list',
                'Support request from operations team',
            ]),
            'description' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(TicketStatus::cases()),
            'priority' => fake()->randomElement(TicketPriority::cases()),
            'category' => fake()->randomElement(TicketCategory::cases()),
            'assigned_to' => fake()->boolean(75) ? fake()->name() : null,
            'due_date' => fake()->boolean(70) ? fake()->dateTimeBetween('-1 week', '+6 weeks')->format('Y-m-d') : null,
        ];
    }

    public function status(TicketStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }

    public function priority(TicketPriority $priority): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => $priority,
        ]);
    }
}
