@extends('layouts.app')
@section('title', 'Patient Home - NurseSheba')
@section('content')
<div style="background: linear-gradient(135deg, #ffffff 0%, #e0f4ff 100%); padding: 80px 0;">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <span class="badge rounded-pill text-bg-light border mb-3 px-3 py-2">Patient Home</span>
        <h1 class="display-5 fw-bold mb-3" style="color: #0288d1;">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="lead text-muted mb-4">
          @if($nextBooking)
            Your next booking is on {{ \Carbon\Carbon::parse($nextBooking->date)->format('d M Y') }} at {{ \Carbon\Carbon::parse($nextBooking->time)->format('h:i A') }} with {{ $nextBooking->nurse->name ?? 'your nurse' }}.
          @else
            Search approved nurses, compare profiles, and manage your home-care bookings from one place.
          @endif
        </p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="{{ route('nurses.index') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-search me-2"></i>Find Nurses
          </a>
          <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-calendar-check me-2"></i>My Bookings
          </a>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-notes-medical me-2 text-primary"></i>Your Booking Snapshot</h5>
            <ul class="mb-0 text-muted">
              <li class="mb-2">{{ $patientSummary['pending'] ?? 0 }} booking requests are awaiting confirmation.</li>
              <li class="mb-2">{{ $patientSummary['accepted'] ?? 0 }} visits are currently confirmed.</li>
              <li class="mb-2">{{ $patientSummary['completed'] ?? 0 }} visits have been completed so far.</li>
              <li class="mb-0">{{ $patientSummary['reviews_pending'] ?? 0 }} completed visits are still waiting for your review.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container my-5">
  <div class="row g-4">
    <div class="col-md-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="small text-uppercase text-muted mb-2">Total Bookings</div>
          <div class="display-6 fw-bold text-primary">{{ $patientSummary['total'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="small text-uppercase text-muted mb-2">Pending</div>
          <div class="display-6 fw-bold text-primary">{{ $patientSummary['pending'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="small text-uppercase text-muted mb-2">Accepted</div>
          <div class="display-6 fw-bold text-primary">{{ $patientSummary['accepted'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card h-100 border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="small text-uppercase text-muted mb-2">Completed</div>
          <div class="display-6 fw-bold text-primary">{{ $patientSummary['completed'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container my-5">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h4 class="mb-0 fw-bold" style="color: #0288d1;">Quick Search</h4>
        <a href="{{ route('patient.dashboard') }}" class="btn btn-sm btn-outline-primary">Open Full Dashboard</a>
      </div>
      <form action="{{ route('nurses.index') }}" method="GET" class="row g-3">
        <div class="col-md-5">
          <select name="location" class="form-select form-select-lg">
            <option value="">Select Dhaka Area</option>
            @foreach($locations as $location)
              <option value="{{ $location }}">{{ $location }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-5">
          <input type="text" name="specialization" class="form-control form-control-lg" placeholder="Specialization (e.g. ICU, Elderly Care)">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary btn-lg w-100"><i class="fas fa-search"></i> Search</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="container my-5">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <h4 class="fw-bold mb-4" style="color: #0288d1;">Recent Booking Activity</h4>
      @if($recentBookings->count() > 0)
        <div class="row g-3">
          @foreach($recentBookings as $booking)
            <div class="col-md-4">
              <div class="p-3 border rounded h-100">
                <h6 class="fw-bold mb-2">{{ $booking->nurse->name ?? 'Nurse not assigned' }}</h6>
                <p class="text-muted mb-2">{{ $booking->service_type }}</p>
                <p class="mb-2">{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }} at {{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</p>
                <span class="badge
                  @if($booking->status === 'pending') bg-warning text-dark
                  @elseif($booking->status === 'accepted') bg-info
                  @elseif($booking->status === 'completed') bg-success
                  @else bg-danger
                  @endif">
                  {{ ucfirst($booking->status) }}
                </span>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="text-center py-4">
          <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
          <p class="text-muted mb-3">You have not created a booking yet.</p>
          <a href="{{ route('nurses.index') }}" class="btn btn-primary">Browse Approved Nurses</a>
        </div>
      @endif
    </div>
  </div>
</div>

@if($featuredNurses->count() > 0)
<div style="background: #f8f9fa;" class="py-5">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      <h2 class="fw-bold mb-0" style="color: #0288d1;">Suggested Nurses</h2>
      <a href="{{ route('nurses.index') }}" class="btn btn-outline-primary">View All Nurses</a>
    </div>
    <div class="row g-4">
      @foreach($featuredNurses as $nurse)
      <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body text-center p-4">
            <div class="mb-3">
              <div class="rounded-circle d-inline-flex align-items-center justify-content-center" style="width:80px;height:80px;background:linear-gradient(135deg,#4fc3f7,#0288d1);">
                <i class="fas fa-user-nurse fa-2x text-white"></i>
              </div>
            </div>
            <h5 class="fw-bold">{{ $nurse->name }}</h5>
            <p class="text-muted mb-1"><i class="fas fa-stethoscope me-1"></i>{{ $nurse->nurseProfile->specialization ?? 'General Nursing' }}</p>
            <p class="text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>{{ $nurse->location ?? $nurse->nurseProfile->thana ?? 'N/A' }}</p>
            <p class="text-muted mb-3"><i class="fas fa-briefcase me-1"></i>{{ $nurse->nurseProfile->experience_years ?? 0 }} years exp.</p>
            <a href="{{ route('nurses.show', $nurse->id) }}" class="btn btn-primary btn-sm">View Profile</a>
            <a href="{{ route('patient.book', $nurse->id) }}" class="btn btn-outline-primary btn-sm">Book Now</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endif
@endsection
