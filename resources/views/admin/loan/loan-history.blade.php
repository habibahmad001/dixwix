<div class="inner_content_table_actions d-flex flex-column flex-md-row mt-4">
    <h4 class="inner_content_title">{{ $data['title'] }}</h4>
</div>

<div class="table-responsive">
    <table id="items_table" class="table">
        <thead>
            <tr>
                <th scope="col">Book Name</th>
                <th scope="col">User</th>
                <th scope="col">Group</th>
                <th scope="col">Reserved At</th>
                <th scope="col">Due Date</th>
                <th scope="col">Returned At</th>
                <th scope="col">Amount</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loanHistory as $history)
            <tr>
                <td>{{ $history->book->name ?? 'N/A' }}</td>
                <td>{{ $history->user->name ?? 'N/A' }}</td>
                <td>{{ $history->group->title ?? 'N/A' }}</td>
                <td>{{ $history->reserved_at }}</td>
                <td>{{ $history->due_date }}</td>
                <td>{{ $history->returned_at ?? 'Not Returned' }}</td>
                <td>${{ number_format($history->amount, 2) }}</td>
                <td>
                    <span>
                        {{ ucfirst($history->status) }}
                    </span>
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
            , dom: 'Bfrtip'
            , buttons: [{
                    extend: 'csvHtml5'
                    , text: 'Export CSV'
                    , title: 'Dixwix: Loan History'
                }
                , {
                    extend: 'excelHtml5'
                    , text: 'Export Excel'
                    , title: 'Dixwix: Loan History'
                }
                , {
                    extend: 'print'
                    , text: 'Print'
                    , title: 'Dixwix: Loan History'
                }
            ]
        });
    });

</script>
