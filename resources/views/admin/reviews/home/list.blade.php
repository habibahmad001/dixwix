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
    </div>
</div>

<div class="table-responsive">
    <table id="items_table" class="table">
        <thead>
            <tr>
                <th scope="col">User Name</th>
                <th scope="col">Points Used</th>
                <th scope="col">Gifto Amount</th>
                <th scope="col">View Card</th>
                <th scope="col">Order Status</th>
                <th scope="col">Order Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->userName }}</td>
                <td>{{ $order->points }}</td>
                <td>{{ $order->giftoAmount }}</td>
                <td>
                    <a href="javascript:void(0);" class="view-card" data-images="{{ $order->cardPath }}">View Card</a>
                </td>
{{--                <td><span class="badge badge-success">{{ $order->orderStatus }}</span></td>--}}
                <td><span class="badge badge-success">{!! (getCampaign($order->campaignUuid))['data']['data']['active'] == 1 ? "Active" : "Inactive" !!}</span></td>
                <td>{{ date("F j, Y",strtotime($order->created_at)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="viewCardModal" tabindex="-1" role="dialog" aria-labelledby="viewCardModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCardModalLabel">Card Images</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalImagesContainer">
                <!-- Images will be injected here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
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

    /********* View Card Model *********/
    // Existing delete-category code...

    // New code for viewing card images
    $('.view-card').on('click', function() {
        const images = $(this).data('images'); // Get the images data
        const modalImagesContainer = $('#modalImagesContainer');

        // Clear previous images
        modalImagesContainer.empty();

        // Check if images exist
        if (images && images.length > 0) {
            // Loop through images and append to modal
            images.forEach(function(image) {
                modalImagesContainer.append(`<img src="${image}" class="img-fluid" alt="Card Image">`);
            });
        } else {
            modalImagesContainer.append('<p>No images available for this card.</p>');
        }

        // Show the modal
        $('#viewCardModal').modal('show');
    });
    /********* View Card Model *********/
});
</script>
