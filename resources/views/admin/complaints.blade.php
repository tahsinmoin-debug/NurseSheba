@extends('layouts.app')
@section('title', 'Complaints - NurseSheba Admin')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Complaints</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">&larr; Back</a>
  </div>
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr><th>From</th><th>Against Nurse</th><th>Message</th><th>Status</th><th>Date</th></tr>
          </thead>
          <tbody>
            @forelse($complaints as $complaint)
            <tr>
              <td>{{ $complaint->user->name ?? 'N/A' }}</td>
              <td>{{ $complaint->nurse->name ?? 'N/A' }}</td>
              <td>{{ Str::limit($complaint->message, 80) }}</td>
              <td><span class="badge {{ $complaint->status === 'open' ? 'bg-danger' : 'bg-success' }}">{{ ucfirst($complaint->status) }}</span></td>
              <td>{{ $complaint->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-4 text-muted">No complaints found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
