@extends('layouts.app')
@section('title', 'Earnings Summary - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-0"><i class="fas fa-chart-line me-2" style="color:#0288d1;"></i>Earnings Summary</h3>
      <p class="text-muted mb-0">Your income overview from NurseSheba bookings</p>
    </div>
    <a href="{{ route('nurse.dashboard') }}" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i> Dashboard
    </a>
  </div>

  {{-- Summary Cards --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm" style="border-left:4px solid #0288d1 !important;">
        <div class="card-body d-flex align-items-center gap-3 p-4">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:54px;height:54px;background:linear-gradient(135deg,#0288d1,#4fc3f7);">
            <i class="fas fa-wallet text-white fa-lg"></i>
          </div>
          <div>
            <p class="text-muted small mb-0">Total Earned</p>
            <h4 class="fw-bold mb-0 text-primary">৳{{ number_format($totalEarned, 2) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm" style="border-left:4px solid #ffc107 !important;">
        <div class="card-body d-flex align-items-center gap-3 p-4">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:54px;height:54px;background:linear-gradient(135deg,#ffc107,#ffd54f);">
            <i class="fas fa-hourglass-half text-white fa-lg"></i>
          </div>
          <div>
            <p class="text-muted small mb-0">Awaiting Payment</p>
            <h4 class="fw-bold mb-0 text-warning">৳{{ number_format($pendingAmount, 2) }}</h4>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm" style="border-left:4px solid #28a745 !important;">
        <div class="card-body d-flex align-items-center gap-3 p-4">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:54px;height:54px;background:linear-gradient(135deg,#28a745,#6fcf7f);">
            <i class="fas fa-clipboard-list text-white fa-lg"></i>
          </div>
          <div>
            <p class="text-muted small mb-0">Total Bookings</p>
            <h4 class="fw-bold mb-0 text-success">{{ $totalBookings }}</h4>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Monthly Summary --}}
  @if($monthlySummary->count() > 0)
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i>Monthly Earnings</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Month</th>
              <th>Bookings Paid</th>
              <th>Earnings</th>
              <th>Progress</th>
            </tr>
          </thead>
          <tbody>
            @php $maxMonthly = $monthlySummary->max('total') ?: 1; @endphp
            @foreach($monthlySummary as $month)
            <tr>
              <td class="fw-semibold">{{ $month['month'] }}</td>
              <td>{{ $month['count'] }} bookings</td>
              <td class="fw-bold text-primary">৳{{ number_format($month['total'], 2) }}</td>
              <td style="width:200px;">
                <div class="progress" style="height:8px;">
                  <div class="progress-bar" style="width:{{ ($month['total']/$maxMonthly)*100 }}%;background:linear-gradient(135deg,#0288d1,#4fc3f7);"></div>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  @endif

  {{-- All Earnings Breakdown --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Booking Earnings Detail</h5>
    </div>
    <div class="card-body p-0">
      @if($payments->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Invoice</th>
                <th>Patient</th>
                <th>Service</th>
                <th>Date</th>
                <th>Duration</th>
                <th>Rate/hr</th>
                <th>Amount</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($payments as $payment)
              <tr>
                <td><span class="badge bg-light text-dark small">{{ $payment->invoice_number }}</span></td>
                <td>{{ $payment->booking->patient->name ?? 'N/A' }}</td>
                <td>{{ $payment->booking->service_type }}</td>
                <td>{{ \Carbon\Carbon::parse($payment->booking->date)->format('d M Y') }}</td>
                <td>{{ $payment->duration_hours }}hr</td>
                <td class="text-muted small">৳{{ number_format($payment->nurse_hourly_rate, 0) }}</td>
                <td class="fw-bold">৳{{ number_format($payment->amount, 2) }}</td>
                <td>
                  <span class="badge bg-{{ $payment->status_color }}">
                    {{ ucfirst($payment->payment_status) }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="text-center py-5">
          <i class="fas fa-coins fa-3x text-muted mb-3"></i>
          <p class="text-muted">No earnings records yet. Start accepting bookings!</p>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
