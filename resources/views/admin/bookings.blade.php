@extends('layouts.app')
@section('title', 'All Bookings - NurseSheba Admin')
@section('content')
@php
  $statusLabels = [
    'all' => 'All',
    'pending' => 'Pending',
    'accepted' => 'Accepted',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
  ];
@endphp

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-0">All Bookings</h3>
      <p class="text-muted mb-0">Review upcoming appointments, past history, and manage booking status platform-wide.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">&larr; Back</a>
  </div>

  <div class="row g-3 mb-4">
    @foreach(['pending', 'accepted', 'completed', 'cancelled'] as $statusKey)
      <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body p-3">
            <div class="small text-uppercase text-muted mb-2">{{ $statusLabels[$statusKey] }}</div>
            <div class="display-6 fw-bold text-primary">{{ $statusCounts[$statusKey] ?? 0 }}</div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
      <form method="GET" action="{{ route('admin.bookings') }}" class="row g-3 align-items-end">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Status</label>
          <select name="status" class="form-select">
            @foreach($statusLabels as $statusKey => $label)
              <option value="{{ $statusKey }}" {{ $statusFilter === $statusKey ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Timeline</label>
          <select name="timeline" class="form-select">
            <option value="all" {{ $timelineFilter === 'all' ? 'selected' : '' }}>All bookings</option>
            <option value="upcoming" {{ $timelineFilter === 'upcoming' ? 'selected' : '' }}>Upcoming appointments</option>
            <option value="past" {{ $timelineFilter === 'past' ? 'selected' : '' }}>Past appointments</option>
          </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
          <button type="submit" class="btn btn-primary">Apply Filters</button>
          <a href="{{ route('admin.bookings') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>Patient</th>
              <th>Nurse</th>
              <th>Schedule</th>
              <th>Service</th>
              <th>Address</th>
              <th>Status</th>
              <th>Manage</th>
            </tr>
          </thead>
          <tbody>
            @forelse($bookings as $booking)
              <tr>
                <td>{{ $booking->patient->name ?? 'N/A' }}</td>
                <td>{{ $booking->nurse->name ?? 'N/A' }}</td>
                <td>
                  <div>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</div>
                  <div class="text-muted small">{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</div>
                </td>
                <td>{{ $booking->service_type }}</td>
                <td>{{ $booking->service_address ?? 'N/A' }}</td>
                <td>
                  @if($booking->status === 'pending')
                    <span class="badge bg-warning text-dark">Pending</span>
                  @elseif($booking->status === 'accepted')
                    <span class="badge bg-info">Accepted</span>
                  @elseif($booking->status === 'completed')
                    <span class="badge bg-success">Completed</span>
                  @else
                    <span class="badge bg-danger">Cancelled</span>
                  @endif
                </td>
                <td>
                  <form action="{{ route('admin.bookings.status', $booking) }}" method="POST" class="d-flex gap-2 align-items-center">
                    @csrf
                    <select name="status" class="form-select form-select-sm">
                      @foreach(['pending', 'accepted', 'completed', 'cancelled'] as $option)
                        <option value="{{ $option }}" {{ $booking->status === $option ? 'selected' : '' }}>{{ ucfirst($option) }}</option>
                      @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">No bookings found for the selected filters.</td>
              </tr>
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
