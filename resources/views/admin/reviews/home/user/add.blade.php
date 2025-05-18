<style>
    .custom-file-upload {
        display: inline-block;
        padding: 6px 12px;
        cursor: pointer;
        border: 1px solid #ccc;
        border-radius: 4px;
        background-color: #f8f9fa;
        color: #007bff;
        transition: background-color 0.3s;
    }
    .custom-file-upload:hover {
        background-color: #e2e6ea;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<div class="container">
    <div class="heading">
        <h2>{{ $data['title'] }}</h2>
    </div>
    @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    @endif
    <div class="divider">
        <hr>
    </div>
    <form class="main-form" name="add-review-form" enctype="multipart/form-data" id="add-review-form" method="post" action="{{ isset($reviews) ? route('home-reviews-update', $reviews->id) : route('home-reviews-store') }}">
        @csrf
        @if(isset($reviews))
            @method('PUT')
            <input type="hidden" name="rid" id="rid" value="{!! $reviews->id !!}" />
        @endif
        <input type="hidden" name="uid" id="uid" value="{!! request()->id !!}" />
        <div class="form-group">
            <input type="text" required class="form-control" id="name" name="name" value="{{ isset($reviews) ? $reviews->name : Auth::user()->name }}" placeholder="Your Name" readonly>
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group" style="display: none">
            <input type="text" required class="form-control" id="role" name="role" value="{{ isset($reviews) ? $reviews->role : Auth::user()->getRoleNames()->first() }}" placeholder="Your Role" readonly>
            @error('role')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        @php
            $user = Auth::user();
            $avatarUrl = $user && !is_null($user->profile_pic) ? asset('storage/' . $user->profile_pic) : asset('assets/media/userimg.png'); // fallback if needed
        @endphp

        <div class="form-group">
            <label>Current Avatar</label>
            <br>
            <img
                src="{{ $avatarUrl }}"
                alt="User Avatar"
                style="max-width: 150px; margin-top: 10px; border-radius: 25px;"
            >

            {{-- Hidden input to include avatar filename in the form --}}
            <input type="hidden" name="avatar" value="{{ $user->avatar }}">

            <small class="form-text text-muted">This avatar is linked to your profile.</small>
        </div>

        <div class="form-group textarea">
            <label for="textDescription">Review Description</label>
            <textarea name="textDescription" class="form-control" id="summernote" placeholder="Write your review here">{!! isset($reviews) ? $reviews->textDescription : "" !!}</textarea>
            @error('textDescription')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn lastbtn submit_btn">Submit</button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Write your review here',
            tabsize: 2,
            height: 180,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('avatar-preview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
