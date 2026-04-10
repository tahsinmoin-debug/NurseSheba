@extends('layouts.app')
@section('title', 'Book Appointment - NurseSheba')
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

          <form action="{{ route('patient.book.store') }}" method="POST">
            @csrf
            <input type="hidden" name="nurse_id" value="{{ $nurse->id }}">

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-semibold">Date</label>
                <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" min="{{ date('Y-m-d') }}" value="{{ old('date') }}" required>
                @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Time</label>
                <input type="time" name="time" class="form-control @error('time') is-invalid @enderror" value="{{ old('time') }}" required>
                @error('time')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Service Type</label>
                <select name="service_type" class="form-select @error('service_type') is-invalid @enderror" required>
                  <option value="">Select service type</option>
                  <option value="General Nursing" {{ old('service_type') == 'General Nursing' ? 'selected' : '' }}>General Nursing</option>
                  <option value="Post-Surgery Care" {{ old('service_type') == 'Post-Surgery Care' ? 'selected' : '' }}>Post-Surgery Care</option>
                  <option value="Elderly Care" {{ old('service_type') == 'Elderly Care' ? 'selected' : '' }}>Elderly Care</option>
                  <option value="Pediatric Care" {{ old('service_type') == 'Pediatric Care' ? 'selected' : '' }}>Pediatric Care</option>
                  <option value="Wound Dressing" {{ old('service_type') == 'Wound Dressing' ? 'selected' : '' }}>Wound Dressing</option>
                  <option value="Injection/IV" {{ old('service_type') == 'Injection/IV' ? 'selected' : '' }}>Injection/IV</option>
                  <option value="Physiotherapy" {{ old('service_type') == 'Physiotherapy' ? 'selected' : '' }}>Physiotherapy</option>
                  <option value="ICU Care" {{ old('service_type') == 'ICU Care' ? 'selected' : '' }}>ICU Care</option>
                  <option value="Other" {{ old('service_type') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>

              <div class="col-12">
                <label class="form-label fw-semibold">Service Address</label>
                <textarea name="service_address" rows="3" class="form-control @error('service_address') is-invalid @enderror" placeholder="Enter full service address for the visit" required>{{ old('service_address', auth()->user()->address) }}</textarea>
                @error('service_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
              </div>
            </div>

            <div class="d-flex gap-2 mt-4">
              <button type="submit" class="btn btn-primary flex-grow-1 btn-lg">Submit Booking Request</button>
              <a href="{{ route('nurses.show', $nurse->id) }}" class="btn btn-outline-secondary btn-lg">Back</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
