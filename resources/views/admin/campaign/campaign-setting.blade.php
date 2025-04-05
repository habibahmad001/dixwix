<div class="container py-5">
    <div class="heading mb-4">
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

    <div class="divider my-4">
        <hr>
    </div>

    <!-- Form to setup campaign -->
    <form class="main-form" name="setup-campaign-form" enctype="multipart/form-data" id="setup-campaign-form" method="POST" action="{{ route('campaign-configuration') }}">
        @csrf

        <input type="hidden" name="compaign_uuid" id="compaign_uuid" value="{!! $id !!}">

        <div class="form-group mb-3">
            <label for="card_title" class="form-label">Card Title <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="card_title" name="card_title" value="{{ $giftoInfo?->card_title }}" placeholder="Enter Card Title" required>
            @error('card_title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="group_id" class="form-label">Select Group <span class="text-danger">*</span></label>
            <select class="form-control" id="group_id" name="group_id" required>
                <option value="">--- Select Group ---</option>
                @foreach($availableGroups as $group)
                    <option value="{{ $group->id }}" {{ $group->id == $giftoInfo?->group_id ? 'selected' : '' }}>{{ $group->title }}</option>
                @endforeach
            </select>
            @error('group_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="card_message" class="form-label">Card Message (Optional)</label>
            <textarea class="form-control" id="card_message" name="card_message" rows="4" placeholder="Enter Card Message">{{ $giftoInfo?->card_message }}</textarea>
            @error('card_message')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="card_bg" class="form-label">Card Background Image <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="file" class="form-control" id="card_bg" name="card_bg" accept="image/*" required>
                <button class="btn btn-outline-secondary" type="button" id="browseButton">
                    <i class="bi bi-folder"></i> Browse
                </button>
            </div>
            <small class="text-muted">Max file size 25MB. Formats: jpg, jpeg, png, bmp, gif</small>
            @error('card_bg')
                <div class="text-danger">{{ $message }}</div>
            @enderror

        <!-- Image preview -->
            <div class="mb-1">
                <img id="preview-image" src="{{ $giftoInfo?->card_bg ? asset("/storage/" . $giftoInfo?->card_bg) : "https://placehold.co/250x250?text=Preview+Image" }}" class="img-fluid rounded shadow" style="max-width: 250px; max-height: 250px; object-fit: cover;" alt="Placeholder">
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-lg btn-primary px-5">Save Campaign</button>
        </div>
    </form>
</div>

<!-- jQuery CDN (for frontend validation and image preview) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Instant client-side form validation
        $('#setup-campaign-form').submit(function(e) {
            let isValid = true;

            if ($('#card_title').val().trim() === '') {
                isValid = false;
                alert('Please enter Card Title.');
            }

            if ($('#group_id').val().trim() === '') {
                isValid = false;
                alert('Please select a Group.');
            }

            let fileInput = $('#card_bg')[0];
            if (fileInput.files.length > 0) {
                let fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
                if (fileSize > 25) {
                    isValid = false;
                    alert('Image size must be less than 25MB.');
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        // Live preview of image
        $('#card_bg').change(function() {
            const file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview-image').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
