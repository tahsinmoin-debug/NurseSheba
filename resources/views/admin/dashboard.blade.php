@extends('layouts.app')
@section('title', 'Admin Dashboard - NurseSheba')
@section('content')
<div class="container py-5">
  <h3 class="fw-bold mb-4">Admin Dashboard</h3>

  <!-- Stats Cards -->
  <div class="row g-4 mb-5">
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center p-3" style="border-top: 4px solid #0288d1 !important;">
        <i class="fas fa-user-nurse fa-2x mb-2" style="color:#0288d1;"></i>
        <h3 class="fw-bold">{{ $stats['total_nurses'] }}</h3>
        <p class="text-muted mb-0">Total Nurses</p>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center p-3" style="border-top: 4px solid #22c55e !important;">
        <i class="fas fa-users fa-2x mb-2 text-success"></i>
        <h3 class="fw-bold">{{ $stats['total_patients'] }}</h3>
        <p class="text-muted mb-0">Total Patients</p>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center p-3" style="border-top: 4px solid #f59e0b !important;">
        <i class="fas fa-calendar-check fa-2x mb-2 text-warning"></i>
        <h3 class="fw-bold">{{ $stats['total_bookings'] }}</h3>
        <p class="text-muted mb-0">Total Bookings</p>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card border-0 shadow-sm text-center p-3" style="border-top: 4px solid #ef4444 !important;">
        <i class="fas fa-clock fa-2x mb-2 text-danger"></i>
        <h3 class="fw-bold">{{ $stats['pending_approvals'] }}</h3>
        <p class="text-muted mb-0">Pending Approvals</p>
      </div>
    </div>
  </div>

  <!-- Quick Links -->
  <div class="row g-3 mb-4">
    <div class="col-auto"><a href="{{ route('admin.nurses') }}" class="btn btn-outline-primary"><i class="fas fa-user-nurse me-2"></i>Manage Nurses</a></div>
    <div class="col-auto"><a href="{{ route('admin.patients') }}" class="btn btn-outline-success"><i class="fas fa-users me-2"></i>Manage Patients</a></div>
    <div class="col-auto"><a href="{{ route('admin.bookings') }}" class="btn btn-outline-warning"><i class="fas fa-calendar me-2"></i>View Bookings</a></div>
    <div class="col-auto"><a href="{{ route('admin.complaints') }}" class="btn btn-outline-danger"><i class="fas fa-flag me-2"></i>Complaints</a></div>
    <div class="col-auto"><a href="{{ route('admin.support') }}" class="btn btn-outline-secondary"><i class="fas fa-headset me-2"></i>Support</a></div>
  </div>

  <!-- Pending Approvals -->
  @if($pendingNurses->count() > 0)
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold"><i class="fas fa-clock me-2 text-warning"></i>Pending Nurse Approvals</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr><th>Name</th><th>Specialization</th><th>Location</th><th>Action</th></tr>
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
    </div>
  </div>
  @endif
</div>
@endsection
