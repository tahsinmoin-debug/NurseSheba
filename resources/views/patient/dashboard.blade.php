@extends('layouts.app')
@section('title', 'Patient Dashboard - NurseSheba')
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
      <p class="text-muted mb-0">Track upcoming visits, review past appointments, and manage booking requests.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('patient.complaints') }}" class="btn btn-outline-danger">
        <i class="fas fa-flag me-2"></i>My Complaints
      </a>
      <a href="{{ route('patient.payment.history') }}" class="btn btn-outline-primary">
        <i class="fas fa-history me-2"></i>Payment History
      </a>
      <a href="{{ route('nurses.index') }}" class="btn btn-primary">
        <i class="fas fa-search me-2"></i>Find a Nurse
      </a>
    </div>
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

  {{-- Upcoming Appointments --}}
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
                <th>Duration</th>
                <th>Status</th>
                <th>Payment</th>
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
                      @if($booking->payment->payment_status !== 'unpaid')
                        <div class="text-muted small">{{ $booking->payment->formatted_amount }}</div>
                      @endif
                    @else
                      <span class="text-muted small">—</span>
                    @endif
                  </td>
                  <td>
                    <div class="d-flex gap-1 flex-wrap">
                      {{-- Pay Now button for accepted + unpaid --}}
                      @if($booking->status === 'accepted' && $booking->payment && $booking->payment->payment_status === 'unpaid')
                        <a href="{{ route('patient.payment.show', $booking) }}" class="btn btn-sm btn-primary">
                          <i class="fas fa-credit-card me-1"></i>Pay Now
                        </a>
                      @endif
                      {{-- Invoice button for paid --}}
                      @if($booking->payment && $booking->payment->payment_status === 'paid')
                        <a href="{{ route('patient.invoice', $booking->payment) }}" class="btn btn-sm btn-outline-success">
                          <i class="fas fa-file-invoice me-1"></i>Invoice
                        </a>
                      @endif
                      {{-- Cancel button --}}
                      @if(in_array($booking->status, ['pending', 'accepted'], true))
                        <form action="{{ route('patient.booking.cancel', $booking) }}" method="POST" class="d-inline">
                          @csrf
                          <button type="submit" class="btn btn-sm btn-outline-danger"
                                  onclick="return confirm('Cancel this booking?')">Cancel</button>
                        </form>
                      @endif
                    </div>
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

  {{-- Booking History --}}
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
                <th>Duration</th>
                <th>Status</th>
                <th>Payment</th>
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
                    <div class="d-flex gap-1 flex-wrap">
                      {{-- Invoice --}}
                      @if($booking->payment && $booking->payment->payment_status === 'paid')
                        <a href="{{ route('patient.invoice', $booking->payment) }}" class="btn btn-sm btn-outline-success">
                          <i class="fas fa-file-invoice me-1"></i>Invoice
                        </a>
                      @endif
                      {{-- Pay Now (unpaid accepted) --}}
                      @if($booking->status === 'accepted' && $booking->payment && $booking->payment->payment_status === 'unpaid')
                        <a href="{{ route('patient.payment.show', $booking) }}" class="btn btn-sm btn-primary">
                          <i class="fas fa-credit-card me-1"></i>Pay Now
                        </a>
                      @endif
                      {{-- Review --}}
                      @if($booking->status === 'completed' && !$booking->review)
                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $booking->id }}">
                          <i class="fas fa-star me-1"></i>Review
                        </button>
                        @include('patient._review_modal', ['booking' => $booking])
                      @elseif($booking->review)
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Reviewed</span>
                      @endif
                      {{-- Report / Complaint --}}
                      @if($booking->status === 'completed')
                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#complaintModal{{ $booking->id }}">
                          <i class="fas fa-flag me-1"></i>Report
                        </button>
                        @include('patient._complaint_modal', ['booking' => $booking])
                      @endif
                      {{-- No action fallback --}}
                      @if(!$booking->payment && !in_array($booking->status, ['completed','cancelled']))
                        <span class="text-muted small">No action</span>
                      @endif
                    </div>
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
