<div class="container-wrapper col-md-11 mx-auto mt-5" style="display: flex; flex-direction: column; gap: 20px;">
    <h3>{{ $data['title'] }}</h3>
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

    <form method="POST" action="{{ route('add-comment') }}">
        @csrf
        <input type="hidden" name="item_id" value="{{$book->id}}" />
        <div class="form-group">
            <label>Comment</label>
            <textarea name="comment" class="form-control mb-2" placeholder="Add a comment..."></textarea>
            @error('comment')
            <span class="text-danger">{{ $messge }}</span>
            @enderror
        </div>
        <button class="btn lastbtn submit_btn" type="submit">Post Comment</button>
    </form>

    <div class="mb-4 d-flex justify-content-between">
        @if($comments && $comments->isNotEmpty() && ((auth()->user()->hasRole('admin') || auth()->id() == $book->created_by) || auth()->id() == $book['group']->created_by || (!empty($status) && $status->activated && $status->member_role == 'admin')))
        <div>
            <form action="{{ route('comments.deleteAll') }}" method="POST" style="display: inline;">
                @csrf
                <input type="hidden" name="item_id" value="{{$book->id}}" />
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="padding: 10px 20px; border-radius: 5px;">
                    Delete All
                </button>
            </form>
        </div>
        @endif
    </div>

    @forelse($comments as $comment)
    <div class="comment-item" style="display: flex; align-items: flex-start; gap: 15px;">
        <div class="comment-content" style="flex-grow: 1; background-color: {{ $comment->approved ? '#E0E0E0' : '#D9FBE4' }}; border-radius: 10px; padding: 20px;">
            <h5 style="margin: 0 0 10px; font-family: Poppins; font-weight: 600; font-size: 16px; color: #094042;">
                {{ $comment->title ?? 'Comment' }}
            </h5>
            <hr style="border: 1px solid white; margin-bottom: 10px;">
            @if(!empty($comment->user))
            <div class="user-info" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <img src="{{ !empty($comment->user->profile_pic) ? asset('storage/'.$comment->user->profile_pic) : asset('assets/media/userimg.png') }}" alt="User" style="width: 48px; border-radius: 50%;">
                <p style="margin: 0; font-size: 15px; font-weight: 600; color: #094042;">{{ $comment->user->name }}</p>
            </div>
            @endif
            <p style="margin: 0 0 10px; font-size: 14px; color: #094042;">
                {!! $comment->comment !!}
            </p>
            <div style="font-size: 13px; color: #094042; margin-bottom: 10px;">
                <span>Date: {{ \Carbon\Carbon::parse($comment->created_at)->format('Y-m-d') }}</span> |
                <span>Time: {{ \Carbon\Carbon::parse($comment->created_at)->format('h:i') }}</span>
            </div>
            <div class="text-right">
                @if(auth()->user()->hasRole('admin') || auth()->id() == $book->created_by || auth()->id() == $book['group']->created_by || auth()->id() == $comment->user_id || (!empty($status) && $status->activated && $status->member_role == 'admin'))
                <form action="{{ route('comments.delete', $comment->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-warning">
                        Delete Comment
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <h4>No comments found</h4>
    @endforelse
</div>
