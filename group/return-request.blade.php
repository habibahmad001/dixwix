<div class="table-group-details table-responsive mt-5">
    <table id="items_table_requests" class="table table-striped table-rounded">
        <thead>
            <tr>
                <th scope="col">Thumbnail</th>
                <th scope="col">Item Name</th>
                <th scope="col">Image At Reservation</th>
                <th scope="col">Image At Return</th>
                <th scope="col">Item ID</th>
                <th scope="col">Requested By</th>
                <th scope="col">Trust Scores</th>
                <th scope="col">Due Date</th>
                <th scope="col">Requested Date</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($group['returnRequests'] as $entry)
            @php
            $user = \App\Models\User::find($entry['reserved_by']);
            $requestedBy = $user?->name ?? 'Unknown User';
            $book = $entry['book'];
            @endphp
            <tr>
                <td>
                    <img style="width: 125px; height:125px" src="{{ $book['cover_page'] }}" alt="Cover Page">
                </td>
                <td>{{ $book['name'] }}</td>
                <td>
                    <a href="{{ $entry['image_at_reservering'] }}" target="_blank">
                        <img style="width: 125px; height:125px" src="{{ $entry['image_at_reservering'] }}" alt="Image">
                    </a>
                </td>
                <td>
                    @if($entry['state'] === 'return-request')
                    <a href="{{ $entry['image_at_returning'] }}" target="_blank">
                        <img style="width: 125px; height:125px" src="{{ $entry['image_at_returning'] }}" alt="Image">
                    </a>
                    @else
                    <span>Pending</span>
                    @endif
                </td>
                <td>{{ $book['item_id'] }}</td>
                <td>{{ $requestedBy }}</td>
                <td>{{ $entry['average_rating'] ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($entry['due_date'])->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($entry['reserved_at'])->format('d-m-Y') }}</td>
                <td>
                    @if($entry['state'] === 'return-request')
                    <div class="btn-group">
                        <a href="javascript:void(0)" onclick="adminReturnBook({{ $entry['id'] }}, {{ $entry['book_id'] }}, event)" class="btn btn-success">Approve</a>
                        <a href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to reject the return request?','question',function(){rejectReturnRequest({{ $entry['id'] }})})" class="btn btn-danger">Reject</a>
                    </div>
                    @elseif($entry['state'] == 'rejected')
                    <span class="badge bg-danger text-white">Rejected</span>
                    @else
                    <span>Not Returned</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    function rejectReturnRequest(entry_id) {
        jQuery.ajax({
            type: 'POST'
            , url: '/reject-return-request'
            , data: {
                "_token": "<?=csrf_token()?>"
                , "entry_id": entry_id
            }
            , success: function(result) {
                window.location.reload();
            }
        });
    }

</script>
