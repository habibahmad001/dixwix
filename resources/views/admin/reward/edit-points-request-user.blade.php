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
    <form class="main-form" name="edit-plan-form" enctype="multipart/form-data" id="edit-redeem-form" method="post" action="{{ route('update-reward-requests-user', $transaction->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Coins</label>
            <input type="number" class="form-control" id="coins" name="coins" value="{{ old('coins', $transaction->points) }}" placeholder="coins">
            @error('coins')
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
        const form = document.getElementById('edit-redeem-form');
        const statusSelect = form.querySelector('select[name="status"]');
        const existingStatus = "{{ $transaction->status }}";

        form.addEventListener('submit', function (e) {
            if (statusSelect.value === existingStatus) {
                alert("You have not changed the status!");
                e.preventDefault(); // Prevent form submission
            }
        });
    });
</script>
