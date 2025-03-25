<div class="container">
    <div class="heading">
        <h2>Edit Loan Rule</h2>
    </div>
    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
    @endif
    <div class="divider">
        <hr>
    </div>
    <form class="main-form" name="edit-loan-rule-form" enctype="multipart/form-data" id="edit-loan-rule-form" method="post" action="{{ route('loan-rules.update', $loanRule->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <input type="text" required class="form-control" id="title" name="title" value="{{ old('title', $loanRule->title) }}" placeholder="Rule Title (e.g., One Week)">
            @error('title')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <input type="number" required class="form-control" id="duration" name="duration" value="{{ old('duration', $loanRule->duration) }}" placeholder="Duration (e.g., 1, 2, 5)">
            @error('duration')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <select class="form-control" id="duration_type" name="duration_type" required>
                <option value="" disabled>Select Duration Type</option>
                <option value="day" {{ old('duration_type', $loanRule->duration_type) == 'day' ? 'selected' : '' }}>Day</option>
                <option value="week" {{ old('duration_type', $loanRule->duration_type) == 'week' ? 'selected' : '' }}>Week</option>
                <option value="month" {{ old('duration_type', $loanRule->duration_type) == 'month' ? 'selected' : '' }}>Month</option>
            </select>
            @error('duration_type')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn text-nowrap lastbtn submit_btn">Update Loan Rule</button>
        </div>
    </form>
</div>
