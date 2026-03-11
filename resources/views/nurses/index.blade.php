@extends('layouts.app')
@section('title', 'Find Nurses - NurseSheba')
@section('content')
<div class="container py-5">
  <h2 class="fw-bold mb-4" style="color:#0288d1;">Find Nurses</h2>

  <!-- Filters -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <form action="{{ route('nurses.index') }}" method="GET" class="row g-3">
        <div class="col-md-5">
          <select name="location" class="form-select">
            <option value="">All Dhaka Areas</option>
            @foreach($locations as $location)
              <option value="{{ $location }}" {{ request('location') == $location ? 'selected' : '' }}>{{ $location }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-5">
          <input type="text" name="specialization" class="form-control" placeholder="Specialization..." value="{{ request('specialization') }}">
        </div>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Nurses Grid -->
  @if($nurses->count() > 0)
  <div class="row g-4">
    @foreach($nurses as $nurse)
    <div class="col-md-4 col-lg-3">
      <div class="card h-100 shadow-sm border-0">
        <div class="card-body text-center p-3">
          <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width:60px;height:60px;background:linear-gradient(135deg,#4fc3f7,#0288d1);">
            <i class="fas fa-user-nurse fa-lg text-white"></i>
          </div>
          <h6 class="fw-bold mb-1">{{ $nurse->name }}</h6>
          <p class="small text-muted mb-1">{{ $nurse->nurseProfile->specialization ?? 'General Nursing' }}</p>
          <p class="small text-muted mb-1"><i class="fas fa-map-marker-alt me-1"></i>{{ $nurse->location ?? $nurse->nurseProfile->thana ?? 'N/A' }}</p>
          <p class="small text-muted mb-2"><i class="fas fa-briefcase me-1"></i>{{ $nurse->nurseProfile->experience_years ?? 0 }} yrs</p>
          @if($nurse->nurseProfile && $nurse->nurseProfile->is_approved)
            <span class="badge bg-success mb-2">Approved</span>
          @endif
          <div class="d-grid gap-1">
            <a href="{{ route('nurses.show', $nurse->id) }}" class="btn btn-sm btn-outline-primary">View Profile</a>
            @auth
              @if(auth()->user()->role === 'patient')
                <a href="{{ route('patient.book', $nurse->id) }}" class="btn btn-sm btn-primary">Book Now</a>
              @endif
            @else
              <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Book Now</a>
            @endauth
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
  <div class="mt-4">{{ $nurses->links() }}</div>
  @else
  <div class="text-center py-5">
    <i class="fas fa-user-nurse fa-3x text-muted mb-3"></i>
    <p class="text-muted">No nurses found matching your criteria.</p>
  </div>
  @endif
</div>
@endsection
