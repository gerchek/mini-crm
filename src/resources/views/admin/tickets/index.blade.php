@extends('layouts.app')

@section('title', 'Tickets — Mini CRM')

@section('content')
<h2 style="margin-bottom:1rem;">Tickets</h2>

<div class="card">
    <form method="GET" action="{{ route('admin.tickets.index') }}">
        <div class="filters">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    @foreach(\App\Models\Ticket::STATUSES as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="{{ request('email') }}" placeholder="customer@...">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ request('phone') }}" placeholder="+1...">
            </div>
            <div class="form-group">
                <label>Date from</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="form-group">
                <label>Date to</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.tickets.index') }}" class="btn" style="background:#e5e7eb;color:#374151;">Reset</a>
            </div>
        </div>
    </form>
</div>

<div class="card" style="padding:0;overflow:auto;">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Subject</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Created</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->id }}</td>
                    <td>{{ Str::limit($ticket->subject, 40) }}</td>
                    <td>{{ $ticket->customer->name }}</td>
                    <td>{{ $ticket->customer->email }}</td>
                    <td>{{ $ticket->customer->phone }}</td>
                    <td><span class="badge badge-{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></td>
                    <td>{{ $ticket->created_at->format('d.m.Y H:i') }}</td>
                    <td><a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-primary btn-sm">View</a></td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;color:#9ca3af;padding:2rem;">No tickets found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($tickets->hasPages())
    <div class="pagination">
        {{ $tickets->withQueryString()->links('vendor.pagination.custom') }}
    </div>
@endif
@endsection
