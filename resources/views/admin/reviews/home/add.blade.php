<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>

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
    <form class="main-form" name="add-blog-post-form" enctype="multipart/form-data" id="add-blog-post-form" method="post" action="{{ route('blog-post.store') }}">
        @csrf
        <div class="form-group">
            <input type="text" required class="form-control" id="title" name="title" value="{{ old('title') }}" placeholder="Blog Title">
            @error('title')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="category" required name="category" value="{{ old('category') }}" placeholder="Category">
            @error('category')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group textarea">
            <label for="content">Blog Content</label>
            <textarea name="content" class="form-control" id="summernote" placeholder="Post content"></textarea>
            @error('content')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="image">Feature Image</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
            <small class="form-text text-muted">Accepted formats: jpg, jpeg, png.</small>
            @error('image')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
            </select>
            @error('status')
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
        placeholder: 'Blog post content',
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
</script>
