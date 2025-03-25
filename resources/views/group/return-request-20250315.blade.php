<div class="table-group-details table-responsive mt-5">
    <table id="items_table_requests" class="table table-striped table-rounded">
        <thead>
            <tr>
                <th scope="col">Thumbnail</th>
                <th scope="col">Item Name</th>
                <th scope="col">Image At Return</th>
                <th scope="col">In Original Condition</th>
                <th scope="col">Item ID</th>
                <th scope="col">Requested By</th>
                <th scope="col">Trust Scores</th>
                <th scope="col">Due Date</th>
                <th scope="col">Requested Date</th>
                <th scope="col">Type</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody>
            {{-- Loop through normal return requests --}}
@foreach($group['returnRequests'] as $entry)
    @php 
        // Get the user by ID and handle case where user might not exist
        $user = \App\Models\User::find($entry['reserved_by']);
        $requestedBy = $user ? $user->name : 'Unknown User'; 

        // Get the book and handle case where book might not exist
        $book = $entry['book'] ?? null;
    @endphp
    @if($book) {{-- Only display the row if the book exists --}}
        <tr>
            <td>
                <img style="width: 100px; height:100px" src="{{ $book['cover_page'] }}" alt="Cover Page">
            </td>
            <td>{{ $book['name'] }}</td>
            <td>
                @if($entry['state'] === 'return-request')
                    <a href="{{ $entry['image_at_returning'] }}" target="_blank">
                        <img style="width: 100px; height:100px" src="{{ $entry['image_at_returning'] }}" alt="Image">
                    </a>
                @else
                    <span>Pending</span>
                @endif
            </td>
            <td>
                <span class="badge badge-{{ $entry['original_condition'] == 'yes' ? 'success' : 'warning' }}">{{ $entry['original_condition'] }}</span>
            </td>
            <td>{{ $book['item_id'] }}</td>
            <td>{{ $requestedBy }}</td>
            <td>{{ $entry['average_rating'] ?? 'N/A' }}</td>
            <td>
                @php
                    $dueDate = \Carbon\Carbon::parse($entry['due_date']);
                    $isOverdue = $dueDate->isPast(); // Check if the due date is in the past
                @endphp

                @if($isOverdue)
                    <span class="badge bg-danger text-white" style="font-size: 12px;">{{ $dueDate->format('d-m-Y') }}</span>
                @else
                    {{ $dueDate->format('d-m-Y') }}
                @endif

                @if($isOverdue)
                    <span class="badge bg-danger text-white">Overdue</span>
                @endif
            </td>
            <td>{{ \Carbon\Carbon::parse($entry['reserved_at'])->format('d-m-Y') }}</td>

            <td>
                {{ 
                    ($entry['is_reserved'] == 2) ? 'Rental' : 
                    ($entry['state'] == 'return-request' ? 'Return' : 'Not Available') 
                }}
            </td>

            <td>
                @if($entry['state'] == 'rejected')
                    <span class="badge bg-danger text-white">Rejected</span>
                @elseif($entry['is_reserved'] == '2' && $entry['reserved_by'] != auth()->id())
                    <div class="btn-group">
                        <a href="javascript:void(0)" onclick="approveDisapprove('approve', {{ $entry['id'] }}, {{ $entry['book']['id'] }}, this)" class="btn text-nowrap btn-success">Approve</a>
                        <a href="javascript:void(0)" onclick="approveDisapprove('disapprove', {{ $entry['id'] }}, {{ $entry['book']['id'] }}, this)" class="btn text-nowrap btn-danger">Reject</a>
                    </div>
                @elseif($entry['state'] === 'return-request' && $entry['reserved_by'] != auth()->id())
                    <div class="btn-group">
                        <a href="javascript:void(0)" onclick="adminReturnBook({{ $entry['id'] }}, {{ $entry['book_id'] }}, event)" class="btn text-nowrap btn-success">Approve</a>
                        <a href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to reject the return request?','question',function(){rejectReturnRequest({{ $entry['id'] }})})" class="btn text-nowrap btn-danger">Reject</a>
                    </div>
                @else
                    <span>Pending</span>
                @endif
            </td>
        </tr>
    @endif
    @endforeach

    {{-- Loop through rejected items --}}
    @foreach($group['itemMetrics']['rejected_items'] as $rejectedItem)
    @php
    $book = \App\Models\Book::find($rejectedItem['book_id']);
    $user = \App\Models\User::find($rejectedItem['disapproved_by']);
    @endphp
    <tr style="background-color: #f8d7da;">  
        <td>
            <img style="width: 100px; height:100px" src="{{ $book->cover_page }}" alt="Cover Page">
        </td>
        <td>{{ $book->name }}</td>
        <td>
            <span>Image Not Available</span>
        </td>
        <td>
            <span class="badge badge-danger">Not Applicable</span>
        </td>
        <td>{{ $book->item_id }}</td>
        <td>{{ $user?->name ?? 'Unknown' }}</td>
        <td>N/A</td>
        <td>
            <span class="badge bg-danger text-white">{{ \Carbon\Carbon::parse($rejectedItem['disapproved_at'])->format('d-m-Y') }}</span>
        </td>
        <td>
            <span class="badge bg-danger text-white">{{ \Carbon\Carbon::parse($rejectedItem['disapproved_at'])->format('d-m-Y') }}</span>
        </td>
        
        <td>Rejected</td>

        <td>
            <span class="badge bg-danger text-white">Rejected</span>
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
