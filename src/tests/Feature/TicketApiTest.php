<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_ticket(): void
    {
        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'phone' => '+12025551234',
            'email' => 'john@example.com',
            'subject' => 'Test ticket',
            'body' => 'This is a test message.',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'subject', 'body', 'status', 'customer', 'created_at'],
            ]);

        $this->assertDatabaseHas('customers', ['email' => 'john@example.com']);
        $this->assertDatabaseHas('tickets', ['subject' => 'Test ticket']);
    }

    public function test_validation_fails_without_required_fields(): void
    {
        $response = $this->postJson('/api/tickets', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone', 'email', 'subject', 'body']);
    }

    public function test_validation_fails_with_invalid_phone(): void
    {
        $response = $this->postJson('/api/tickets', [
            'name' => 'John',
            'phone' => '12345',
            'email' => 'john@example.com',
            'subject' => 'Test',
            'body' => 'Message',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    }

    public function test_rate_limit_one_ticket_per_day_per_email(): void
    {
        $data = [
            'name' => 'John Doe',
            'phone' => '+12025551234',
            'email' => 'john@example.com',
            'subject' => 'First ticket',
            'body' => 'First message.',
        ];

        $this->postJson('/api/tickets', $data)->assertStatus(201);

        $data['subject'] = 'Second ticket';
        $data['phone'] = '+12025559999';

        $this->postJson('/api/tickets', $data)->assertStatus(429);
    }

    public function test_rate_limit_one_ticket_per_day_per_phone(): void
    {
        $this->postJson('/api/tickets', [
            'name' => 'John',
            'phone' => '+12025551234',
            'email' => 'john@example.com',
            'subject' => 'First',
            'body' => 'Message',
        ])->assertStatus(201);

        $this->postJson('/api/tickets', [
            'name' => 'Jane',
            'phone' => '+12025551234',
            'email' => 'jane@example.com',
            'subject' => 'Second',
            'body' => 'Message',
        ])->assertStatus(429);
    }

    public function test_statistics_endpoint(): void
    {
        Customer::factory()
            ->has(Ticket::factory()->count(3))
            ->create();

        $response = $this->getJson('/api/tickets/statistics');

        $response->assertOk()
            ->assertJsonStructure(['today', 'week', 'month']);
    }
}
