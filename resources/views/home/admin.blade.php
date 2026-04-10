@extends('layouts.app')
@section('title', 'Admin Home - NurseSheba')
@section('content')
<div style="background: linear-gradient(135deg, #f8fbff 0%, #e0f4ff 100%); padding: 80px 0;">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-7">
        <span class="badge rounded-pill text-bg-light border mb-3 px-3 py-2">Admin Home</span>
        <h1 class="display-5 fw-bold mb-3" style="color: #0288d1;">Platform overview for {{ auth()->user()->name }}</h1>
        <p class="lead text-muted mb-4">Monitor approvals, bookings, support, and patient operations from the homepage without dropping into the full dashboard first.</p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="{{ route('admin.nurses') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-user-nurse me-2"></i>Review Nurses
          </a>
          <a href="{{ route('admin.bookings') }}" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-calendar-check me-2"></i>View Bookings
          </a>
          <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-gauge-high me-2"></i>Open Dashboard
          </a>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-triangle-exclamation me-2 text-primary"></i>Needs Attention</h5>
            <ul class="mb-0 text-muted">
              <li class="mb-2">{{ $stats['pending_approvals'] }} nurse profiles are waiting for approval.</li>
              <li class="mb-2">{{ $operations['support_requests'] }} support requests are in the queue.</li>
              <li class="mb-2">{{ $operations['complaints'] }} complaint records exist in the system.</li>
              <li class="mb-0">{{ $operations['announcements'] }} announcements have been published so far.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container my-5">
  <div class="row g-4">
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm text-center h-100">
        <div class="card-body p-4">
          <i class="fas fa-user-nurse fa-2x mb-3 text-primary"></i>
          <div class="display-6 fw-bold">{{ $stats['total_nurses'] }}</div>
          <p class="text-muted mb-0">Total Nurses</p>
        </div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm text-center h-100">
        <div class="card-body p-4">
          <i class="fas fa-users fa-2x mb-3 text-success"></i>
          <div class="display-6 fw-bold">{{ $stats['total_patients'] }}</div>
          <p class="text-muted mb-0">Total Patients</p>
        </div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm text-center h-100">
        <div class="card-body p-4">
          <i class="fas fa-calendar-days fa-2x mb-3 text-warning"></i>
          <div class="display-6 fw-bold">{{ $stats['total_bookings'] }}</div>
          <p class="text-muted mb-0">Total Bookings</p>
        </div>
      </div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card border-0 shadow-sm text-center h-100">
        <div class="card-body p-4">
          <i class="fas fa-clock fa-2x mb-3 text-danger"></i>
          <div class="display-6 fw-bold">{{ $stats['pending_approvals'] }}</div>
          <p class="text-muted mb-0">Pending Approvals</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container my-5">
  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h4 class="fw-bold mb-0" style="color: #0288d1;">Pending Nurse Approvals</h4>
            <a href="{{ route('admin.nurses') }}" class="btn btn-sm btn-outline-primary">Manage All Nurses</a>
          </div>
          @if($pendingNurses->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Name</th>
                    <th>Specialization</th>
                    <th>Location</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($pendingNurses as $nurse)
                    <tr>
                      <td>{{ $nurse->name }}</td>
                      <td>{{ $nurse->nurseProfile->specialization ?? 'N/A' }}</td>
                      <td>{{ $nurse->location ?? $nurse->nurseProfile->thana ?? 'N/A' }}</td>
                      <td>
                        <form action="{{ route('admin.nurses.approve', $nurse->id) }}" method="POST" class="d-inline">
                          @csrf
                          <button class="btn btn-sm btn-success">Approve</button>
                        </form>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-center py-4">
              <i class="fas fa-circle-check fa-3x text-success mb-3"></i>
              <p class="text-muted mb-0">There are no pending nurse approvals right now.</p>
            </div>
          @endif
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h4 class="fw-bold mb-0" style="color: #0288d1;">Recent Support Requests</h4>
            <a href="{{ route('admin.support') }}" class="btn btn-sm btn-outline-primary">Open Support</a>
          </div>
          @if($recentSupportRequests->count() > 0)
            <div class="list-group list-group-flush">
              @foreach($recentSupportRequests as $request)
                <div class="list-group-item px-0">
                  <div class="fw-bold">{{ $request->subject }}</div>
                  <div class="text-muted small mb-1">From {{ $request->user->name ?? 'Unknown user' }}</div>
                  <div class="small">{{ \Illuminate\Support\Str::limit($request->message, 110) }}</div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-4">
              <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
              <p class="text-muted mb-0">No support requests have been submitted yet.</p>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
