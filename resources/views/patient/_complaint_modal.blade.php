<div class="modal fade" id="complaintModal{{ $booking->id }}" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-danger bg-opacity-10">
        <h5 class="modal-title"><i class="fas fa-flag me-2 text-danger"></i>Report an Issue</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route(auth()->user()->role === 'nurse' ? 'nurse.complaint.store' : 'patient.complaint.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="booking_id" value="{{ $booking->id }}">

          <div class="mb-3">
            <label class="form-label fw-semibold">Booking Reference</label>
            <div class="p-3 bg-light rounded">
              <div class="row">
                <div class="col-md-6">
                  <small class="text-muted">Booking #{{ $booking->id }}</small><br>
                  <strong>{{ $booking->service_type }}</strong>
                </div>
                <div class="col-md-6 text-md-end">
                  <small class="text-muted">{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</small><br>
                  <small>{{ \Carbon\Carbon::parse($booking->time)->format('h:i A') }}</small>
                </div>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Select Complaint Type <span class="text-danger">*</span></label>
            <select name="complaint_type" class="form-select" required>
              <option value="">-- Choose a category --</option>
              @foreach(\App\Models\Complaint::COMPLAINT_TYPES as $type)
                <option value="{{ $type }}">{{ $type }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Describe the Issue <span class="text-danger">*</span></label>
            <textarea name="message" class="form-control" rows="4" required
                      placeholder="Please provide details about the issue you experienced..."
                      maxlength="2000"></textarea>
            <div class="form-text">Maximum 2000 characters</div>
          </div>

          <div class="alert alert-info border-0 mb-0">
            <i class="fas fa-info-circle me-2"></i>
            Your complaint will be reviewed by an admin. You'll receive feedback once it's been processed.
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger"><i class="fas fa-paper-plane me-2"></i>Submit Complaint</button>
        </div>
      </form>
    </div>
  </div>
</div>
