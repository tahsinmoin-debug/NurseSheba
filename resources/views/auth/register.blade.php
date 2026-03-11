@extends('layouts.app')
@section('title', 'Register - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-7">
      <div class="card shadow-sm border-0">
        <div class="card-body p-5">
          <div class="text-center mb-4">
            <i class="fas fa-user-plus fa-3x mb-2" style="color:#0288d1;"></i>
            <h4 class="fw-bold">Create Account</h4>
          </div>

          <!-- Role Selection -->
          <div class="row g-3 mb-4" id="roleCards">
            <div class="col-6">
              <div class="card border-2 text-center p-3 role-card" data-role="patient" id="patientCard" style="cursor:pointer;">
                <i class="fas fa-user fa-2x mb-2" style="color:#0288d1;"></i>
                <h6 class="fw-bold mb-0">Register as Patient</h6>
                <small class="text-muted">Looking for nursing care</small>
              </div>
            </div>
            <div class="col-6">
              <div class="card border-2 text-center p-3 role-card" data-role="nurse" id="nurseCard" style="cursor:pointer;">
                <i class="fas fa-user-nurse fa-2x mb-2" style="color:#0288d1;"></i>
                <h6 class="fw-bold mb-0">Register as Nurse</h6>
                <small class="text-muted">Provide nursing services</small>
              </div>
            </div>
          </div>

          <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="role" id="roleInput" value="{{ old('role', 'patient') }}">
            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif
            @if($errors->has('register'))
              <div class="alert alert-danger">{{ $errors->first('register') }}</div>
            @endif
            <div class="mb-3">
              <label class="form-label fw-semibold">Full Name</label>
              <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Phone Number</label>
              <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" placeholder="01XXXXXXXXX" required>
              @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Location (Dhaka Area)</label>
              <select name="location" class="form-select @error('location') is-invalid @enderror" required>
                <option value="">Select your area</option>
                @foreach($locations as $location)
                  <option value="{{ $location }}" {{ old('location') === $location ? 'selected' : '' }}>{{ $location }}</option>
                @endforeach
              </select>
              @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Address</label>
              <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}" required>
              @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div id="nurseFields" style="display:none;">
              <div class="mb-3">
                <label class="form-label fw-semibold">Qualification</label>
              <input type="text" name="qualification" id="qualification" class="form-control @error('qualification') is-invalid @enderror" value="{{ old('qualification') }}" placeholder="e.g. BSc in Nursing">
                @error('qualification')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Gender</label>
                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror">
                  <option value="">Select gender</option>
                  <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                  <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Years of Experience</label>
                <input type="number" name="experience_years" id="experience_years" class="form-control @error('experience_years') is-invalid @enderror" value="{{ old('experience_years') }}" min="0" placeholder="e.g. 3">
                @error('experience_years')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Specialization</label>
                <input type="text" name="specialization" id="specialization" class="form-control @error('specialization') is-invalid @enderror" value="{{ old('specialization') }}" placeholder="e.g. ICU, Elderly Care">
                @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">License/Certificate Upload</label>
                <input type="file" name="license_document" id="license_document" class="form-control @error('license_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                <small class="text-muted">Allowed: PDF, JPG, JPEG, PNG (max 5MB)</small>
                @error('license_document')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Password</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
              <small class="text-muted">Minimum 8 characters.</small>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
              <label class="form-label fw-semibold">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            @error('role')<div class="alert alert-danger">{{ $message }}</div>@enderror
            <button type="submit" class="btn btn-primary w-100 btn-lg">Create Account</button>
          </form>
          <div class="text-center mt-3">
            <p class="text-muted">Already have an account? <a href="{{ route('login') }}" style="color:#0288d1;">Login here</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function() {
  var role = '{{ old("role", "patient") }}';
  setActiveRole(role);

  $('.role-card').click(function() {
    var selectedRole = $(this).data('role');
    $('#roleInput').val(selectedRole);
    setActiveRole(selectedRole);
  });

  function setActiveRole(role) {
    $('.role-card').removeClass('border-primary').css('border-color', '');
    $('#' + role + 'Card').addClass('border-primary');
    var isNurse = role === 'nurse';
    $('#nurseFields').toggle(isNurse);
    $('#qualification, #gender, #experience_years, #specialization, #license_document').prop('disabled', !isNurse);
    $('#qualification, #gender, #experience_years, #specialization, #license_document').prop('required', isNurse);
  }
});
</script>
@endsection
