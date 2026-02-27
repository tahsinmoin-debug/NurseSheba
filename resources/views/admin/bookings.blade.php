@extends('layouts.app')
@section('title', 'All Bookings - NurseSheba Admin')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">All Bookings</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">&larr; Back</a>
  </div>
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr><th>Patient</th><th>Nurse</th><th>Date</th><th>Time</th><th>Service</th><th>Status</th></tr>
          </thead>
          <tbody>
            @forelse($bookings as $booking)
            <tr>
              <td>{{ $booking->patient->name ?? 'N/A' }}</td>
              <td>{{ $booking->nurse->name ?? 'N/A' }}</td>
              <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>
              <td>{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</td>
              <td>{{ $booking->service_type }}</td>
              <td>
                @if($booking->status === 'pending') <span class="badge bg-warning text-dark">Pending</span>
                @elseif($booking->status === 'accepted') <span class="badge bg-info">Accepted</span>
                @elseif($booking->status === 'completed') <span class="badge bg-success">Completed</span>
                @else <span class="badge bg-danger">Cancelled</span>
                @endif
              </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4 text-muted">No bookings found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($bookings->hasPages())
    <div class="card-footer bg-white">{{ $bookings->links() }}</div>
    @endif
  </div>
</div>
@endsection
