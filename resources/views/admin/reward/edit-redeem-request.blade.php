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
    <form class="main-form" name="edit-plan-form" enctype="multipart/form-data" id="edit-redeem-form" method="post" action="{{ route('update-redeem-requests', $transaction->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Coins</label>
            <input type="number" class="form-control" id="coins" name="coins" value="{{ old('coins', $transaction->coins) }}" placeholder="coins" readonly>
            @error('coins')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label>Amount</label>
            <input type="text" class="form-control" id="price" name="price" value="{{ old('amount', $transaction->amount) }}" placeholder="Amount" readonly>
            @error('amount')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                @foreach(App\Models\RewardTransaction::$STATUS_TEXT as $key => $status)
                    <option value="{{ $key }}" {{ $transaction->status == $key ? 'selected' : '' }}>
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
