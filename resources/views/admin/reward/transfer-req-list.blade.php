<div class="inner_content_table_actions d-flex flex-column flex-md-row mt-4">
    <h4 class="inner_content_title">{{ $data['title'] }}</h4>
</div>

@if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
@endif

<div class="table-responsive">
    <table id="items_table" class="table">
        <thead>
            <tr>
                <th scope="col">From User</th>
                <th scope="col">To User</th>
                <th scope="col">Points</th>
                <th scope="col">Approved By</th>
                <th scope="col">Approved At</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach($transferRequests as $transferRequest)
            <tr>
                <td>{{ $transferRequest->fromUser->name ?? "-" }}</td>
                <td>{{ $transferRequest->toUser->name ?? "-" }}</td>
                <td>{{ $transferRequest->points }}</td>
                <td>{{ $transferRequest->approveUser->name??"-" }}</td>
                <td>{{ $transferRequest->approved_at??"-" }}</td>
                <td>{{ $transferRequest->status_text??"-" }}</td>
                <td>
                    <div class="d-flex justify-content-center">
                        <a href="{{ $transferRequest->status == \App\Models\TransferRequest::PENDING ? route('edit-transfer-requests', ['id' => $transferRequest->id]) :  'javascript:void(0);' }}"
                           style="{{ $transferRequest->status == 1 ? 'cursor: not-allowed; opacity: 0.7;' : '' }}">
                            <img src="{{ url("assets/media/edit-orange.png") }}" width="15px" height="15px" />
                        </a>
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
    });

</script>
