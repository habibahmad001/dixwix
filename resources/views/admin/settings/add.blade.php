<div class="container">
    <div class="heading">
        <h2>{{ $data["title"] }}</h2>
    </div>
    <div class="divider">
        <hr>
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
    <form class="main-form" name="add-type-form" enctype="multipart/form-data" id="add-type-form" method="post"
          action="{{ route('store-settings') }}">
        @csrf
        <input type="hidden" name="mode" value="{{ $mode }}">
        <input type="hidden" name="setting_id" value="{{ $setting_id ?? '' }}">

        <div class="form-group">
            <input type="text" class="form-control" id="title" name="name"
                   value="{{ old('name', $setting['name'] ?? '') }}" placeholder="Settings Name" @if(isset($setting_id)) readonly @else required @endif>
            @error('name')
            <div class="error_msg">*{{ $message }}</div>
            @enderror
        </div>

        @if(!isset($setting_id))
            <div class="form-group">
                <select name="type" id="type" class="form-control" required>
                    <option value="">Select Type</option>
                    @foreach(App\Models\Setting::$TYPE_TEXT as $key => $status)
                        <option value="{{ $key }}" {{ isset($setting['type']) && $setting['type'] == $key ? 'selected' : '' }}>
                            {{ $status }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        @endif

        <div class="form-group" id="value-container">
            @if(isset($setting['type']))
                @if($setting['type'] == 1)
                    <select class="form-control" name="value">
                        <option value="1" {{ old('value', $setting['value']) == '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('value', $setting['value']) == '0' ? 'selected' : '' }}>No</option>
                    </select>
                @elseif($setting['type'] == 2)
                    <input type="number" class="form-control" name="value"
                           value="{{ old('value', $setting['value']) }}" placeholder="Settings Value" required>
                @else
                    <input type="text" class="form-control" name="value"
                           value="{{ old('value', $setting['value']) }}" placeholder="Settings Value">
                @endif
            @endif

            @error('value')
            <div class="error_msg">*{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <button type="submit" class="btn lastbtn submit_btn">Submit</button>
        </div>
        @if (session('error'))
        <div class="error_msg">*{{ session('error') }}</div>
        @endif
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const typeSelect = document.getElementById("type");
        const valueContainer = document.getElementById("value-container");

        function updateValueInput() {
            let selectedType = typeSelect.value;
            let inputHtml = "";

            if (selectedType == 1) {
                inputHtml = `
                    <select class="form-control" name="value">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>`;
            } else if (selectedType == 2) {
                inputHtml = `<input type="number" class="form-control" name="value" placeholder="Settings Value" required>`;
            } else if (selectedType == 3) {
                inputHtml = `<input type="text" class="form-control" name="value" placeholder="Settings Value">`;
            }

            valueContainer.innerHTML = inputHtml;
        }

        typeSelect.addEventListener("change", updateValueInput);
    });
</script>
