<div class="inner_content">
    <h3>{{ $data['title'] }}</h3>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-success">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" id="buld-edit-items-form" action="{{ route('bulk-update-items') }}">
        @csrf

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Group</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Rental Price</th>
                    <th>Status</th>
                    <th>Condition</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $index=>$item)
                <tr>
                    <td>
                        <input type="hidden" name="items[{{ $index+1 }}][id]" value="{{ $item->id }}" data-item-id="{{ $item->id }}">
                        <input type="text" name="items[{{ $index+1 }}][name]" value="{{ $item->name }}" class="form-control" data-item-id="{{ $item->id }}">
                    </td>
                    <td>
                        <select name="items[{{ $index+1 }}][group_id]" class="form-control">
                            <option value="">Select Group</option>
                            @foreach ($groups as $group)
                            <option value="{{ $group->id }}" {{ $item->group_id == $group->id ? 'selected' : '' }}>
                                {{ $group->title }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select name="items[{{ $index+1 }}][type_id]" class="form-control category-select" data-item-id="{{ $item->id }}">
                            <option value="" data-percent="0">Select Category</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}" data-percent="{{ $category->percentage ?? 0 }}" {{ $item->category['name'] == $category['name'] ? 'selected' : '' }}>
                                {{ $category['name'] }}
                            </option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index+1 }}][price]" value="{{ $item->price }}" class="form-control price-input" data-item-id="{{ $item->id }}">
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index+1 }}][rent_price]" value="{{ $item->rent_price }}" class="form-control rent-price-input" data-item-id="{{ $item->id }}">
                    </td>
                    <td>
                        <select name="items[{{ $index+1 }}][status_options]" class="form-control">
                            <option value="">Select Status</option>
                            <option value="disable" {{ $item->status_options == 'disable' ? 'selected' : '' }}>Disabled</option>
                            <option value="maintenance" {{ $item->status_options == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </td>
                    <td>
                        <select name="items[{{ $index+1 }}][condition]" class="form-control">
                            <option value="">Please select option</option>
                            <option value="new" {{ $item->condition == 'new' ? 'selected' : '' }}>New</option>
                            <option value="good" {{ $item->condition == 'good' ? 'selected' : '' }}>Good</option>
                            <option value="fair" {{ $item->condition == 'fair' ? 'selected' : '' }}>Fair</option>
                            <option value="poor" {{ $item->condition == 'poor' ? 'selected' : '' }}>Poor</option>
                        </select>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex">
            <button type="button" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to edit the items?','question',function(){$('#buld-edit-items-form').submit()})" class="btn mr-2 lastbtn submit_btn">Save Changes</button>
            <a href="{{ route('my-items') }}" class="btn lastbtn submit_btn btn-primary">Cancel</a>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        function calculateRentPrice(itemId) {
            const categoryPercent = parseFloat($(`.category-select[data-item-id="${itemId}"] option:selected`).data('percent')) || 0;
            const price = parseFloat($(`.price-input[data-item-id="${itemId}"]`).val()) || 0;

            if (categoryPercent > 0) {
                const rentPrice = Math.round((price * categoryPercent) / 100);
                $(`.rent-price-input[data-item-id="${itemId}"]`).val(rentPrice);
            } else {
                $(`.rent-price-input[data-item-id="${itemId}"]`).val('');
            }
        }

        $('.category-select').on('change', function() {
            const itemId = $(this).data('item-id');
            calculateRentPrice(itemId);
        });

        $('.price-input').on('input', function() {
            const itemId = $(this).data('item-id');
            calculateRentPrice(itemId);
        });
    });

</script>
