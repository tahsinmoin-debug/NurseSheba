@extends('layouts.app')
@section('title', 'NurseSheba - Quality Home Nursing Services')
@section('content')
<!-- Hero Section -->
<div style="background: linear-gradient(135deg, #ffffff 0%, #e0f4ff 100%); padding: 80px 0;">
  <div class="container text-center">
    <h1 class="display-4 fw-bold mb-3" style="color: #0288d1;">Quality Home Nursing Services<br>Across Dhaka</h1>
    <p class="lead mb-4 text-muted">Professional nurses available at your doorstep. Safe, reliable, and affordable healthcare at home.</p>
    <div class="d-flex gap-3 justify-content-center flex-wrap">
      <a href="{{ route('nurses.index') }}" class="btn btn-primary btn-lg"><i class="fas fa-search me-2"></i>Find Nurses</a>
      <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg"><i class="fas fa-user-plus me-2"></i>Register as Patient</a>
      <a href="{{ route('home.nurse') }}" class="btn btn-outline-primary btn-lg"><i class="fas fa-user-nurse me-2"></i>For Nurses</a>
    </div>
  </div>
</div>

<!-- Search Section -->
<div class="container my-5">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">
      <h4 class="mb-3 text-center" style="color: #0288d1;"><i class="fas fa-search me-2"></i>Find a Nurse Near You</h4>
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

<!-- Featured Nurses -->
@if($featuredNurses->count() > 0)
<div class="container my-5">
  <h2 class="text-center mb-4 fw-bold" style="color: #0288d1;">Featured Nurses</h2>
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
          @auth
            @if(auth()->user()->role === 'patient')
              <a href="{{ route('patient.book', $nurse->id) }}" class="btn btn-outline-primary btn-sm">Book Now</a>
            @endif
          @else
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Book Now</a>
          @endauth
        </div>
      </div>
    </div>
    @endforeach
  </div>
  <div class="text-center mt-4">
    <a href="{{ route('nurses.index') }}" class="btn btn-outline-primary btn-lg">View All Nurses</a>
  </div>
</div>
@endif

<!-- How it Works -->
<div style="background: #f8f9fa;" class="py-5">
  <div class="container">
    <h2 class="text-center fw-bold mb-5" style="color: #0288d1;">How It Works</h2>
    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="p-4">
          <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;background:#e0f4ff;">
            <i class="fas fa-search fa-2x" style="color:#0288d1;"></i>
          </div>
          <h4 class="fw-bold">1. Search</h4>
          <p class="text-muted">Find qualified nurses by Dhaka area or specialization</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4">
          <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;background:#e0f4ff;">
            <i class="fas fa-calendar-check fa-2x" style="color:#0288d1;"></i>
          </div>
          <h4 class="fw-bold">2. Book</h4>
          <p class="text-muted">Schedule your appointment at a convenient time</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="p-4">
          <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;background:#e0f4ff;">
            <i class="fas fa-heartbeat fa-2x" style="color:#0288d1;"></i>
          </div>
          <h4 class="fw-bold">3. Care</h4>
          <p class="text-muted">Receive professional nursing care at your home</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Stats -->
<div style="background:linear-gradient(135deg,#0288d1,#4fc3f7);" class="py-5 text-white">
  <div class="container">
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3">
        <h2 class="fw-bold display-5">500+</h2>
        <p>Qualified Nurses</p>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="fw-bold display-5">10K+</h2>
        <p>Happy Patients</p>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="fw-bold display-5">{{ count($locations) }}</h2>
        <p>Dhaka Areas Covered</p>
      </div>
      <div class="col-6 col-md-3">
        <h2 class="fw-bold display-5">4.8★</h2>
        <p>Average Rating</p>
      </div>
    </div>
  </div>
</div>
@endsection
