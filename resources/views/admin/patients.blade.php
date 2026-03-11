@extends('layouts.app')
@section('title', 'Manage Patients - NurseSheba Admin')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Manage Patients</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">&larr; Back</a>
  </div>
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr><th>Name</th><th>Email</th><th>Phone</th><th>Location</th><th>Address</th><th>Registered</th></tr>
          </thead>
          <tbody>
            @forelse($patients as $patient)
            <tr>
              <td>{{ $patient->name }}</td>
              <td>{{ $patient->email }}</td>
              <td>{{ $patient->phone }}</td>
              <td>{{ $patient->location ?? 'N/A' }}</td>
              <td>{{ $patient->address }}</td>
              <td>{{ $patient->created_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-4 text-muted">No patients found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    @if($patients->hasPages())
    <div class="card-footer bg-white">{{ $patients->links() }}</div>
    @endif
  </div>
</div>
@endsection
