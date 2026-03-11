@extends('layouts.app')
@section('title', 'My Profile - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow-sm border-0">
        <div class="card-body p-5">
          <h4 class="fw-bold mb-4"><i class="fas fa-user-edit me-2" style="color:#0288d1;"></i>Update Profile</h4>
          <form action="{{ route('nurse.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-semibold">Qualification</label>
              <input type="text" name="qualification" class="form-control @error('qualification') is-invalid @enderror" value="{{ old('qualification', $profile->qualification ?? '') }}" placeholder="e.g. BSc in Nursing" required>
              @error('qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Gender</label>
              <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                <option value="">Select gender</option>
                <option value="male" {{ old('gender', $profile->gender ?? '') === 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $profile->gender ?? '') === 'female' ? 'selected' : '' }}>Female</option>
              </select>
              @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Specialization</label>
              <input type="text" name="specialization" class="form-control @error('specialization') is-invalid @enderror" value="{{ old('specialization', $profile->specialization ?? '') }}" placeholder="e.g. ICU, Elderly Care, Pediatrics" required>
              @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Years of Experience</label>
              <input type="number" name="experience_years" class="form-control @error('experience_years') is-invalid @enderror" value="{{ old('experience_years', $profile->experience_years ?? 0) }}" min="0" required>
              @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Location (Dhaka Area)</label>
              <select name="location" class="form-select @error('location') is-invalid @enderror" required>
                <option value="">Select your area</option>
                @foreach($locations as $location)
                  <option value="{{ $location }}" {{ old('location', auth()->user()->location ?? $profile->thana ?? '') == $location ? 'selected' : '' }}>{{ $location }}</option>
                @endforeach
              </select>
              @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Bio</label>
              <textarea name="bio" class="form-control" rows="4" placeholder="Tell patients about your experience and expertise...">{{ old('bio', $profile->bio ?? '') }}</textarea>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">License/Certificate Upload</label>
              <input type="file" name="license_document" class="form-control @error('license_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
              @if(!empty($profile?->license_document))
                <small class="text-muted d-block mt-1">Current file: {{ basename($profile->license_document) }}</small>
              @endif
              @error('license_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="availability" id="availability" {{ ($profile && !$profile->availability) ? '' : 'checked' }}>
                <label class="form-check-label fw-semibold" for="availability">Available for Bookings</label>
              </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100">Save Profile</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
