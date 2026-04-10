@extends('layouts.app')
@section('title', 'Patient Dashboard - NurseSheba')
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
      <h3 class="fw-bold mb-0">Welcome, {{ auth()->user()->name }}!</h3>
      <p class="text-muted mb-0">Track upcoming visits, review past appointments, and manage booking requests.</p>
    </div>
    <a href="{{ route('nurses.index') }}" class="btn btn-primary"><i class="fas fa-search me-2"></i>Find a Nurse</a>
  </div>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h5 class="fw-bold mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter By Status</h5>
        <span class="text-muted small">Current filter: {{ $statusLabels[$statusFilter] ?? 'All' }}</span>
      </div>
      <div class="d-flex flex-wrap gap-2">
        @foreach($statusLabels as $statusKey => $label)
          <a href="{{ route('patient.dashboard', ['status' => $statusKey === 'all' ? null : $statusKey]) }}"
             class="btn {{ $statusFilter === $statusKey ? 'btn-primary' : 'btn-outline-primary' }}">
            {{ $label }}
            @if($statusKey !== 'all')
              <span class="ms-1">({{ $statusCounts[$statusKey] ?? 0 }})</span>
            @endif
          </a>
        @endforeach
      </div>
    </div>
  </div>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-check me-2" style="color:#0288d1;"></i>Upcoming Appointments</h5>
      <span class="badge bg-light text-dark">{{ $upcomingBookings->count() }} scheduled</span>
    </div>
    <div class="card-body p-0">
      @if($upcomingBookings->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Nurse</th>
                <th>Schedule</th>
                <th>Service</th>
                <th>Address</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($upcomingBookings as $booking)
                <tr>
                  <td>
                    <div class="fw-semibold">{{ $booking->nurse->name ?? 'N/A' }}</div>
                    <div class="text-muted small">{{ $booking->nurse->nurseProfile->specialization ?? 'General Nursing' }}</div>
                  </td>
                  <td>
                    <div>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</div>
                    <div class="text-muted small">{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</div>
                  </td>
                  <td>{{ $booking->service_type }}</td>
                  <td>{{ $booking->service_address }}</td>
                  <td>
                    @if($booking->status === 'pending')
                      <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($booking->status === 'accepted')
                      <span class="badge bg-info">Accepted</span>
                    @endif
                  </td>
                  <td>
                    @if(in_array($booking->status, ['pending', 'accepted'], true))
                      <form action="{{ route('patient.booking.cancel', $booking) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this booking?')">Cancel</button>
                      </form>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-center py-5">
          <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
          <p class="text-muted mb-0">No upcoming appointments match the current filter.</p>
        </div>
      @endif
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold"><i class="fas fa-clock-rotate-left me-2" style="color:#0288d1;"></i>Booking History</h5>
      <span class="badge bg-light text-dark">{{ $pastBookings->count() }} records</span>
    </div>
    <div class="card-body p-0">
      @if($pastBookings->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Nurse</th>
                <th>Schedule</th>
                <th>Service</th>
                <th>Address</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pastBookings as $booking)
                <tr>
                  <td>
                    <div class="fw-semibold">{{ $booking->nurse->name ?? 'N/A' }}</div>
                    <div class="text-muted small">{{ $booking->nurse->nurseProfile->specialization ?? 'General Nursing' }}</div>
                  </td>
                  <td>
                    <div>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</div>
                    <div class="text-muted small">{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</div>
                  </td>
                  <td>{{ $booking->service_type }}</td>
                  <td>{{ $booking->service_address }}</td>
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
                    @if($booking->status === 'completed' && !$booking->review)
                      <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $booking->id }}">
                        <i class="fas fa-star me-1"></i>Review
                      </button>
                      <div class="modal fade" id="reviewModal{{ $booking->id }}" tabindex="-1">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title">Write a Review</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('patient.review.store') }}" method="POST">
                              @csrf
                              <div class="modal-body">
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                <div class="mb-3">
                                  <label class="form-label">Rating</label>
                                  <select name="rating" class="form-select" required>
                                    <option value="5">&#11088;&#11088;&#11088;&#11088;&#11088; Excellent</option>
                                    <option value="4">&#11088;&#11088;&#11088;&#11088; Good</option>
                                    <option value="3">&#11088;&#11088;&#11088; Average</option>
                                    <option value="2">&#11088;&#11088; Poor</option>
                                    <option value="1">&#11088; Very Poor</option>
                                  </select>
                                </div>
                                <div class="mb-3">
                                  <label class="form-label">Comment</label>
                                  <textarea name="comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    @elseif($booking->review)
                      <span class="badge bg-success"><i class="fas fa-check me-1"></i>Reviewed</span>
                    @else
                      <span class="text-muted small">No action</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-center py-5">
          <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
          <p class="text-muted">No booking history matches the current filter.</p>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
