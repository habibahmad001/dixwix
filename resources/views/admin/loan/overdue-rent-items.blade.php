<div class="inner_content_table_actions d-flex flex-column flex-md-row mt-4">
    <h4 class="inner_content_title">{{ $data['title'] }}</h4>
</div>

<div class="table-responsive">
    <table id="items_table" class="table">
        <thead>
            <tr>
                <th scope="col">Book Name</th>
                <th scope="col">Reserved By</th>
                <th scope="col">Group</th>
                <th scope="col">Due Date</th>
                <th scope="col" class="text-right">Reserved At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($overdueItems as $item)
            <tr>
                <td>{{ $item->book->name }}</td>
                <td>{{ $item->reserver->name }}</td>
                <td>{{ $item->book->group->title }}</td>
                <td>{{ $item->due_date }}</td>
                <td class="text-right">{{ $item->reserved_at }}</td>
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
            , dom: 'Bfrtip'
            , buttons: [{
                    extend: 'csvHtml5'
                    , text: 'Export CSV'
                    , title: 'Dixwix: Overdue items list'
                }
                , {
                    extend: 'excelHtml5'
                    , text: 'Export Excel'
                    , title: 'Dixwix: Overdue items list'
                }
                , {
                    extend: 'print'
                    , text: 'Print'
                    , title: 'Dixwix: Overdue items list'
                }
            ]
        });
    });

    function deleteRule(ruleId, ruleTitle) {
        Swal.fire({
            title: "Are You Sure?"
            , text: "Do you really want to permanently remove this loan rule?"
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
                    url: `/loan-rules/${ruleId}`
                    , method: 'DELETE'
                    , data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }
                    , success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Deleted!'
                                , text: `Loan rule "${ruleTitle}" has been deleted.`
                                , icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!'
                                , text: response.message || 'Could not delete the rule.'
                                , icon: 'error'
                            });
                        }
                    }
                    , error: function(xhr) {
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
