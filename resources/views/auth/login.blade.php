@extends('layouts.app')
@section('title', 'Login - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-sm border-0">
        <div class="card-body p-5">
          <div class="text-center mb-4">
            <i class="fas fa-heartbeat fa-3x mb-2" style="color:#0288d1;"></i>
            <h4 class="fw-bold">Welcome Back</h4>
            <p class="text-muted">Login to NurseSheba</p>
          </div>
          <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-semibold">Email Address</label>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Password</label>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
              @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" name="remember" id="remember">
              <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg">Login</button>
          </form>
          <div class="text-center mt-3">
            <p class="text-muted">Don't have an account? <a href="{{ route('register') }}" style="color:#0288d1;">Register here</a></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
