@extends('layouts.app')
@section('title', $nurse->name . ' - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="row">
    <div class="col-md-4 mb-4">
      <div class="card shadow-sm border-0">
        <div class="card-body text-center p-4">
          <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:100px;height:100px;background:linear-gradient(135deg,#4fc3f7,#0288d1);">
            <i class="fas fa-user-nurse fa-3x text-white"></i>
          </div>
          <h4 class="fw-bold">{{ $nurse->name }}</h4>
          @if($nurse->nurseProfile && $nurse->nurseProfile->is_approved)
            <span class="badge bg-success mb-2">Verified Nurse</span>
          @endif
          <p class="text-muted"><i class="fas fa-stethoscope me-1"></i>{{ $nurse->nurseProfile->specialization ?? 'General Nursing' }}</p>
          <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $nurse->nurseProfile->district ?? 'N/A' }}, {{ $nurse->nurseProfile->thana ?? '' }}</p>
          <p class="text-muted"><i class="fas fa-briefcase me-1"></i>{{ $nurse->nurseProfile->experience_years ?? 0 }} years experience</p>
          @if($nurse->nurseProfile && $nurse->nurseProfile->availability)
            <span class="badge bg-success">Available</span>
          @else
            <span class="badge bg-danger">Not Available</span>
          @endif
          <div class="mt-3">
            @auth
              @if(auth()->user()->role === 'patient')
                <a href="{{ route('patient.book', $nurse->id) }}" class="btn btn-primary w-100"><i class="fas fa-calendar-plus me-2"></i>Book Now</a>
              @endif
            @else
              <a href="{{ route('login') }}" class="btn btn-primary w-100"><i class="fas fa-calendar-plus me-2"></i>Book Now</a>
            @endauth
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-8">
      @if($nurse->nurseProfile && $nurse->nurseProfile->bio)
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3"><i class="fas fa-user me-2" style="color:#0288d1;"></i>About</h5>
          <p>{{ $nurse->nurseProfile->bio }}</p>
        </div>
      </div>
      @endif

      <!-- Reviews -->
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3"><i class="fas fa-star me-2" style="color:#f59e0b;"></i>Reviews ({{ $reviews->count() }})</h5>
          @forelse($reviews as $review)
          <div class="border-bottom pb-3 mb-3">
            <div class="d-flex align-items-center mb-1">
              @for($i = 1; $i <= 5; $i++)
                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
              @endfor
              <span class="ms-2 text-muted small">{{ $review->created_at->diffForHumans() }}</span>
            </div>
            @if($review->comment)
              <p class="mb-0 text-muted">{{ $review->comment }}</p>
            @endif
          </div>
          @empty
          <p class="text-muted">No reviews yet.</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
