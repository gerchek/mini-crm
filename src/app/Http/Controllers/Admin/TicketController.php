<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function __construct(
        private readonly TicketService $ticketService,
    ) {}

    public function index(Request $request): View
    {
        $tickets = $this->ticketService->searchTickets(
            $request->only(['status', 'email', 'phone', 'date_from', 'date_to'])
        );

        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load(['customer', 'media']);

        return view('admin.tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', Ticket::STATUSES)],
        ]);

        $this->ticketService->updateStatus($ticket, $request->status);

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket status updated.');
    }
}
