@extends('layouts.app')
@section('title', 'Forgot Password - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm border-0">
        <div class="card-body p-5">
          <div class="text-center mb-4">
            <i class="fas fa-key fa-3x mb-2" style="color:#0288d1;"></i>
            <h4 class="fw-bold">Forgot Password</h4>
            <p class="text-muted">Enter your email to receive an OTP code</p>
          </div>
          <form method="POST" action="{{ route('password.otp.send') }}">
            @csrf
            <div class="mb-4">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">Send OTP</button>
          </form>
          <div class="text-center mt-3">
            <a href="{{ route('login') }}" style="color:#0288d1;">Back to login</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
