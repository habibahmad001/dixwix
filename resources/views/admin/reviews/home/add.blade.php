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
    <form class="main-form" name="add-review-form" enctype="multipart/form-data" id="add-review-form" method="post" action="{{ isset($reviews) ? route('home-reviews.update', $reviews->id) : route('home-reviews.store') }}">
        @csrf
        @if(isset($reviews))
            @method('PUT')
            <input type="hidden" name="rid" id="rid" value="{!! $reviews->id !!}" />
        @endif
        <div class="form-group">
            <input type="text" required class="form-control" id="name" name="name" value="{{ isset($reviews) ? $reviews->name : old('name') }}" placeholder="Your Name">
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" required class="form-control" id="role" name="role" value="{{ isset($reviews) ? $reviews->role : old('role') }}" placeholder="Your Role">
            @error('role')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="avatar" class="custom-file-upload">
                <i class="fas fa-upload"></i> Choose Avatar
            </label>
            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*" style="display: none;" onchange="previewImage(event)">
            <img id="avatar-preview" src="#" alt="Avatar Preview" style="display: none; max-width: 150px; margin-top: 10px; border-radius: 25px;">
            <small class="form-text text-muted">Accepted formats: jpg, jpeg, png.</small>
            @error('avatar')
            <div class="text-danger">{{ $message }}</div>
            @enderror
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
