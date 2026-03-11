@extends('layouts.app')
@section('title', 'Verify OTP - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm border-0">
        <div class="card-body p-5">
          <div class="text-center mb-4">
            <i class="fas fa-shield-alt fa-3x mb-2" style="color:#0288d1;"></i>
            <h4 class="fw-bold">Verify OTP</h4>
            <p class="text-muted">Enter the 6-digit code sent to your email</p>
          </div>
          <form method="POST" action="{{ route('password.otp.verify') }}">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email) }}">
            <div class="mb-3">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" class="form-control" value="{{ old('email', $email) }}" disabled>
            </div>
            <div class="mb-4">
              <label class="form-label fw-semibold">OTP Code</label>
              <input type="text" name="otp" class="form-control @error('otp') is-invalid @enderror" value="{{ old('otp') }}" maxlength="6" required>
              @error('otp')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">Verify OTP</button>
          </form>
          <div class="text-center mt-3">
            <a href="{{ route('password.otp.request.form') }}" style="color:#0288d1;">Resend OTP</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
