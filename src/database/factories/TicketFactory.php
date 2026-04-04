<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'subject' => fake()->sentence(),
            'body' => fake()->paragraphs(2, true),
            'status' => fake()->randomElement(Ticket::STATUSES),
            'responded_at' => fake()->optional(0.5)->dateTimeBetween('-1 month'),
        ];
    }
}
