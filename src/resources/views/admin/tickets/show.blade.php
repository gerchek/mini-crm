@extends('layouts.app')

@section('title', 'Ticket #' . $ticket->id . ' — Mini CRM')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <h2>Ticket #{{ $ticket->id }}</h2>
    <a href="{{ route('admin.tickets.index') }}" class="btn" style="background:#e5e7eb;color:#374151;">Back to list</a>
</div>

<div class="card">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
        <div>
            <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
            <p><strong>Status:</strong> <span class="badge badge-{{ $ticket->status }}">{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</span></p>
            <p><strong>Created:</strong> {{ $ticket->created_at->format('d.m.Y H:i') }}</p>
            <p><strong>Responded at:</strong> {{ $ticket->responded_at?->format('d.m.Y H:i') ?? '—' }}</p>
        </div>
        <div>
            <p><strong>Customer:</strong> {{ $ticket->customer->name }}</p>
            <p><strong>Email:</strong> {{ $ticket->customer->email }}</p>
            <p><strong>Phone:</strong> {{ $ticket->customer->phone }}</p>
        </div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom:0.5rem;">Message</h3>
    <p style="white-space:pre-wrap;">{{ $ticket->body }}</p>
</div>

<div class="card">
    <h3 style="margin-bottom:0.5rem;">Change Status</h3>
    <form method="POST" action="{{ route('admin.tickets.update-status', $ticket) }}" style="display:flex;gap:0.5rem;align-items:end;">
        @csrf
        @method('PATCH')
        <div class="form-group" style="margin-bottom:0;">
            <select name="status" class="form-control">
                @foreach(\App\Models\Ticket::STATUSES as $s)
                    <option value="{{ $s }}" @selected($ticket->status === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
    @error('status') <p style="color:#dc2626;font-size:0.8rem;margin-top:0.5rem;">{{ $message }}</p> @enderror
</div>

@if($ticket->media->count())
<div class="card">
    <h3 style="margin-bottom:0.5rem;">Attachments</h3>
    <ul style="list-style:none;">
        @foreach($ticket->media as $media)
            <li style="padding:0.4rem 0;border-bottom:1px solid #e5e7eb;">
                <a href="{{ $media->getUrl() }}" target="_blank" style="color:#3b82f6;">
                    {{ $media->file_name }}
                </a>
                <span style="color:#9ca3af;font-size:0.8rem;">({{ number_format($media->size / 1024, 1) }} KB)</span>
            </li>
        @endforeach
    </ul>
</div>
@endif
@endsection
