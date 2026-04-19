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

          {{-- Average Rating Display --}}
          <div class="mb-3">
            @if($reviewCount > 0)
              <div class="d-flex align-items-center justify-content-center gap-2 mb-1">
                <span class="fw-bold fs-4" style="color:#f59e0b;">{{ $averageRating }}</span>
                <div>
                  @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= round($averageRating) ? 'text-warning' : 'text-muted' }}"></i>
                  @endfor
                </div>
              </div>
              <small class="text-muted">{{ $reviewCount }} review{{ $reviewCount !== 1 ? 's' : '' }}</small>
            @else
              <div class="text-muted">
                <i class="fas fa-star text-muted"></i> No reviews yet
              </div>
            @endif
          </div>

          <p class="text-muted"><i class="fas fa-stethoscope me-1"></i>{{ $nurse->nurseProfile->specialization ?? 'General Nursing' }}</p>
          <p class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>{{ $nurse->location ?? $nurse->nurseProfile->thana ?? 'N/A' }}, Dhaka</p>
          <p class="text-muted"><i class="fas fa-venus-mars me-1"></i>{{ ucfirst($nurse->nurseProfile->gender ?? 'N/A') }}</p>
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

      {{-- Rating Summary --}}
      @if($reviewCount > 0)
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2" style="color:#0288d1;"></i>Rating Summary</h5>
          <div class="row align-items-center">
            <div class="col-md-4 text-center">
              <div class="display-3 fw-bold" style="color:#f59e0b;">{{ $averageRating }}</div>
              <div class="mb-1">
                @for($i = 1; $i <= 5; $i++)
                  <i class="fas fa-star {{ $i <= round($averageRating) ? 'text-warning' : 'text-muted' }}"></i>
                @endfor
              </div>
              <small class="text-muted">{{ $reviewCount }} total review{{ $reviewCount !== 1 ? 's' : '' }}</small>
            </div>
            <div class="col-md-8">
              @for($star = 5; $star >= 1; $star--)
                @php
                  $count = $reviews->where('rating', $star)->count();
                  $percent = $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0;
                @endphp
                <div class="d-flex align-items-center mb-1">
                  <span class="small me-2" style="width: 20px;">{{ $star }}★</span>
                  <div class="progress flex-grow-1" style="height: 8px;">
                    <div class="progress-bar bg-warning" style="width: {{ $percent }}%"></div>
                  </div>
                  <span class="small ms-2 text-muted" style="width: 30px;">{{ $count }}</span>
                </div>
              @endfor
            </div>
          </div>
        </div>
      </div>
      @endif

      <!-- Reviews -->
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <h5 class="fw-bold mb-3"><i class="fas fa-star me-2" style="color:#f59e0b;"></i>Reviews ({{ $reviews->count() }})</h5>
          @forelse($reviews as $review)
          <div class="border-bottom pb-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <div class="d-flex align-items-center">
                @for($i = 1; $i <= 5; $i++)
                  <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                @endfor
                <span class="ms-2 fw-semibold">{{ $review->patient_name ?? 'Patient' }}</span>
              </div>
              <span class="text-muted small">{{ $review->created_at->diffForHumans() }}</span>
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
