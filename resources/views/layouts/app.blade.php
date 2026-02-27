<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'NurseSheba')</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #4fc3f7; --primary-dark: #0288d1; }
        .navbar { background: linear-gradient(135deg, #0288d1, #4fc3f7) !important; }
        .btn-primary { background-color: #0288d1; border-color: #0288d1; }
        .btn-primary:hover { background-color: #0277bd; border-color: #0277bd; }
        .text-primary { color: #0288d1 !important; }
        footer { background: #1a237e; color: #fff; }
    </style>
    @stack('styles')
    @yield('head')
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container">
    <a class="navbar-brand fw-bold" href="/"><i class="fas fa-heartbeat me-2"></i>NurseSheba</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('nurses.index') }}">Find Nurses</a></li>
      </ul>
      <ul class="navbar-nav">
        @auth
          <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
          <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-outline-light btn-sm ms-2">Logout</button>
            </form>
          </li>
        @else
          <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
          <li class="nav-item"><a class="btn btn-light btn-sm ms-2 nav-link" href="{{ route('register') }}">Register</a></li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

<main>
  @if(session('success'))
    <div class="container mt-3"><div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
  @endif
  @if(session('error'))
    <div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
  @endif
  @yield('content')
</main>

<footer class="py-4 mt-5">
  <div class="container text-center">
    <p class="mb-0">&copy; {{ date('Y') }} NurseSheba - Home Nurse Service Platform for Bangladesh</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
@yield('scripts')
</body>
</html>
