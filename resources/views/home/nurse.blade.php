@extends('layouts.app')
@section('title', 'For Nurses - NurseSheba')
@section('content')
<div style="background: linear-gradient(135deg, #ffffff 0%, #e0f4ff 100%); padding: 80px 0;">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <h1 class="display-5 fw-bold mb-3" style="color: #0288d1;">Grow Your Nursing Career With NurseSheba</h1>
        <p class="lead text-muted mb-4">Join a trusted home-care platform, serve patients across Dhaka, and manage your schedule from one dashboard.</p>
        <div class="d-flex flex-wrap gap-3">
          <a href="{{ route('register', ['role' => 'nurse']) }}" class="btn btn-primary btn-lg">
            <i class="fas fa-user-nurse me-2"></i>Register as Nurse
          </a>
          @auth
            @if(auth()->user()->role === 'nurse')
              <a href="{{ route('nurse.dashboard') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-gauge-high me-2"></i>Go to Dashboard
              </a>
            @endif
          @else
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
              <i class="fas fa-right-to-bracket me-2"></i>Nurse Login
            </a>
          @endauth
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-shield-heart me-2 text-primary"></i>Why Nurses Choose Us</h5>
            <ul class="mb-0 text-muted">
              <li class="mb-2">Consistent booking flow from real patient requests</li>
              <li class="mb-2">Clear profile and approval process</li>
              <li class="mb-2">Simple appointment management for daily work</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@if(auth()->check() && auth()->user()->role === 'nurse')
<div class="container my-5">
  <div class="card border-0 shadow-sm">
    <div class="card-body p-4">
      <h4 class="fw-bold mb-4" style="color: #0288d1;">Your Quick Actions</h4>
      <div class="row g-3">
        <div class="col-md-4">
          <div class="p-3 border rounded h-100">
            <h6 class="fw-bold"><i class="fas fa-gauge-high me-2 text-primary"></i>Appointments</h6>
            <p class="text-muted mb-3">Manage booking requests and update status from your dashboard.</p>
            <a href="{{ route('nurse.dashboard') }}" class="btn btn-primary btn-sm">Open Dashboard</a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 border rounded h-100">
            <h6 class="fw-bold"><i class="fas fa-user-pen me-2 text-primary"></i>Profile</h6>
            <p class="text-muted mb-3">Update specialization, experience, documents, and service area.</p>
            <a href="{{ route('nurse.profile') }}" class="btn btn-outline-primary btn-sm">Edit Profile</a>
          </div>
        </div>
        <div class="col-md-4">
          <div class="p-3 border rounded h-100">
            <h6 class="fw-bold"><i class="fas fa-toggle-on me-2 text-primary"></i>Availability Status</h6>
            @if(!$nurseContext || !$nurseContext['has_profile'])
              <p class="mb-3 text-warning">Your profile is incomplete. Complete profile setup to start receiving bookings.</p>
            @elseif(!$nurseContext['is_approved'])
              <p class="mb-3 text-warning">Your profile is pending approval. You can still update details now.</p>
            @elseif($nurseContext['is_available'])
              <p class="mb-3 text-success">You are currently marked as available for new bookings.</p>
            @else
              <p class="mb-3 text-muted">You are currently marked as unavailable for new bookings.</p>
            @endif
            <a href="{{ route('nurse.profile') }}" class="btn btn-outline-primary btn-sm">Update Availability</a>
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
@endsection
