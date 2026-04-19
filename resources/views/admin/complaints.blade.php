@extends('layouts.app')
@section('title', 'Complaints - NurseSheba Admin')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h3 class="fw-bold"><i class="fas fa-flag me-2 text-danger"></i>Complaint Management</h3>
      <p class="text-muted mb-0">Review and respond to complaints from patients and nurses.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">&larr; Back</a>
  </div>

  {{-- Status Summary Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-3 text-center">
          <div class="small text-uppercase text-muted mb-1">Open</div>
          <div class="display-6 fw-bold text-danger">{{ $statusCounts['open'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-3 text-center">
          <div class="small text-uppercase text-muted mb-1">In Review</div>
          <div class="display-6 fw-bold text-warning">{{ $statusCounts['in_review'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-3 text-center">
          <div class="small text-uppercase text-muted mb-1">Resolved</div>
          <div class="display-6 fw-bold text-success">{{ $statusCounts['resolved'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body p-3 text-center">
          <div class="small text-uppercase text-muted mb-1">Closed</div>
          <div class="display-6 fw-bold text-secondary">{{ $statusCounts['closed'] ?? 0 }}</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
      <form action="{{ route('admin.complaints') }}" method="GET" class="row g-3">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Filter by Status</label>
          <select name="status" class="form-select">
            <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>All Statuses</option>
            @foreach(\App\Models\Complaint::STATUSES as $status)
              <option value="{{ $status }}" {{ $statusFilter === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-semibold">Filter by Type</label>
          <select name="type" class="form-select">
            <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>All Types</option>
            @foreach(\App\Models\Complaint::COMPLAINT_TYPES as $type)
              <option value="{{ $type }}" {{ $typeFilter === $type ? 'selected' : '' }}>{{ $type }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4 d-flex align-items-end gap-2">
          <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
          <a href="{{ route('admin.complaints') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
      </form>
    </div>
  </div>

  {{-- Complaints List --}}
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold">All Complaints</h5>
      <span class="badge bg-light text-dark">{{ $complaints->count() }} result{{ $complaints->count() !== 1 ? 's' : '' }}</span>
    </div>
    <div class="card-body p-0">
      @forelse($complaints as $complaint)
        <div class="border-bottom p-4">
          {{-- Header Row --}}
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
              <span class="badge bg-{{ $complaint->status_color }}">{{ ucfirst(str_replace('_', ' ', $complaint->status)) }}</span>
              <span class="badge bg-light text-dark border">{{ $complaint->complaint_type }}</span>
              <span class="badge bg-{{ $complaint->reporter_role === 'patient' ? 'primary' : 'info' }}">
                By {{ ucfirst($complaint->reporter_role) }}
              </span>
            </div>
            <small class="text-muted">{{ $complaint->created_at->format('d M Y, h:i A') }}</small>
          </div>

          {{-- Details --}}
          <div class="row mb-3">
            <div class="col-md-6">
              <p class="mb-1"><strong>Filed By:</strong> {{ $complaint->user->name ?? 'N/A' }} <small class="text-muted">({{ $complaint->user->email ?? '' }})</small></p>
              <p class="mb-1"><strong>Against:</strong> {{ $complaint->nurse->name ?? 'N/A' }} <small class="text-muted">({{ $complaint->nurse->email ?? '' }})</small></p>
            </div>
            <div class="col-md-6">
              @if($complaint->booking)
                <p class="mb-1"><strong>Booking:</strong> #{{ $complaint->booking_id }} — {{ $complaint->booking->service_type }}</p>
                <p class="mb-1"><strong>Booking Date:</strong> {{ \Carbon\Carbon::parse($complaint->booking->date)->format('d M Y') }}</p>
              @else
                <p class="mb-1 text-muted"><em>No linked booking</em></p>
              @endif
            </div>
          </div>

          {{-- Message --}}
          <div class="p-3 bg-light rounded mb-3">
            <strong class="d-block mb-1">Complaint Message:</strong>
            <p class="mb-0">{{ $complaint->message }}</p>
          </div>

          {{-- Existing Admin Reply --}}
          @if($complaint->admin_reply)
            <div class="p-3 bg-success bg-opacity-10 rounded border-start border-3 border-success mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <strong class="text-success"><i class="fas fa-reply me-1"></i>Your Reply</strong>
                <small class="text-muted">{{ $complaint->replied_at ? $complaint->replied_at->format('d M Y, h:i A') : '' }}</small>
              </div>
              <p class="mb-0">{{ $complaint->admin_reply }}</p>
            </div>
          @endif

          {{-- Admin Actions --}}
          <div class="d-flex flex-wrap gap-3 align-items-start">
            {{-- Reply Form --}}
            <div class="flex-grow-1">
              <form action="{{ route('admin.complaint.reply', $complaint) }}" method="POST">
                @csrf
                <div class="input-group">
                  <input type="text" name="admin_reply" class="form-control"
                         placeholder="{{ $complaint->admin_reply ? 'Update your reply...' : 'Write a reply to this complaint...' }}"
                         required maxlength="2000">
                  <button type="submit" class="btn btn-success">
                    <i class="fas fa-reply me-1"></i>{{ $complaint->admin_reply ? 'Update Reply' : 'Send Reply' }}
                  </button>
                </div>
              </form>
            </div>

            {{-- Status Update --}}
            <div>
              <form action="{{ route('admin.complaint.status', $complaint) }}" method="POST" class="d-flex gap-1">
                @csrf
                <select name="status" class="form-select form-select-sm" style="width: auto;">
                  @foreach(\App\Models\Complaint::STATUSES as $status)
                    <option value="{{ $status }}" {{ $complaint->status === $status ? 'selected' : '' }}>
                      {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                  @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">Update</button>
              </form>
            </div>
          </div>
        </div>
      @empty
        <div class="text-center py-5">
          <i class="fas fa-check-circle fa-3x text-muted mb-3"></i>
          <p class="text-muted">No complaints found matching the current filters.</p>
        </div>
      @endforelse
    </div>
  </div>
</div>
@endsection
