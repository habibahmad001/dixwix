<style>
    .modal-dialog {
        max-width: 80%; /* Set modal width to 80% */
    }

    .modal-body {
        display: flex;
        flex-wrap: wrap; /* Allow images to wrap */
        justify-content: center; /* Space out images */
    }

    .modal-body img {
        width: 32%; /* Set each image to take up about 1/3 of the row */
        margin-bottom: 10px; /* Add some space below each image */
    }
</style>

<div class="inner_content_table_actions d-flex flex-column flex-md-row mt-4">
    <h4 class="inner_content_title">{!! $data['title'] !!}</h4>
    <div class="inner_content_table_action_tools">
        <a href="{{ route('home-reviews.create') }}">
            <i class="fa fa-plus-circle" aria-hidden="true" style="font-size: 18px; color: #db5f3e"></i>
        </a>

    </div>
</div>

<div class="table-responsive">
    <table id="items_table" class="table">
        <thead>
            <tr>
                <th scope="col">Image</th>
                <th scope="col">Name</th>
                <th scope="col">Role</th>
                <th scope="col">Created At</th>
                <th scope="col">Description</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reviews as $review)
            <tr>
{{--                <td><img src="{{ asset('storage/' . $review->avatar) }}" style="width: 100px; height: 100px; border-radius: 5%" alt="View Group" class="icon"></td>--}}
                <td><img src="{{ !is_null($review->avatar) ? asset('storage/' . $review->avatar) : asset('assets/media/userimg.png') }}" style="width: 100px; height: 100px; border-radius: 5%" alt="View Group" class="icon"></td>
                <td>{{ $review->name }}</td>
                <td>{{ $review->role }}</td>
                <td>{{ strip_tags($review->textDescription) }}</td>
                <td>{{ date("F j, Y",strtotime($review->created_at)) }}</td>
                <td>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('home-reviews.show', $review->id)  }}">
                            <i class="fa fa-edit" style="color: darkblue; font-size: 15px"></i>
                        </a>
                        &nbsp;
                        <form action="{{ route('home-reviews.destroy', $review->id) }}" method="POST" class="delete-review-form" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="swal-delete-button" style="background: none; border: none; padding: 0;">
                                <i class="fa fa-trash" style="color: maroon; font-size: 15px"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {

    /******** Pagination **********/
    $('#items_table').DataTable({
        paging: true
        , searching: true
        , ordering: true
        , responsive: true
        , lengthChange: true
        , pageLength: 10
        , info: true
        , autoWidth: false
        , columnDefs: [{
            orderable: false
            , targets: -1
        }]
        , language: {
            search: "Search:"
            , lengthMenu: "Show _MENU_ entries"
            , info: "Showing _START_ to _END_ of _TOTAL_ entries"
            , paginate: {
                first: "First"
                , last: "Last"
                , next: "Next"
                , previous: "Previous"
            }
        }
    });
    /******** Pagination **********/
});

    document.addEventListener('DOMContentLoaded', function () {
        const deleteButtons = document.querySelectorAll('.swal-delete-button');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function () {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to undo this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit the parent form
                        button.closest('form').submit();
                    }
                });
            });
        });
    });

</script>
