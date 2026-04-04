<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class TicketService
{
    public function createTicket(array $data, array $files = []): Ticket
    {
        return DB::transaction(function () use ($data, $files) {
            $customer = Customer::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'phone' => $data['phone'],
                ]
            );

            $customer->update([
                'name' => $data['name'],
                'phone' => $data['phone'],
            ]);

            $ticket = $customer->tickets()->create([
                'subject' => $data['subject'],
                'body' => $data['body'],
                'status' => Ticket::STATUS_NEW,
            ]);

            foreach ($files as $file) {
                $ticket->addMedia($file)->toMediaCollection('attachments');
            }

            return $ticket->load('customer');
        });
    }

    public function canSubmitTicket(string $email, string $phone): bool
    {
        $today = Carbon::today();

        return !$this->ticketExistsTodayBy('email', $email, $today)
            && !$this->ticketExistsTodayBy('phone', $phone, $today);
    }

    public function searchTickets(array $filters): LengthAwarePaginator
    {
        return Ticket::with('customer')
            ->when(
                $filters['status'] ?? null,
                fn ($q, $status) => $q->where('status', $status)
            )
            ->when(
                $filters['email'] ?? null,
                fn ($q, $email) => $q->whereHas(
                    'customer',
                    fn ($c) => $c->where('email', 'like', "%{$email}%")
                )
            )
            ->when(
                $filters['phone'] ?? null,
                fn ($q, $phone) => $q->whereHas(
                    'customer',
                    fn ($c) => $c->where('phone', 'like', "%{$phone}%")
                )
            )
            ->when(
                $filters['date_from'] ?? null,
                fn ($q, $date) => $q->whereDate('created_at', '>=', $date)
            )
            ->when(
                $filters['date_to'] ?? null,
                fn ($q, $date) => $q->whereDate('created_at', '<=', $date)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }

    public function updateStatus(Ticket $ticket, string $status): void
    {
        $ticket->update([
            'status' => $status,
            'responded_at' => $status !== Ticket::STATUS_NEW
                ? now()
                : $ticket->responded_at,
        ]);
    }

    public function getStatistics(): array
    {
        $now = Carbon::now();

        return [
            'today' => Ticket::whereDate('created_at', $now->toDateString())->count(),
            'week' => Ticket::whereBetween('created_at', [
                $now->copy()->startOfWeek(),
                $now->copy()->endOfWeek(),
            ])->count(),
            'month' => Ticket::whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)
                ->count(),
        ];
    }

    private function ticketExistsTodayBy(string $field, string $value, Carbon $today): bool
    {
        return Ticket::whereHas('customer', function ($q) use ($field, $value) {
            $q->where($field, $value);
        })->whereDate('created_at', $today)->exists();
    }
}
