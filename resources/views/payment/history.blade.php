@extends('layouts.app')
@section('title', 'Payment History - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
      <h3 class="fw-bold mb-0"><i class="fas fa-history me-2" style="color:#0288d1;"></i>Payment History</h3>
      <p class="text-muted mb-0">Track all your payments and download invoices</p>
    </div>
    <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary">
      <i class="fas fa-arrow-left me-1"></i> Dashboard
    </a>
  </div>

  {{-- Summary Cards --}}
  @php
    $allPayments   = $payments->getCollection();
    $totalPaid     = $allPayments->where('payment_status','paid')->sum('amount');
    $totalUnpaid   = $allPayments->where('payment_status','unpaid')->sum('amount');
    $totalRefunded = $allPayments->where('payment_status','refunded')->sum('amount');
  @endphp

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3 p-4">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:linear-gradient(135deg,#d1fae5,#6ee7b7);">
            <i class="fas fa-check-circle text-success"></i>
          </div>
          <div>
            <p class="text-muted small mb-0">Total Paid</p>
            <h5 class="fw-bold mb-0 text-success">৳{{ number_format($totalPaid, 2) }}</h5>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3 p-4">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:linear-gradient(135deg,#fee2e2,#fca5a5);">
            <i class="fas fa-clock text-danger"></i>
          </div>
          <div>
            <p class="text-muted small mb-0">Pending Payment</p>
            <h5 class="fw-bold mb-0 text-danger">৳{{ number_format($totalUnpaid, 2) }}</h5>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm">
        <div class="card-body d-flex align-items-center gap-3 p-4">
          <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;background:linear-gradient(135deg,#fef3c7,#fcd34d);">
            <i class="fas fa-undo text-warning"></i>
          </div>
          <div>
            <p class="text-muted small mb-0">Refunded</p>
            <h5 class="fw-bold mb-0 text-warning">৳{{ number_format($totalRefunded, 2) }}</h5>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Payments Table --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>All Transactions</h5>
    </div>
    <div class="card-body p-0">
      @if($payments->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>Invoice No</th>
                <th>Nurse</th>
                <th>Service</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Method</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($payments as $payment)
              <tr>
                <td><span class="badge bg-light text-dark">{{ $payment->invoice_number }}</span></td>
                <td>
                  <div class="fw-semibold">{{ $payment->booking->nurse->name ?? 'N/A' }}</div>
                  <div class="text-muted small">{{ $payment->booking->nurse->nurseProfile->specialization ?? '' }}</div>
                </td>
                <td>{{ $payment->booking->service_type }}</td>
                <td>
                  <div>{{ \Carbon\Carbon::parse($payment->booking->date)->format('d M Y') }}</div>
                  <div class="text-muted small">{{ $payment->duration_hours }}hr(s)</div>
                </td>
                <td class="fw-bold">{{ $payment->formatted_amount }}</td>
                <td>
                  @if($payment->payment_method === 'stripe')
                    <span class="badge bg-primary"><i class="fab fa-stripe me-1"></i>Stripe</span>
                  @elseif($payment->payment_method === 'bkash')
                    <span class="badge bg-danger">bKash</span>
                  @elseif($payment->payment_method === 'nagad')
                    <span class="badge bg-warning text-dark">Nagad</span>
                  @else
                    <span class="text-muted small">—</span>
                  @endif
                </td>
                <td>
                  <span class="badge bg-{{ $payment->status_color }} px-3 py-2">
                    {{ ucfirst($payment->payment_status) }}
                  </span>
                </td>
                <td>
                  @if($payment->payment_status === 'unpaid')
                    <a href="{{ route('patient.payment.show', $payment->booking) }}" class="btn btn-sm btn-primary">
                      <i class="fas fa-credit-card me-1"></i>Pay Now
                    </a>
                  @else
                    <a href="{{ route('patient.invoice', $payment) }}" class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-file-invoice me-1"></i>Invoice
                    </a>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="p-3">{{ $payments->links() }}</div>
      @else
        <div class="text-center py-5">
          <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
          <p class="text-muted">No payment records found.</p>
          <a href="{{ route('nurses.index') }}" class="btn btn-primary">Find a Nurse</a>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection
