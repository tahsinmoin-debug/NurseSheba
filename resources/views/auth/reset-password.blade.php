@extends('layouts.app')
@section('title', 'Reset Password - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm border-0">
        <div class="card-body p-5">
          <div class="text-center mb-4">
            <i class="fas fa-lock fa-3x mb-2" style="color:#0288d1;"></i>
            <h4 class="fw-bold">Set New Password</h4>
            <p class="text-muted">Create a new secure password</p>
          </div>
          <form method="POST" action="{{ route('password.otp.reset') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="mb-3">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" class="form-control" value="{{ $email }}" disabled>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">New Password</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-4">
              <label class="form-label fw-semibold">Confirm New Password</label>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">Reset Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
