@extends('layouts.app')
@section('title', 'Nurse Dashboard - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="fw-bold mb-0">Welcome, {{ auth()->user()->name }}!</h3>
      <p class="text-muted">Manage your appointments</p>
    </div>
    <a href="{{ route('nurse.profile') }}" class="btn btn-outline-primary"><i class="fas fa-user-edit me-2"></i>Update Profile</a>
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

  <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2" style="color:#0288d1;"></i>My Appointments</h5>
    </div>
    <div class="card-body p-0">
      @if($bookings->count() > 0)
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>Patient</th>
              <th>Date</th>
              <th>Time</th>
              <th>Service</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($bookings as $booking)
            <tr>
              <td>{{ $booking->patient->name ?? 'N/A' }}</td>
              <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>
              <td>{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</td>
              <td>{{ $booking->service_type }}</td>
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
                @if($booking->status === 'pending')
                  <form action="{{ route('nurse.booking.accept', $booking) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">Accept</button>
                  </form>
                @elseif($booking->status === 'accepted')
                  <form action="{{ route('nurse.booking.complete', $booking) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-primary">Complete</button>
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
        <p class="text-muted">No appointments yet.</p>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection
