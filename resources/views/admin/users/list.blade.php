<div class="inner_content_table_actions d-flex flex-column flex-md-row mt-4">
    <h4 class="inner_content_title">{{ $data['title'] }}</h4>
</div>

<div class="table-responsive">
    <table id="items_table" class="table">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Groups</th>
                <th scope="col">Membership Type</th>
                <th scope="col">Date Added</th>
                <th scope="col">Roles</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone }}</td>
                <td>
                    @if ($user->createdgroups->isNotEmpty())
                    @foreach($user->createdgroups as $group)
                    <strong>{{ $group->title }}</strong>
                    <br/>
                    @endforeach
                    @else
                    No groups
                    @endif
                </td>

                <td>
                    @if ($user->membership->isNotEmpty())
                    <div>
                        @foreach($user->membership as $membership)
                        @if ($membership->plan)
                        <p>{{ $membership->plan->name }}</p>
                        @else
                        <span>No associated plan</span>
                        @endif
                        @endforeach
                    </div>
                    @else
                    <span>No memberships</span>
                    @endif
                </td>
                <td>{{ date("m/d/Y", strtotime($user->created_at)) }}</td>
                <td>
                    @if ($user->roles->isNotEmpty())
                    {{ $user->roles->pluck('name')->join(', ') }}
                    @else
                    <span>No roles assigned</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('edit-user', ['id' => $user->id]) }}">
                            <img src="{{ url('assets/media/edit-orange.png') }}" width="15px" height="15px" />
                        </a>
                        @if ($user->id != auth()->id() && $user->id != 1)
                        <button class="border-0 bg-transparent delete-user" onclick="deleteUser('{{ $user->id }}', '{{ $user->name }}')">
                            <img src="{{ url('assets/media/delete.png') }}" width="15px" height="15px" />
                        </button>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<!-- Buttons Extension CSS -->
<link rel="stylesheet" href="//cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" />

<!-- jQuery -->
<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- DataTables JS -->
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<!-- Buttons Extension JS -->
<script src="//cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<!-- JSZip for Excel export -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<!-- PDFMake for PDF export -->
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<!-- Buttons for CSV, Excel, and PDF -->
<script src="//cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="//cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>


<script>
    $(document).ready(function() {

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
            , dom: 'Bfrtip', // Add the Buttons extension
            buttons: [{
                    extend: 'csvHtml5'
                    , text: 'Export CSV'
                    , title: 'Data Export'
                }
                , {
                    extend: 'excelHtml5'
                    , text: 'Export Excel'
                    , title: 'Data Export'
                }
                , {
                    extend: 'pdfHtml5'
                    , text: 'Export PDF'
                    , title: 'Data Export'
                    , orientation: 'landscape', // Optional: adjust page orientation
                    pageSize: 'A4' // Optional: adjust page size
                }
                , {
                    extend: 'print'
                    , text: 'Print'
                    , title: 'Data Export'
                }
            ]
        });
    });

    function deleteUser(userId, userName) {

        Swal.fire({
            title: "Are You Sure?"
            , text: "Do you really want to permanently remove this user?"
            , icon: "warning"
            , showCancelButton: true
            , confirmButtonColor: "#3085d6"
            , cancelButtonColor: "#d33"
            , confirmButtonText: "Yes, Delete Permanently"
            , cancelButtonText: "Cancel"
            , reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('delete-user') }}"
                    , method: 'POST'
                    , data: {
                        id: userId
                        , _token: "{{ csrf_token() }}"
                    }
                    , success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Deleted!'
                                , text: `User "${userName}" has been deleted.`
                                , icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!'
                                , text: response.message || 'Could not delete the user.'
                                , icon: 'error'
                            });
                        }
                    }
                    , error: function() {
                        Swal.fire({
                            title: 'Error!'
                            , text: 'An unexpected error occurred.'
                            , icon: 'error'
                        });
                    }
                });
            }
        });
    }

</script>
