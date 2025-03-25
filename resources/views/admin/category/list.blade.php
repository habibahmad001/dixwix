<div class="inner_content_table_actions d-flex flex-column flex-md-row mt-4">
    <h4 class="inner_content_title">Categories</h4>
    <div class="inner_content_table_action_tools">
        <span>List item by</span>
        <select name="item_groups" id="item_group">
            <option value="">Group Type</option>
            @foreach($data["group_types"] as $groupt)
            <option value="{{ $groupt["id"] }}">{{ $groupt["name"] }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="table-responsive">
    <table id="items_table" class="table">
        <thead>
            <tr>
                <th scope="col"></th>
                <th scope="col">Name</th>
                <th scope="col">Percentage(%)</th>
                <th scope="col">Description</th>
                <th scope="col">Date Added</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data["types"] as $type)
            <tr>
                <td>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route("edit-category",["id"=>$type->id]) }}">
                            <img src="{{ url("assets/media/edit-orange.png") }}" width="15px" height="15px" /></a>
                        <a href="#" class="delete-category" data-id="{{ $type->id }}" data-name="{{ $type->name }}">
                            <img src="{{ url('assets/media/delete.png') }}" width="15px" height="15px" />
                        </a>
                    </div>
                </td>
                <td>{{ $type->name }}</td>
                <td>{{ $type->percentage }}</td>
                <td>{{ $type->description }}</td>
                <td>{{ date("m/d/Y",strtotime($type->created_at)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('.delete-category').on('click', function(e) {
        e.preventDefault();

        const categoryId = $(this).data('id');
        const categoryName = $(this).data('name');

        Swal.fire({
            title: "Are You Sure?",
            text: "Do you really want to permanently remove this category? This action may affect other parts of the system where this category is used!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Delete Permanently",
            cancelButtonText: "Cancel",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('delete-category') }}",
                    method: 'POST',
                    data: {
                        id: categoryId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: `Category "${categoryName}" has been deleted.`,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Could not delete the category.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An unexpected error occurred.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
});
</script>
