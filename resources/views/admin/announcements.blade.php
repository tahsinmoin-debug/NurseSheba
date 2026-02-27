@extends('layouts.app')
@section('title', 'Announcements - NurseSheba Admin')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Announcements</h3>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">&larr; Back</a>
  </div>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold"><i class="fas fa-plus me-2" style="color:#0288d1;"></i>Create Announcement</h5>
    </div>
    <div class="card-body p-4">
      <form action="{{ route('admin.announcements.store') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label class="form-label fw-semibold">Title</label>
          <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
          @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Message</label>
          <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="4" required>{{ old('message') }}</textarea>
          @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary">Publish Announcement</button>
      </form>
    </div>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold">Recent Announcements</h5>
    </div>
    <div class="card-body p-0">
      @forelse($announcements as $ann)
      <div class="p-4 border-bottom">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h6 class="fw-bold mb-1">{{ $ann->title }}</h6>
            <p class="text-muted mb-0">{{ $ann->message }}</p>
          </div>
          <small class="text-muted text-nowrap ms-3">{{ $ann->created_at->format('d M Y') }}</small>
        </div>
      </div>
      @empty
      <div class="text-center py-4 text-muted">No announcements yet.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection
