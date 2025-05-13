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
                <th scope="col">User</th>
                <th scope="col">Coins</th>
                <th scope="col">Amount</th>
                <th scope="col">Approved By</th>
                <th scope="col">Action At</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction?->user?->name }}</td>
                <td>{{ $transaction->coins }}</td>
                <td>{{ $transaction->amount }}</td>
                <td>{{ $transaction->approveUser->name??"Auto" }}</td>
                <td>{{ $transaction->approved_at ?? $transaction->created_at }}</td>
                <td>{{ $transaction->status_text??"-" }}</td>
                <td>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('edit-redeem-requests-user', ['id' => $transaction->id])  }}">
                            <i class="fa fa-edit" style="color: darkblue; font-size: 15px"></i>
                        </a>
                        &nbsp;
                        <a href="{{ route('delete-redeem-requests-user', ['id' => $transaction->id])  }}">
                            <i class="fa fa-trash" style="color: maroon; font-size: 15px"></i>
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
