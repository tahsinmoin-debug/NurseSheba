@extends('layouts.app')
@section('title', auth()->check() && auth()->user()->role === 'nurse' ? 'Nurse Home - NurseSheba' : 'For Nurses - NurseSheba')
@section('content')
@php
  $isLoggedInNurse = auth()->check() && auth()->user()->role === 'nurse';
  $profileActionLabel = !$nurseContext || !$nurseContext['has_profile'] ? 'Complete Profile' : 'Edit Profile';
@endphp

<div style="background: linear-gradient(135deg, #ffffff 0%, #e0f4ff 100%); padding: 80px 0;">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        @if($isLoggedInNurse)
          <span class="badge rounded-pill text-bg-light border mb-3 px-3 py-2">Nurse Home</span>
          <h1 class="display-5 fw-bold mb-3" style="color: #0288d1;">Welcome back, {{ auth()->user()->name }}</h1>
          <p class="lead text-muted mb-4">
            @if(!$nurseContext || !$nurseContext['has_profile'])
              Complete your profile to start receiving patient requests.
            @elseif(!$nurseContext['is_approved'])
              Your profile is under admin review. Keep your details updated so approval is not delayed.
            @elseif($nurseContext['is_available'])
              You are visible for new bookings. Review your requests and keep your schedule current.
            @else
              Your account is currently unavailable for new bookings. Update availability when you are ready to accept work again.
            @endif
          </p>
          <div class="d-flex flex-wrap gap-3">
            <a href="{{ route('nurse.dashboard') }}" class="btn btn-primary btn-lg">
              <i class="fas fa-gauge-high me-2"></i>Open Dashboard
            </a>
            <a href="{{ route('nurse.profile') }}" class="btn btn-outline-primary btn-lg">
              <i class="fas fa-user-pen me-2"></i>{{ $profileActionLabel }}
            </a>
          </div>
        @else
          <h1 class="display-5 fw-bold mb-3" style="color: #0288d1;">Grow Your Nursing Career With NurseSheba</h1>
          <p class="lead text-muted mb-4">Join a trusted home-care platform, serve patients across Dhaka, and manage your schedule from one dashboard.</p>
          <div class="d-flex flex-wrap gap-3">
            @guest
              <a href="{{ route('register', ['role' => 'nurse']) }}" class="btn btn-primary btn-lg">
                <i class="fas fa-user-nurse me-2"></i>Register as Nurse
              </a>
              <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-right-to-bracket me-2"></i>Nurse Login
              </a>
            @else
              <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                <i class="fas fa-house me-2"></i>Go to Home
              </a>
              <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-gauge-high me-2"></i>Open Dashboard
              </a>
            @endguest
          </div>
        @endif
      </div>
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            @if($isLoggedInNurse)
              <h5 class="fw-bold mb-3"><i class="fas fa-briefcase-medical me-2 text-primary"></i>Your Current Status</h5>
              <ul class="mb-0 text-muted">
                <li class="mb-2">
                  @if(!$nurseContext || !$nurseContext['has_profile'])
                    Profile setup is incomplete.
                  @elseif($nurseContext['is_approved'])
                    Profile approval: approved.
                  @else
                    Profile approval: pending review.
                  @endif
                </li>
                <li class="mb-2">
                  @if($nurseContext && $nurseContext['has_profile'])
                    Availability:
                    {{ $nurseContext['is_available'] ? 'available for new bookings' : 'not accepting new bookings' }}.
                  @else
                    Availability updates will unlock after profile setup.
                  @endif
                </li>
                <li class="mb-0">
                  @if($nextBooking)
                    Next booking: {{ \Carbon\Carbon::parse($nextBooking->date)->format('d M Y') }} at {{ \Carbon\Carbon::parse($nextBooking->time)->format('h:i A') }} with {{ $nextBooking->patient->name ?? 'a patient' }}.
                  @else
                    No active booking is scheduled yet.
                  @endif
                </li>
              </ul>
            @else
              <h5 class="fw-bold mb-3"><i class="fas fa-shield-heart me-2 text-primary"></i>Why Nurses Choose Us</h5>
              <ul class="mb-0 text-muted">
                <li class="mb-2">Consistent booking flow from real patient requests</li>
                <li class="mb-2">Clear profile and approval process</li>
                <li class="mb-2">Simple appointment management for daily work</li>
              </ul>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if($isLoggedInNurse)
