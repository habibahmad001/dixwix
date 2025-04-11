<form class="main-form" name="setup-campaign-form" enctype="multipart/form-data" id="setup-campaign-form" method="POST" action="{{ route('campaign-configuration') }}">
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
            <input type="file" multiple class="form-control" id="card_bg" name="card_bg[]" accept="image/*" required style="display: none;">

            <!-- Drag and Drop Zone -->
            <div id="drop-zone" class="border border-dashed p-4 text-center my-3" style="cursor: pointer;">
                <p>Drag and drop files here or click to upload</p>
            </div>
            <small class="text-muted">Max file size 25MB each. Formats: jpg, jpeg, png, bmp, gif</small>
            @error('card_bg')
            <div class="text-danger">{{ $message }}</div>
        @enderror

        <!-- Image preview -->
            <div class="mb-1" id="image-preview-container">
                @if($giftoInfo?->card_bg)
                    @php
                        $cardBgPaths = json_decode($giftoInfo->card_bg, true);
                        $limitedCardBgPaths = array_slice($cardBgPaths, 0, 5); // Limit to first 5 images
                    @endphp
                    @foreach($limitedCardBgPaths as $originalName => $data)
                        @if(is_array($data) && isset($data['path']))
                            <img
                                src="{{ asset('/storage/' . $data['path']) }}"
                                class="img-fluid rounded shadow"
                                style="max-width: 250px; max-height: 250px; object-fit: cover; margin: 5px;"
                                alt="{{ $data['name'] ?? $originalName }}"
                            >
                        @else
                            {{-- Fallback if somehow $data is not properly structured --}}
                            <div class="alert alert-warning">
                                Invalid image data for "{{ $originalName }}".
                            </div>
                        @endif
                    @endforeach

                @if (count($cardBgPaths) > 5)
                        <p style="color: red; margin-top: 10px;">Only the first 5 images are shown.</p>
                    @endif
                @else
                    <img id="preview-image" src="https://placehold.co/250x250?text=Preview+Image" class="img-fluid rounded shadow" style="max-width: 250px; max-height: 250px; object-fit: cover;" alt="Placeholder">
                @endif
            </div>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-lg btn-primary px-5">Save Campaign</button>
        </div>
</div>


<!-- Bootstrap Modal for Images Table -->
<div class="modal fade" id="imagesModal" tabindex="-1" aria-labelledby="imagesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagesModalLabel">Manage Uploaded Images</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table id="imagesTable" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Thumbnail</th>
                        <th>Name</th>
                        <th>Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- Rows will be dynamically inserted -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">Done</button>
            </div>
        </div>
    </div>
</div>
</form>

<link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
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
                for (let i = 0; i < fileInput.files.length; i++) {
                    let fileSize = fileInput.files[i].size / 1024 / 1024; // in MB
                    if (fileSize > 25) {
                        isValid = false;
                        alert('Each image size must be less than 25MB.');
                    }
                }
            }

            if (!isValid) {
                e.preventDefault();
            }
        });

        let imageFiles = [];

        // Initialize DataTable
        let imagesTable = $('#imagesTable').DataTable({
            "paging": false,
            "info": false,
            "searching": false
        });

        function openModalWithImages(files) {
            imagesTable.clear().draw();
            const maxFiles = 5;
            const filesToShow = Math.min(files.length, maxFiles);

            for (let i = 0; i < filesToShow; i++) {
                let file = files[i];
                let reader = new FileReader();
                reader.onload = function (e) {
                    imagesTable.row.add([
                        `<img src="${e.target.result}" class="img-thumbnail" style="max-width: 100px;">`,
                        `<input type="text" class="form-control" name="image_name[]" placeholder="Enter Name">`,
                        `<input type="number" step="500" onchange="javascript:validateInput(this);" value="500" class="form-control" name="image_price[]" placeholder="Enter Price">`
                    ]).draw(false);
                };
                reader.readAsDataURL(file);
            }

            if (files.length > maxFiles) {
                alert('Only the first 5 images are shown.');
            }

            let modal = new bootstrap.Modal(document.getElementById('imagesModal'));
            modal.show();
        }

        $('#card_bg').change(function () {
            imageFiles = this.files;
            if (imageFiles.length > 0) {
                openModalWithImages(imageFiles);
            }
        });

        // Live preview of image
        // function updateImagePreview(files) {
        //     $('#image-preview-container').empty(); // Clear previous previews
        //     for (let i = 0; i < files.length; i++) {
        //         const file = files[i];
        //         if (file) {
        //             let reader = new FileReader();
        //             reader.onload = function(e) {
        //                 const img = $('<img>').attr('src', e.target.result).addClass('img-fluid rounded shadow').css({
        //                     'max-width': '250px',
        //                     'max-height': '250px',
        //                     'object-fit': 'cover',
        //                     'margin': '5px'
        //                 });
        //                 $('#image-preview-container').append(img);
        //             }
        //             reader.readAsDataURL(file);
        //         }
        //     }
        // }

        // Live preview of images
        function updateImagePreview(files) {
            $('#image-preview-container').empty(); // Clear previous previews
            const maxFiles = 5; // Maximum number of files to show
            const filesToShow = files.length > maxFiles ? maxFiles : files.length; // Determine how many files to show

            for (let i = 0; i < filesToShow; i++) {
                const file = files[i];
                if (file) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        const img = $('<img>').attr('src', e.target.result).addClass('img-fluid rounded shadow').css({
                            'max-width': '250px',
                            'max-height': '250px',
                            'object-fit': 'cover',
                            'margin': '5px'
                        });
                        $('#image-preview-container').append(img);
                    }
                    reader.readAsDataURL(file);
                }
            }

            // If there are more than 5 files, show a message
            if (files.length > maxFiles) {
                const message = $('<p>').text('Only the first 5 images are shown.').css({
                    'color': 'red',
                    'margin-top': '10px'
                });
                $('#image-preview-container').append(message);
            }
        }

        $('#card_bg').change(function() {
            updateImagePreview(this.files);
        });

        // Drag and Drop functionality
        const dropZone = $('#drop-zone');

        dropZone.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.addClass('bg-light');
        });

        dropZone.on('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.removeClass('bg-light');
        });

        dropZone.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.removeClass('bg-light');

            const files = e.originalEvent.dataTransfer.files;
            $('#card_bg')[0].files = files; // Set the files to the input
            updateImagePreview(files); // Update the preview
        });

        // Click event to open file dialog
        dropZone.click(function() {
            $('#card_bg').click();
        });
    });

    function closepopup() {
        $('#imagesModal').modal('hide');
    }

    /******* Logic for Multiple of 500 *******/
    function validateInput(input) {
        const value = parseInt(input.value, 10);
        const step = 500;
        const min = parseInt(input.min, 10);
        const max = parseInt(input.max, 10);

        // Ensure max is a multiple of 500
        const adjustedMax = Math.floor(max / step) * step;

        // Check if the value is a multiple of 500
        if (value % step !== 0) {
            // If not, round it to the nearest multiple of 500
            const roundedValue = Math.round(value / step) * step;

            // Ensure the rounded value is within the min and adjusted max limits
            if (roundedValue < min) {
                input.value = min;
            } else if (roundedValue > adjustedMax) {
                input.value = adjustedMax;
            } else {
                input.value = roundedValue;
            }
        }

        // Ensure the value does not exceed the adjusted max limit
        if (value > adjustedMax) {
            input.value = adjustedMax;
        }

        // Update the displayed value
        $('.peice').text(input.value / 100);
    }
    /******* Logic for Multiple of 500 *******/
</script>
