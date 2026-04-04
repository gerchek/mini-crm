<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminTicketTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'manager']);
        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');
    }

    public function test_guest_cannot_access_admin(): void
    {
        $this->get('/admin/tickets')->assertRedirect('/login');
    }

    public function test_user_without_role_cannot_access_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/tickets')
            ->assertForbidden();
    }

    public function test_manager_can_view_tickets_list(): void
    {
        Customer::factory()
            ->has(Ticket::factory()->count(3))
            ->create();

        $this->actingAs($this->manager)
            ->get('/admin/tickets')
            ->assertOk()
            ->assertViewHas('tickets');
    }

    public function test_manager_can_view_ticket_details(): void
    {
        $ticket = Ticket::factory()->create();

        $this->actingAs($this->manager)
            ->get("/admin/tickets/{$ticket->id}")
            ->assertOk()
            ->assertSee($ticket->subject);
    }

    public function test_manager_can_update_ticket_status(): void
    {
        $ticket = Ticket::factory()->create(['status' => 'new']);

        $this->actingAs($this->manager)
            ->patch("/admin/tickets/{$ticket->id}/status", ['status' => 'in_progress'])
            ->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_filter_tickets_by_status(): void
    {
        Customer::factory()
            ->has(Ticket::factory()->state(['status' => 'new'])->count(2))
            ->create();

        Customer::factory()
            ->has(Ticket::factory()->state(['status' => 'processed'])->count(1))
            ->create();

        $this->actingAs($this->manager)
            ->get('/admin/tickets?status=new')
            ->assertOk();
    }
}
