<div class="container-wrapper col-md-11 mx-auto mt-5" style="display: flex; flex-direction: column; gap: 20px;">
    <div class="d-flex align-items-center justify-content-between">
        <h3>{{ $data['title'] }}
        </h3>
        <div>
            <strong>Rating:</strong> {{ number_format($averageRating, 1) }}/5</p>
        </div>
    </div>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    @if($canAddReview)
    <form method="POST" action="{{ route('add-review') }}">
        @csrf
        <input type="hidden" name="item_id" value="{{ $book->id }}" />
        <div class="form-group">
            <label for="rating">Rating</label>
            <select name="rating" id="rating" class="form-control mb-2" required>
                <option value="" disabled selected>Select a rating</option>
                <option value="5">5 - Excellent</option>
                <option value="4">4 - Good</option>
                <option value="3">3 - Average</option>
                <option value="2">2 - Poor</option>
                <option value="1">1 - Terrible</option>
            </select>
            @error('rating')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="review">Review</label>
            <textarea id="review" name="review" class="form-control mb-2" placeholder="Add a review..."></textarea>
            @error('review')
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button class="btn lastbtn submit_btn" type="submit">Post Review</button>
    </form>
    @endif

    <div class="mb-4 d-flex justify-content-between">
        @if($reviews && $reviews->isNotEmpty() && ((auth()->user()->hasRole('admin') || auth()->id() == $book->created_by) || auth()->id() == $book['group']->created_by || (!empty($status) && $status->activated && $status->member_role == 'admin')))
        <div>
            <form action="{{ route('reviews.deleteAll') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="item_id" value="{{ $book->id }}" />
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="padding: 10px 20px; border-radius: 5px;">
                    Delete All Reviews
                </button>
            </form>
        </div>
        @endif
    </div>

    @forelse($reviews as $review)
    <div class="review-item" style="display: flex; align-items: flex-start; gap: 15px;">
        <div class="review-content" style="flex-grow: 1; background-color: {{ $review->approved ? '#E0E0E0' : '#D9FBE4' }}; border-radius: 10px; padding: 20px;">
            <h5 style="margin: 0 0 10px; font-family: Poppins; font-weight: 600; font-size: 16px; color: #094042;">
                {{ $review->title ?? 'Review' }}
            </h5>
            <hr style="border: 1px solid white; margin-bottom: 10px;">
            @if(!empty($review->user))
            <div class="user-info" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <img src="{{ !empty($review->user->profile_pic) ? asset('storage/'.$review->user->profile_pic) : asset('assets/media/userimg.png') }}" alt="User" style="width: 48px; border-radius: 50%;">
                <p style="margin: 0; font-size: 15px; font-weight: 600; color: #094042;">{{ $review->user->name }}</p>
            </div>
            @endif
            <p style="margin: 0 0 10px; font-size: 14px; color: #094042;">
                {!! $review->review !!}
            </p>
            <p style="margin: 0 0 10px; font-size: 14px; color: #094042;">
                <strong>Rating:</strong> {{ $review->rating }}/5
            </p>
            <div style="font-size: 13px; color: #094042; margin-bottom: 10px;">
                <span>Date: {{ \Carbon\Carbon::parse($review->created_at)->format('Y-m-d') }}</span> |
                <span>Time: {{ \Carbon\Carbon::parse($review->created_at)->format('h:i') }}</span>
            </div>
            <div class="text-right">
                @if(auth()->user()->hasRole('admin') || auth()->id() == $book->created_by || auth()->id() == $book['group']->created_by || auth()->id() == $review->user_id || (!empty($status) && $status->activated && $status->member_role == 'admin'))
                <form action="{{ route('reviews.delete', $review->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-warning">
                        Delete Review
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <h4>No reviews found</h4>
    @endforelse
</div>
