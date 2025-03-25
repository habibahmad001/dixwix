<div class="inner_content_table_actions d-flex flex-column flex-md-row mt-4">
    <h4 class="inner_content_title">{{ $data['title'] }}</h4>
</div>
<form method="GET" action="{{ route('admin.reservation-report') }}" class="mb-4">
    <div class="row">
        <div class="col-md-3">
            <label for="reserved_by">Member:</label>
            <select name="reserved_by" id="reserved_by" class="form-control">
                <option value="">-- Select Member --</option>
                @foreach($members as $member)
                <option value="{{ $member->id }}" {{ request('reserved_by') == $member->id ? 'selected' : '' }}>
                    {{ $member->name }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label for="item">Item Name/ID:</label>
            <input type="text" name="item" id="item" class="form-control" value="{{ request('item') }}">
        </div>
        <div class="col-md-3">
            <label for="from_date">From Date:</label>
            <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>
        <div class="col-md-3">
            <label for="to_date">To Date:</label>
            <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>
        <div class="col-md-3 align-self-end mt-3">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </div>
</form>

<div class="table-responsive">
    <table id="items_table" class="table table-bordered">
        <thead>
            <tr>
                <th scope="col">Member</th>
                <th scope="col">Item Name</th>
                <th scope="col">Group</th>
                <th scope="col">Reserved At</th>
                <th scope="col">Due Date</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reservationReport as $item)
            <tr>
                <td>{{ $item->reserver->name ?? 'N/A' }}</td>
                <td>{{ $item->book->name ?? 'N/A' }}</td>
                <td>{{ $item->book->group->title ?? 'N/A' }}</td>
                <td>{{ $item->reserved_at ?? 'N/A' }}</td>
                <td>{{ $item->due_date ?? 'N/A' }}</td>
                <td>{{ $item->state == 'return-request' ? 'Return requested' : 'Not Return Yet' }}</td>
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
                    , title: 'Dixwix: Reservation Report'
                }
                , {
                    extend: 'excelHtml5'
                    , text: 'Export Excel'
                    , title: 'Dixwix: Reservation Report'
                }
                , {
                    extend: 'print'
                    , text: 'Print'
                    , title: 'Dixwix: Reservation Report'
                }
            ]
        });
    });

</script>
