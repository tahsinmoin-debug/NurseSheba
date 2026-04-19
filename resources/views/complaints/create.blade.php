@extends('layouts.app')
@section('title', 'File a Complaint - NurseSheba')
@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0"><i class="fas fa-flag me-2 text-danger"></i>File a Complaint</h3>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">&larr; Back</a>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <div class="mb-4 p-3 bg-light rounded">
            <h6 class="fw-bold mb-2">Booking Reference #{{ $booking->id }}</h6>
            <div class="row">
              <div class="col-md-6">
                <p class="mb-1"><strong>Service:</strong> {{ $booking->service_type }}</p>
                <p class="mb-1"><strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</p>
              </div>
              <div class="col-md-6">
                <p class="mb-1"><strong>Nurse:</strong> {{ $booking->nurse->name ?? 'N/A' }}</p>
                <p class="mb-1"><strong>Patient:</strong> {{ $booking->patient->name ?? 'N/A' }}</p>
              </div>
            </div>
          </div>

          <form action="{{ route(auth()->user()->role === 'nurse' ? 'nurse.complaint.store' : 'patient.complaint.store') }}" method="POST">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $booking->id }}">

            <div class="mb-3">
              <label class="form-label fw-semibold">Select Complaint Type <span class="text-danger">*</span></label>
              <select name="complaint_type" class="form-select form-select-lg" required>
                <option value="">-- Choose a category --</option>
                @foreach($complaintTypes as $type)
                  <option value="{{ $type }}" {{ old('complaint_type') === $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
              </select>
              @error('complaint_type')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-4">
              <label class="form-label fw-semibold">Describe the Issue <span class="text-danger">*</span></label>
              <textarea name="message" class="form-control" rows="5" required
                        placeholder="Please provide detailed information about the issue..."
                        maxlength="2000">{{ old('message') }}</textarea>
              <div class="form-text">Maximum 2000 characters</div>
              @error('message')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="alert alert-info border-0">
              <i class="fas fa-info-circle me-2"></i>
              Your complaint will be reviewed by an admin. You'll receive feedback once it's been processed.
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-danger btn-lg">
                <i class="fas fa-paper-plane me-2"></i>Submit Complaint
              </button>
              <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-lg">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
