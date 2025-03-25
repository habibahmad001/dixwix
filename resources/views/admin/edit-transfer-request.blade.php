<div class="container">
    <div class="heading">
        <h2>{{ $data['title'] }}</h2>
    </div>
    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
    @endif
    <div class="divider">
        <hr>
    </div>
    <form class="main-form" name="edit-plan-form" enctype="multipart/form-data" id="edit-transfer-form" method="post" action="{{ route('update-transfer-requests', $transferRequest->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>From User</label>
            <input type="text" class="form-control" value="{{ $transferRequest->fromUser->name }}" disabled>
        </div>

        <div class="form-group">
            <label>To User</label>
            <input type="text" class="form-control" value="{{ $transferRequest->toUser->name }}" disabled>
        </div>
        <div class="form-group">
            <label class="form-label">Points</label>
            <input type="text" class="form-control" value="{{ $transferRequest->points }}" disabled>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                @foreach(App\Models\TransferRequest::$STATUS_TEXT as $key => $status)
                    <option value="{{ $key }}" {{ $transferRequest->status == $key ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
            @error('status')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn lastbtn submit_btn">Update</button>
        </div>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('edit-transfer-form');
        const statusSelect = form.querySelector('select[name="status"]');
        const existingStatus = "{{ $transferRequest->status }}";

        form.addEventListener('submit', function (e) {
            if (statusSelect.value === existingStatus) {
                alert("You have not changed the status!");
                e.preventDefault(); // Prevent form submission
            }
        });
    });
</script>
