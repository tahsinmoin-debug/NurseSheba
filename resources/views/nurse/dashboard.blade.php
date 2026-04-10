@extends('layouts.app')
@section('title', 'Nurse Dashboard - NurseSheba')
@section('content')
@php
  $statusLabels = [
    'all'       => 'All',
    'pending'   => 'Pending',
    'accepted'  => 'Accepted',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled',
  ];
@endphp

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-0">Welcome, {{ auth()->user()->name }}!</h3>
      <p class="text-muted mb-0">Review booking requests, manage confirmed visits, and track booking history.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('nurse.earnings') }}" class="btn btn-outline-success">
        <i class="fas fa-chart-line me-2"></i>Earnings
      </a>
      <a href="{{ route('nurse.profile') }}" class="btn btn-outline-primary">
        <i class="fas fa-user-edit me-2"></i>Update Profile
      </a>
    </div>
  </div>

  @if(!auth()->user()->nurseProfile || !auth()->user()->nurseProfile->is_approved)
    <div class="alert alert-warning border-0 shadow-sm mb-4">
      <i class="fas fa-exclamation-triangle me-2"></i>
      @if(!auth()->user()->nurseProfile || !auth()->user()->nurseProfile->specialization)
        Your profile is incomplete. <a href="{{ route('nurse.profile') }}" class="alert-link">Complete your profile</a> to start receiving bookings.
      @else
        Your profile is pending admin approval. You will start receiving bookings once approved.
      @endif
    </div>
  @endif

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h5 class="fw-bold mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter By Status</h5>
        <span class="text-muted small">Current filter: {{ $statusLabels[$statusFilter] ?? 'All' }}</span>
      </div>
      <div class="d-flex flex-wrap gap-2">
        @foreach($statusLabels as $statusKey => $label)
          <a href="{{ route('nurse.dashboard', ['status' => $statusKey === 'all' ? null : $statusKey]) }}"
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

  {{-- Upcoming Appointments --}}
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-check me-2" style="color:#0288d1;"></i>Upcoming Appointments</h5>
      <span class="badge bg-light text-dark">{{ $upcomingBookings->count() }} active</span>
    </div>
    <div class="card-body p-0">
      @if($upcomingBookings->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Patient</th>
                <th>Schedule</th>
                <th>Service</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($upcomingBookings as $booking)
                <tr>
                  <td>{{ $booking->patient->name ?? 'N/A' }}</td>
                  <td>
                    <div>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</div>
                    <div class="text-muted small">{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</div>
                  </td>
                  <td>{{ $booking->service_type }}</td>
                  <td>{{ $booking->duration_hours ?? 1 }}hr</td>
                  <td>
                    @if($booking->status === 'pending')
                      <span class="badge bg-warning text-dark">Pending</span>
                    @elseif($booking->status === 'accepted')
                      <span class="badge bg-info">Accepted</span>
                    @endif
                  </td>
                  <td>
                    @if($booking->payment)
                      <span class="badge bg-{{ $booking->payment->status_color }}">
                        {{ ucfirst($booking->payment->payment_status) }}
                      </span>
                      @if($booking->payment->payment_status === 'paid')
                        <div class="text-success small fw-semibold">{{ $booking->payment->formatted_amount }}</div>
                      @endif
                    @else
                      <span class="text-muted small">—</span>
                    @endif
                  </td>
                  <td>
                    @if($booking->status === 'pending')
                      <div class="d-flex gap-2 flex-wrap">
                        <form action="{{ route('nurse.booking.accept', $booking) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-success">Accept</button>
                        </form>
                        <form action="{{ route('nurse.booking.reject', $booking) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-danger"
                                  onclick="return confirm('Reject this booking request?')">Reject</button>
                        </form>
                      </div>
                    @elseif($booking->status === 'accepted')
                      <div class="d-flex gap-2 flex-wrap">
                        @if($booking->payment && $booking->payment->payment_status === 'paid')
                          <form action="{{ route('nurse.booking.complete', $booking) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">Mark Completed</button>
                          </form>
                        @else
                          <span class="text-muted small">Waiting for payment</span>
                        @endif
                      </div>
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
          <p class="text-muted mb-0">No active appointments match the current filter.</p>
        </div>
      @endif
    </div>
  </div>

  {{-- Appointment History --}}
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold"><i class="fas fa-clock-rotate-left me-2" style="color:#0288d1;"></i>Appointment History</h5>
      <span class="badge bg-light text-dark">{{ $pastBookings->count() }} records</span>
    </div>
    <div class="card-body p-0">
      @if($pastBookings->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Patient</th>
                <th>Schedule</th>
                <th>Service</th>
                <th>Duration</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pastBookings as $booking)
                <tr>
                  <td>{{ $booking->patient->name ?? 'N/A' }}</td>
                  <td>
                    <div>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</div>
                    <div class="text-muted small">{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</div>
                  </td>
                  <td>{{ $booking->service_type }}</td>
                  <td>{{ $booking->duration_hours ?? 1 }}hr</td>
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
                    @if($booking->payment)
                      <span class="badge bg-{{ $booking->payment->status_color }}">
                        {{ ucfirst($booking->payment->payment_status) }}
                      </span>
                      <div class="text-muted small">{{ $booking->payment->formatted_amount }}</div>
                    @else
                      <span class="text-muted small">—</span>
                    @endif
                  </td>
                  <td>
                    @if($booking->status === 'accepted' && $booking->payment && $booking->payment->payment_status === 'paid')
                      <form action="{{ route('nurse.booking.complete', $booking) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-primary">Mark Completed</button>
                      </form>
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
          <p class="text-muted">No appointment history matches the current filter.</p>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