<div class="container my-5">
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <h4 class="fw-bold mb-4" style="color: #0288d1;">Your Quick Actions</h4>
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="p-3 border rounded h-100">
            <h6 class="fw-bold"><i class="fas fa-hourglass-half me-2 text-primary"></i>Pending Requests</h6>
            <p class="text-muted mb-2">{{ $bookingSummary['pending'] ?? 0 }} booking requests need your attention.</p>
            <a href="{{ route('nurse.dashboard') }}" class="btn btn-primary btn-sm">Review Requests</a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 border rounded h-100">
            <h6 class="fw-bold"><i class="fas fa-calendar-check me-2 text-primary"></i>Accepted Visits</h6>
            <p class="text-muted mb-2">{{ $bookingSummary['accepted'] ?? 0 }} appointments are currently active.</p>
            <a href="{{ route('nurse.dashboard') }}" class="btn btn-outline-primary btn-sm">Manage Visits</a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 border rounded h-100">
            <h6 class="fw-bold"><i class="fas fa-user-pen me-2 text-primary"></i>Profile & Availability</h6>
            <p class="text-muted mb-2">Keep specialization, documents, and availability updated.</p>
            <a href="{{ route('nurse.profile') }}" class="btn btn-outline-primary btn-sm">{{ $profileActionLabel }}</a>
          </div>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="p-3 rounded text-white h-100" style="background:linear-gradient(135deg,#0288d1,#4fc3f7);">
            <div class="small text-uppercase opacity-75 mb-2">Pending</div>
            <div class="display-6 fw-bold">{{ $bookingSummary['pending'] ?? 0 }}</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 rounded h-100 border" style="background:#f8fbff;">
            <div class="small text-uppercase text-muted mb-2">Accepted</div>
            <div class="display-6 fw-bold text-primary">{{ $bookingSummary['accepted'] ?? 0 }}</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 rounded h-100 border" style="background:#f8fbff;">
            <div class="small text-uppercase text-muted mb-2">Completed</div>
            <div class="display-6 fw-bold text-primary">{{ $bookingSummary['completed'] ?? 0 }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<div style="background: #f8f9fa;" class="py-5">
  <div class="container">
    <h2 class="text-center fw-bold mb-4" style="color: #0288d1;">Trusted Platform Stats</h2>
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3">
        <h2 class="fw-bold display-6">{{ $approvedNurseCount }}</h2>
        <p class="text-muted mb-0">Approved Nurses</p>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="fw-bold display-6">{{ $completedBookingCount }}</h2>
        <p class="text-muted mb-0">Completed Bookings</p>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="fw-bold display-6">{{ $patientCount }}</h2>
        <p class="text-muted mb-0">Registered Patients</p>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="fw-bold display-6">{{ $coveredAreaCount }}</h2>
        <p class="text-muted mb-0">Dhaka Areas Covered</p>
      </div>
    </div>
  </div>
</div>

@if(!$isLoggedInNurse)
<div class="container py-5">
  <h2 class="text-center fw-bold mb-5" style="color: #0288d1;">How To Start As A Nurse</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-4 text-center">
          <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;background:#e0f4ff;">
            <i class="fas fa-user-plus fa-xl text-primary"></i>
          </div>
          <h5 class="fw-bold">1. Register</h5>
          <p class="text-muted mb-0">Create your nurse account and submit your professional details.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-4 text-center">
          <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;background:#e0f4ff;">
            <i class="fas fa-file-circle-check fa-xl text-primary"></i>
          </div>
          <h5 class="fw-bold">2. Get Approved</h5>
          <p class="text-muted mb-0">Our team reviews your profile and activates your service visibility.</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-4 text-center">
          <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;background:#e0f4ff;">
            <i class="fas fa-calendar-check fa-xl text-primary"></i>
          </div>
          <h5 class="fw-bold">3. Accept Bookings</h5>
          <p class="text-muted mb-0">Respond to patient requests and manage appointments from your dashboard.</p>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection
