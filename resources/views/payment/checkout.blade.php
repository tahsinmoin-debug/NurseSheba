@extends('layouts.app')
@section('title', 'Checkout - NurseSheba')
@push('styles')
<style>
  .payment-tab-btn { cursor:pointer; transition: all .2s; }
  .payment-tab-btn.active { background: linear-gradient(135deg,#0288d1,#4fc3f7); color:#fff; border-color:#0288d1; }
  .cost-breakdown { background: linear-gradient(135deg,#f0f9ff,#e0f2fe); border-radius:12px; }
  #stripe-card-element { padding:14px; border:1.5px solid #dee2e6; border-radius:8px; background:#fff; transition: border-color .2s; }
  #stripe-card-element.StripeElement--focus { border-color:#0288d1; box-shadow:0 0 0 3px rgba(2,136,209,.15); }
  .method-panel { display:none; }
  .method-panel.active { display:block; }
  .secure-badge { background:#f8fff8; border:1px solid #c3e6cb; border-radius:8px; }
</style>
@endpush
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-xl-10">
      <div class="mb-4">
        <a href="{{ route('patient.dashboard') }}" class="text-decoration-none text-muted small">
          <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
      </div>

      <div class="row g-4">
        {{-- Left: Cost Breakdown --}}
        <div class="col-lg-5">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-4"><i class="fas fa-receipt me-2 text-primary"></i>Order Summary</h5>

              <div class="cost-breakdown p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">Service</span>
                  <span class="fw-semibold">{{ $booking->service_type }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">Nurse</span>
                  <span class="fw-semibold">{{ $booking->nurse->name }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">Date & Time</span>
                  <span class="fw-semibold">{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}, {{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">Duration</span>
                  <span class="fw-semibold">{{ $payment->duration_hours }} hour(s)</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted">Hourly Rate</span>
                  <span class="fw-semibold">{{ $payment->formatted_rate }}/hr</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                  <span class="fw-bold fs-5">Total</span>
                  <span class="fw-bold fs-4 text-primary">{{ $payment->formatted_amount }}</span>
                </div>
              </div>

              <div class="secure-badge p-3 text-center">
                <i class="fas fa-lock text-success me-2"></i>
                <span class="small text-muted">Secure &amp; Encrypted Payment</span>
              </div>

              <div class="mt-3 p-3 bg-light rounded">
                <p class="small text-muted mb-1"><strong>Invoice No:</strong> {{ $payment->invoice_number }}</p>
                <p class="small text-muted mb-1"><strong>Booking ID:</strong> #{{ $booking->id }}</p>
                <p class="small text-muted mb-0"><strong>Address:</strong> {{ $booking->service_address }}</p>
              </div>
            </div>
          </div>
        </div>

        {{-- Right: Payment Options --}}
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
              <h5 class="fw-bold mb-4"><i class="fas fa-credit-card me-2 text-primary"></i>Choose Payment Method</h5>

              {{-- Tabs --}}
              <div class="d-flex gap-3 mb-4 flex-wrap">
                <button class="btn btn-outline-primary payment-tab-btn active flex-grow-1" data-target="stripe-panel">
                  <i class="fab fa-stripe me-2"></i>Pay with Card (Stripe)
                </button>
                <button class="btn btn-outline-primary payment-tab-btn flex-grow-1" data-target="mobile-panel">
                  <i class="fas fa-mobile-alt me-2"></i>bKash / Nagad
                </button>
              </div>

              @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
              @endif

              {{-- Stripe Panel --}}
              <div id="stripe-panel" class="method-panel active">
                <div class="alert alert-info small">
                  <i class="fas fa-info-circle me-1"></i>
                  Test card: <strong>4242 4242 4242 4242</strong> · Any future date · Any 3-digit CVC
                </div>
                <div id="stripe-card-element" class="mb-3"></div>
                <div id="card-errors" class="text-danger small mb-3"></div>
                <button id="pay-stripe-btn" class="btn btn-primary w-100 btn-lg">
                  <span id="pay-text"><i class="fas fa-lock me-2"></i>Pay {{ $payment->formatted_amount }}</span>
                  <span id="pay-spinner" class="d-none"><span class="spinner-border spinner-border-sm me-2"></span>Processing...</span>
                </button>
              </div>

              {{-- Mobile Money Panel --}}
              <div id="mobile-panel" class="method-panel">
                <div class="alert alert-warning small">
                  <i class="fas fa-flask me-1"></i>
                  This is a <strong>simulated</strong> payment for academic purposes. A fake transaction ID will be generated.
                </div>
                <form action="{{ route('patient.payment.mobile', $booking) }}" method="POST">
                  @csrf
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Select Provider</label>
                    <div class="d-flex gap-3">
                      <div class="form-check flex-grow-1 border rounded p-3">
                        <input class="form-check-input" type="radio" name="mobile_provider" id="bkash" value="bkash" required>
                        <label class="form-check-label fw-semibold text-danger" for="bkash">
                          <i class="fas fa-mobile-alt me-1"></i> bKash
                        </label>
                      </div>
                      <div class="form-check flex-grow-1 border rounded p-3">
                        <input class="form-check-input" type="radio" name="mobile_provider" id="nagad" value="nagad">
                        <label class="form-check-label fw-semibold text-warning" for="nagad">
                          <i class="fas fa-mobile-alt me-1"></i> Nagad
                        </label>
                      </div>
                    </div>
                    @error('mobile_provider')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                  </div>
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Mobile Number</label>
                    <div class="input-group">
                      <span class="input-group-text">+880</span>
                      <input type="text" name="mobile_number" class="form-control @error('mobile_number') is-invalid @enderror"
                             placeholder="01XXXXXXXXX" maxlength="11" value="{{ old('mobile_number') }}">
                      @error('mobile_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                  </div>
                  <button type="submit" class="btn btn-success w-100 btn-lg">
                    <i class="fas fa-check-circle me-2"></i>Simulate Payment — {{ $payment->formatted_amount }}
                  </button>
                </form>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<script src="https://js.stripe.com/v3/"></script>
@endpush

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
// Tab switching
document.querySelectorAll('.payment-tab-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.payment-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.method-panel').forEach(p => p.classList.remove('active'));
    this.classList.add('active');
    document.getElementById(this.dataset.target).classList.add('active');
  });
});

// Stripe
const stripe = Stripe('{{ $stripeKey }}');
const elements = stripe.elements();
const card = elements.create('card', {
  style: {
    base: { fontSize: '16px', color: '#212529', fontFamily: 'Inter, sans-serif' },
    invalid: { color: '#dc3545' }
  }
});
card.mount('#stripe-card-element');

card.on('change', function(event) {
  document.getElementById('card-errors').textContent = event.error ? event.error.message : '';
});

document.getElementById('pay-stripe-btn').addEventListener('click', async function() {
  const btn = this;
  btn.disabled = true;
  document.getElementById('pay-text').classList.add('d-none');
  document.getElementById('pay-spinner').classList.remove('d-none');

  try {
    // Get PaymentIntent client secret
    const resp = await fetch('{{ route("patient.payment.stripe.intent", $booking) }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    });
    const data = await resp.json();

    if (data.error) {
      document.getElementById('card-errors').textContent = data.error;
      btn.disabled = false;
      document.getElementById('pay-text').classList.remove('d-none');
      document.getElementById('pay-spinner').classList.add('d-none');
      return;
    }

    const { error } = await stripe.confirmCardPayment(data.clientSecret, {
      payment_method: { card: card }
    });

    if (error) {
      document.getElementById('card-errors').textContent = error.message;
      btn.disabled = false;
      document.getElementById('pay-text').classList.remove('d-none');
      document.getElementById('pay-spinner').classList.add('d-none');
    } else {
      window.location.href = '{{ route("patient.payment.stripe.success", $booking) }}';
    }
  } catch(e) {
    document.getElementById('card-errors').textContent = 'An error occurred. Please try again.';
    btn.disabled = false;
    document.getElementById('pay-text').classList.remove('d-none');
    document.getElementById('pay-spinner').classList.add('d-none');
  }
});
</script>
@endsection
