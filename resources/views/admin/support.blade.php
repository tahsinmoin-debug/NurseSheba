@extends('layouts.app')
@section('title', 'Support Requests - NurseSheba Admin')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Support Requests</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">&larr; Back</a>
  </div>
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr><th>User</th><th>Subject</th><th>Message</th><th>Status</th><th>Date</th></tr>
          </thead>
          <tbody>
            @forelse($requests as $req)
            <tr>
              <td>{{ $req->user->name ?? 'N/A' }}</td>
              <td>{{ $req->subject }}</td>
              <td>{{ Str::limit($req->message, 80) }}</td>
              <td><span class="badge {{ $req->status === 'open' ? 'bg-warning text-dark' : 'bg-success' }}">{{ ucfirst($req->status) }}</span></td>
              <td>{{ $req->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-4 text-muted">No support requests found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
