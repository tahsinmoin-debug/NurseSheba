@extends('layouts.app')
@section('title', 'Complaints - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-0"><i class="fas fa-flag me-2 text-danger"></i>Complaints</h3>
      <p class="text-muted mb-0">View complaints you've filed and complaints filed against you.</p>
    </div>
    <a href="{{ route('nurse.dashboard') }}" class="btn btn-outline-secondary">&larr; Back to Dashboard</a>
  </div>

  {{-- Complaints Filed by Nurse --}}
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold"><i class="fas fa-paper-plane me-2" style="color:#0288d1;"></i>Complaints I Filed</h5>
      <span class="badge bg-light text-dark">{{ $filed->count() }} complaint{{ $filed->count() !== 1 ? 's' : '' }}</span>
    </div>
    <div class="card-body p-0">
      @if($filed->count() > 0)
        @foreach($filed as $complaint)
          <div class="border-bottom p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
              <div>
                <span class="badge bg-{{ $complaint->status_color }} me-2">{{ ucfirst(str_replace('_', ' ', $complaint->status)) }}</span>
                <span class="badge bg-light text-dark border">{{ $complaint->complaint_type }}</span>
              </div>
              <small class="text-muted">{{ $complaint->created_at->format('d M Y, h:i A') }}</small>
            </div>

            <div class="mb-2">
              <strong>Against:</strong> {{ $complaint->nurse->name ?? 'N/A' }}
              @if($complaint->booking)
                <span class="text-muted ms-2">| Booking #{{ $complaint->booking_id }} — {{ $complaint->booking->service_type }}</span>
              @endif
            </div>

            <p class="mb-2 text-muted">{{ $complaint->message }}</p>

            @if($complaint->admin_reply)
              <div class="mt-3 p-3 bg-success bg-opacity-10 rounded border-start border-3 border-success">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <strong class="text-success"><i class="fas fa-shield-alt me-1"></i>Admin Reply</strong>
                  <small class="text-muted">{{ $complaint->replied_at ? $complaint->replied_at->format('d M Y, h:i A') : '' }}</small>
                </div>
                <p class="mb-0">{{ $complaint->admin_reply }}</p>
              </div>
            @else
              <div class="mt-2">
                <small class="text-muted"><i class="fas fa-clock me-1"></i>Awaiting admin review...</small>
              </div>
            @endif
          </div>
        @endforeach
      @else
        <div class="text-center py-5">
          <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
          <p class="text-muted mb-0">You haven't filed any complaints.</p>
        </div>
      @endif
    </div>
  </div>

  {{-- Complaints Filed Against This Nurse --}}
  @if($against->count() > 0)
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Complaints Against Me</h5>
      <span class="badge bg-light text-dark">{{ $against->count() }} complaint{{ $against->count() !== 1 ? 's' : '' }}</span>
    </div>
    <div class="card-body p-0">
      @foreach($against as $complaint)
        <div class="border-bottom p-4">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
            <div>
              <span class="badge bg-{{ $complaint->status_color }} me-2">{{ ucfirst(str_replace('_', ' ', $complaint->status)) }}</span>
              <span class="badge bg-light text-dark border">{{ $complaint->complaint_type }}</span>
            </div>
            <small class="text-muted">{{ $complaint->created_at->format('d M Y, h:i A') }}</small>
          </div>

          <div class="mb-2">
            <strong>From:</strong> {{ $complaint->user->name ?? 'N/A' }}
            @if($complaint->booking)
              <span class="text-muted ms-2">| Booking #{{ $complaint->booking_id }} — {{ $complaint->booking->service_type }}</span>
            @endif
          </div>

          <p class="mb-2 text-muted">{{ $complaint->message }}</p>

          @if($complaint->admin_reply)
            <div class="mt-3 p-3 bg-success bg-opacity-10 rounded border-start border-3 border-success">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <strong class="text-success"><i class="fas fa-shield-alt me-1"></i>Admin Feedback</strong>
                <small class="text-muted">{{ $complaint->replied_at ? $complaint->replied_at->format('d M Y, h:i A') : '' }}</small>
              </div>
              <p class="mb-0">{{ $complaint->admin_reply }}</p>
            </div>
          @else
            <div class="mt-2">
              <small class="text-muted"><i class="fas fa-clock me-1"></i>Under admin review...</small>
            </div>
          @endif
        </div>
      @endforeach
    </div>
  </div>
  @endif
</div>
@endsection
