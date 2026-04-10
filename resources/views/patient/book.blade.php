@extends('layouts.app')
@section('title', 'Book Appointment - NurseSheba')
@push('styles')
<style>
  .cost-preview { background: linear-gradient(135deg,#f0f9ff,#e0f2fe); border: 1.5px solid #bae6fd; border-radius: 10px; transition: all .3s; }
</style>
@endpush
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card shadow-sm border-0">
        <div class="card-body p-5">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
              <h4 class="fw-bold mb-1">Send Booking Request</h4>
              <p class="text-muted mb-0">with <strong>{{ $nurse->name }}</strong> - {{ $nurse->nurseProfile->specialization ?? 'General Nursing' }}</p>
            </div>
            <span class="badge bg-warning text-dark px-3 py-2">Initial status: Pending</span>
          </div>

          <form action="{{ route('patient.book.store') }}" method="POST" id="booking-form">
            @csrf
            <input type="hidden" name="nurse_id" value="{{ $nurse->id }}">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Date</label>
                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror"
                       min="{{ date('Y-m-d') }}" value="{{ old('date') }}" required>
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Time</label>
                <input type="time" name="time" class="form-control @error('time') is-invalid @enderror"
                       value="{{ old('time') }}" required>
                @error('time')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-8">
                <label class="form-label fw-semibold">Service Type</label>
                <select name="service_type" id="service_type"
                        class="form-select @error('service_type') is-invalid @enderror" required>
                  <option value="">Select service type</option>
                  @foreach($rates as $type => $rate)
                    <option value="{{ $type }}"
                            data-rate="{{ $rate }}"
                            {{ old('service_type') == $type ? 'selected' : '' }}>
                      {{ $type }} — ৳{{ number_format($rate) }}/hr base
                    </option>
                  @endforeach
                </select>
                @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-4">
                <label class="form-label fw-semibold">Duration (hours)</label>
                <input type="number" name="duration_hours" id="duration_hours"
                       class="form-control @error('duration_hours') is-invalid @enderror"
                       min="1" max="24" step="0.5" value="{{ old('duration_hours', 2) }}" required>
                @error('duration_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Service Address</label>
                <textarea name="service_address" rows="2"
                          class="form-control @error('service_address') is-invalid @enderror"
                          placeholder="Enter full service address for the visit" required>{{ old('service_address', auth()->user()->address) }}</textarea>
                @error('service_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            {{-- Live Cost Estimate --}}
            <div id="cost-preview" class="cost-preview p-4 mt-4" style="display:none;">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                  <h6 class="fw-bold mb-1"><i class="fas fa-calculator me-2 text-primary"></i>Estimated Cost</h6>
                  <p class="text-muted small mb-0">Nurse experience bonus included · Final amount confirmed at checkout</p>
                </div>
                <div class="text-end">
                  <div class="text-muted small">Rate: ৳<span id="preview-rate">0</span>/hr × <span id="preview-hours">0</span>hr</div>
                  <div class="fw-bold fs-4 text-primary">৳<span id="preview-total">0</span></div>
                </div>
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button type="submit" class="btn btn-primary flex-grow-1 btn-lg">
                <i class="fas fa-paper-plane me-2"></i>Submit Booking Request
              </button>
              <a href="{{ route('nurses.show', $nurse->id) }}" class="btn btn-outline-secondary btn-lg">Back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
const experienceYears = {{ $nurse->nurseProfile->experience_years ?? 0 }};
const bonusPerYear    = 50;
const maxYears        = 5;
const minRate         = 400;
const maxRate         = 2000;

function updateCostPreview() {
  const serviceSelect = document.getElementById('service_type');
  const durationInput = document.getElementById('duration_hours');
  const preview       = document.getElementById('cost-preview');

  if (!serviceSelect.value || !durationInput.value) {
    preview.style.display = 'none';
    return;
  }

  const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
  const baseRate       = parseInt(selectedOption.dataset.rate || 0);
  const capped         = Math.min(experienceYears, maxYears);
  let   rate           = baseRate + (capped * bonusPerYear);
  rate                 = Math.min(Math.max(rate, minRate), maxRate);
  const hours          = parseFloat(durationInput.value);
  const total          = (rate * hours).toFixed(2);

  document.getElementById('preview-rate').textContent  = rate.toLocaleString();
  document.getElementById('preview-hours').textContent = hours;
  document.getElementById('preview-total').textContent = parseFloat(total).toLocaleString('en', {minimumFractionDigits: 2});
  preview.style.display = 'block';
}

document.getElementById('service_type').addEventListener('change', updateCostPreview);
document.getElementById('duration_hours').addEventListener('input', updateCostPreview);
updateCostPreview();
</script>
@endsection
