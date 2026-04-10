@extends('layouts.app')
@section('title', 'Invoice {{ $payment->invoice_number }} - NurseSheba')
@push('styles')
<style>
  .invoice-wrapper { max-width: 780px; margin: 0 auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.1); }
  .invoice-header { background: linear-gradient(135deg,#0288d1,#4fc3f7); color:#fff; border-radius:16px 16px 0 0; }
  .invoice-divider { border-top: 2px dashed #dee2e6; }
  .status-paid { background:#d1fae5; color:#065f46; }
  .status-unpaid { background:#fee2e2; color:#991b1b; }
  .status-refunded { background:#fef3c7; color:#92400e; }
  @media print {
    .no-print { display:none !important; }
    .invoice-wrapper { box-shadow:none; }
    body { background:#fff !important; }
  }
</style>
@endpush
@section('content')
<div class="container py-5">

  {{-- Action bar --}}
  <div class="d-flex justify-content-between align-items-center mb-4 no-print flex-wrap gap-2">
    <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i> Dashboard
    </a>
    <div class="d-flex gap-2">
      @if($payment->payment_status === 'paid' && auth()->user()->role === 'admin')
        <form action="{{ route('admin.payment.refund', $payment) }}" method="POST"
              onsubmit="return confirm('Are you sure you want to refund this payment?')">
          @csrf
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-undo me-1"></i> Refund Payment
          </button>
        </form>
      @endif
      <button onclick="window.print()" class="btn btn-primary">
        <i class="fas fa-print me-1"></i> Print / Save PDF
      </button>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success no-print"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
  @endif

  <div class="invoice-wrapper p-0">
    {{-- Header --}}
    <div class="invoice-header p-5">
      <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
        <div>
          <h2 class="mb-1 fw-bold"><i class="fas fa-heartbeat me-2"></i>NurseSheba</h2>
          <p class="mb-0 opacity-75">Home Nurse Service Platform</p>
          <p class="mb-0 opacity-75 small">Dhaka, Bangladesh · admin@nursesheba.com</p>
        </div>
        <div class="text-end">
          <h3 class="mb-1 fw-bold">INVOICE</h3>
          <p class="mb-0 opacity-90">{{ $payment->invoice_number }}</p>
          <p class="mb-0 opacity-75 small">Issued: {{ $payment->created_at->format('d M Y') }}</p>
        </div>
      </div>
    </div>

    <div class="p-5">
      {{-- Status Badge --}}
      <div class="text-center mb-4">
        @if($payment->payment_status === 'paid')
          <span class="badge status-paid px-4 py-2 fs-6 rounded-pill">
            <i class="fas fa-check-circle me-1"></i>PAID
          </span>
        @elseif($payment->payment_status === 'refunded')
          <span class="badge status-refunded px-4 py-2 fs-6 rounded-pill">
            <i class="fas fa-undo me-1"></i>REFUNDED
          </span>
        @else
          <span class="badge status-unpaid px-4 py-2 fs-6 rounded-pill">
            <i class="fas fa-clock me-1"></i>UNPAID
          </span>
        @endif
      </div>

      {{-- Bill To / From --}}
      <div class="row mb-4">
        <div class="col-md-6">
          <h6 class="text-muted text-uppercase small fw-bold mb-2">Billed To</h6>
          <p class="mb-1 fw-semibold">{{ $booking->patient->name }}</p>
          <p class="mb-1 text-muted small">{{ $booking->patient->email }}</p>
          <p class="mb-1 text-muted small">{{ $booking->patient->phone ?? 'N/A' }}</p>
        </div>
        <div class="col-md-6 text-md-end">
          <h6 class="text-muted text-uppercase small fw-bold mb-2">Service Provider</h6>
          <p class="mb-1 fw-semibold">{{ $booking->nurse->name }}</p>
          <p class="mb-1 text-muted small">{{ $booking->nurse->nurseProfile->specialization ?? 'General Nursing' }}</p>
          <p class="mb-1 text-muted small">{{ $booking->nurse->email }}</p>
        </div>
      </div>

      <div class="invoice-divider mb-4"></div>

      {{-- Service Details Table --}}
      <h6 class="text-muted text-uppercase small fw-bold mb-3">Service Details</h6>
      <div class="table-responsive mb-4">
        <table class="table table-borderless align-middle">
          <thead class="table-light">
            <tr>
              <th>Description</th>
              <th class="text-center">Duration</th>
              <th class="text-end">Rate/hr</th>
              <th class="text-end">Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="fw-semibold">{{ $booking->service_type }}</div>
                <div class="text-muted small">
                  <i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}
                  &nbsp;<i class="fas fa-clock me-1"></i>{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}
                </div>
                <div class="text-muted small">
                  <i class="fas fa-map-marker-alt me-1"></i>{{ $booking->service_address }}
                </div>
              </td>
              <td class="text-center">{{ $payment->duration_hours }} hr(s)</td>
              <td class="text-end">৳{{ number_format($payment->nurse_hourly_rate, 2) }}</td>
              <td class="text-end fw-semibold">৳{{ number_format($payment->amount, 2) }}</td>
            </tr>
          </tbody>
          <tfoot>
            <tr class="border-top">
              <td colspan="3" class="text-end fw-bold fs-5 pt-3">Total</td>
              <td class="text-end fw-bold fs-4 text-primary pt-3">৳{{ number_format($payment->amount, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>

      {{-- Payment Details --}}
      @if($payment->payment_status !== 'unpaid')
      <div class="invoice-divider mb-4"></div>
      <div class="row">
        <div class="col-md-6">
          <h6 class="text-muted text-uppercase small fw-bold mb-2">Payment Details</h6>
          <table class="table table-sm table-borderless mb-0">
            <tr>
              <td class="text-muted">Method</td>
              <td class="fw-semibold text-capitalize">
                @if($payment->payment_method === 'stripe')
                  <i class="fab fa-stripe text-primary me-1"></i>Stripe (Card)
                @elseif($payment->payment_method === 'bkash')
                  <span class="text-danger fw-bold">bKash</span>
                @else
                  <span class="text-warning fw-bold">Nagad</span>
                @endif
              </td>
            </tr>
            <tr>
              <td class="text-muted">Transaction ID</td>
              <td class="fw-semibold small">{{ $payment->transaction_id ?? 'N/A' }}</td>
            </tr>
            <tr>
              <td class="text-muted">Paid On</td>
              <td class="fw-semibold">{{ $payment->paid_at?->format('d M Y, h:i A') ?? 'N/A' }}</td>
            </tr>
          </table>
        </div>
        <div class="col-md-6 d-flex align-items-end justify-content-md-end mt-3 mt-md-0">
          <div class="text-center">
            <i class="fas fa-check-circle fa-3x text-success mb-2"></i>
            <p class="text-success fw-bold mb-0">Payment Verified</p>
          </div>
        </div>
      </div>
      @endif

      <div class="invoice-divider mt-4 mb-3"></div>
      <p class="text-muted small text-center mb-0">
        Thank you for choosing NurseSheba. Booking ID: #{{ $booking->id }}
        — For support, contact admin@nursesheba.com
      </p>
    </div>
  </div>
</div>
@endsection
