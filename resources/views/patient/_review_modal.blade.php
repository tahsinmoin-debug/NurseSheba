<div class="modal fade" id="reviewModal{{ $booking->id }}" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Write a Review</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('patient.review.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="booking_id" value="{{ $booking->id }}">
          <div class="mb-3">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select" required>
              <option value="5">&#11088;&#11088;&#11088;&#11088;&#11088; Excellent</option>
              <option value="4">&#11088;&#11088;&#11088;&#11088; Good</option>
              <option value="3">&#11088;&#11088;&#11088; Average</option>
              <option value="2">&#11088;&#11088; Poor</option>
              <option value="1">&#11088; Very Poor</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Comment</label>
            <textarea name="comment" class="form-control" rows="3" placeholder="Share your experience..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Submit Review</button>
        </div>
      </form>
    </div>
  </div>
</div>
