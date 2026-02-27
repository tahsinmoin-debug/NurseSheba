@extends('layouts.app')
@section('title', 'Manage Nurses - NurseSheba Admin')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Manage Nurses</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">&larr; Back</a>
  </div>
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Specialization</th><th>District</th><th>Status</th><th>Actions</th></tr>
          </thead>
          <tbody>
            @forelse($nurses as $nurse)
            <tr>
              <td>{{ $nurse->name }}</td>
              <td>{{ $nurse->email }}</td>
              <td>{{ $nurse->phone }}</td>
              <td>{{ $nurse->nurseProfile->specialization ?? 'N/A' }}</td>
              <td>{{ $nurse->nurseProfile->district ?? 'N/A' }}</td>
              <td>
                @if($nurse->nurseProfile && $nurse->nurseProfile->is_approved)
                  <span class="badge bg-success">Approved</span>
                @else
                  <span class="badge bg-warning text-dark">Pending</span>
                @endif
              </td>
              <td>
                @if($nurse->nurseProfile && !$nurse->nurseProfile->is_approved)
                  <form action="{{ route('admin.nurses.approve', $nurse->id) }}" method="POST" class="d-inline">
                    @csrf <button class="btn btn-sm btn-success">Approve</button>
                  </form>
                @else
                  <form action="{{ route('admin.nurses.reject', $nurse->id) }}" method="POST" class="d-inline">
                    @csrf <button class="btn btn-sm btn-outline-danger">Revoke</button>
                  </form>
                @endif
              </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-muted">No nurses found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($nurses->hasPages())
    <div class="card-footer bg-white">{{ $nurses->links() }}</div>
    @endif
  </div>
</div>
@endsection
