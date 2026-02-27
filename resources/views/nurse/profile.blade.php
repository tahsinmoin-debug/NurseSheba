@extends('layouts.app')
@section('title', 'My Profile - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow-sm border-0">
        <div class="card-body p-5">
          <h4 class="fw-bold mb-4"><i class="fas fa-user-edit me-2" style="color:#0288d1;"></i>Update Profile</h4>
          <form action="{{ route('nurse.profile.update') }}" method="POST">
            @csrf
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
              <label class="form-label fw-semibold">District</label>
              <select name="district" class="form-select @error('district') is-invalid @enderror" required>
                <option value="">Select District</option>
                @foreach($districts as $district)
                  <option value="{{ $district }}" {{ old('district', $profile->district ?? '') == $district ? 'selected' : '' }}>{{ $district }}</option>
                @endforeach
              </select>
              @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Thana/Upazila</label>
              <input type="text" name="thana" class="form-control @error('thana') is-invalid @enderror" value="{{ old('thana', $profile->thana ?? '') }}" required>
              @error('thana')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Bio</label>
              <textarea name="bio" class="form-control" rows="4" placeholder="Tell patients about your experience and expertise...">{{ old('bio', $profile->bio ?? '') }}</textarea>
            </div>
            <div class="mb-4">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="availability" id="availability" {{ old('availability') !== null ? (old('availability') ? 'checked' : '') : (isset($profile) && $profile && !$profile->availability ? '' : 'checked') }}>
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
