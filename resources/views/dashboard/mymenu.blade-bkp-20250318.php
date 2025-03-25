<h2>{{ $data['title'] }}</h2>

@if(count($data['nodata']) == 0)
    <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: red;">Data not found</p>
@else
    <div class="table-responsive">
        <table id="items_table1" class="table data-table-format table-bordered table-hover">
            <thead class="bg-head">
                @if (isset($data['overdueBooks']))
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Reserved By</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
                @elseif(isset($data['itemrented']))
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Reserved By</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Group</th>
                    <th class="text-center">Actions</th>
                </tr>
                @elseif(isset($data['customers']))
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                @elseif(isset($data['itemsall']))
                <tr>
                    <th>Sr.NO.</th>
                    <th>Title </th>
                    <th>Author</th>
                </tr>

                @elseif(isset($data['itemrejected']))
                <tr>
                    <th>Image</th>
                    <th>Title </th>
                    <th>Request by</th>
                    <th>Rejected by</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                @endif
            </thead>
            <tbody>
                @if (isset($data['overdueBooks']))
                    @foreach ($data['overdueBooks'] as $book)
                        <tr>
                            <td><img src="{{ $book->cover_page }}" alt="Book Cover" style="width: 50px; height: 50px;"></td>
                            <td>{{ $book->title }}</td>
                            <td>{{ $book->reserved_by_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($book->due_date)->format('d-m-Y') }}</td>
                            <td>
                                @if($book->state == 'return-request')
                                    <span class="badge bg-warning text-white">Return Request</span>
                                @elseif($book->state == 'approved')
                                    <span class="badge bg-success text-white">Approved</span>
                                @else
                                    <span class="badge bg-danger text-white">Overdue</span>
                                @endif
                            </td>
                            <td align="center">
                                <a href="{{ route('show-group', $book->group_id) }}" class="btn btn-info btn-sm">View</a>
                            </td>
                        </tr>
                    @endforeach
            @elseif(isset($data['customers']))
                @foreach($data['customers'] as $client)
                    <tr>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->email }}</td> 
                    </tr>
                @endforeach
            @elseif(isset($data['itemsall']))
                @php
                    $ibx = 0;
                @endphp
                @foreach($data['itemsall'] as $item)
                    @php
                        $ibx++;
                    @endphp
                    <tr>
                        <td align="center">{{ $ibx }}</td>
                        <td><a href="{{ route('show-item', $item->id) }}" style="text-decoration: none;color: var(--green-dark-01);">{{ $item->title }}</a></td>
                        <td>{{ $item->author }}</td> 
                    </tr>
                @endforeach

            @elseif(isset($data['itemrented']))
            @foreach ($data['itemrented'] as $book)

            <?php $imgtx='storage/media/logo.png';if (isset($book->cover_page) && trim($book->cover_page)!="" && trim($book->cover_page)!="media/logo.png") {
                $imgtx='storage/'.$book->cover_page;
            } ?>
            <tr>
                <td><img src="https://www.dixwix.com/{{ $imgtx }}" alt="Book Cover" style="width: 50px; height: 50px;"></td>
                <td>{{ $book->title }}</td>
                <td>{{ $book->reserved_by_name }}</td>
                <td>{{ \Carbon\Carbon::parse($book->due_date)->format('d-m-Y') }}</td>
                <?php /*<td>
                    @if($book->state == 'return-request')
                        <span class="badge bg-warning text-white">Return Request</span>
                    @elseif($book->state == 'approved')
                        <span class="badge bg-success text-white">Approved</span>
                    @else
                        <span class="badge bg-danger text-white">Overdue</span>
                    @endif
                </td>*/ ?>

                <td>
                    @php
                        $dueDate = \Carbon\Carbon::parse($book->due_date);
                    @endphp

                    @if($book->state == 'return-request')
                        <span class="badge bg-warning text-white">Return Request</span>
                    @elseif($book->state == 'approved')
                        <span class="badge bg-success text-white">Approved</span>
                    @elseif($dueDate->isPast())  
                        <span class="badge bg-danger text-white">Overdue</span>
                    @else
                        <span class="badge bg-info text-white">Due {{ $dueDate->format('d-m-Y') }}</span>  
                    @endif
                </td>

                <td>{{ $book->groupName }}</td> <!-- Display the group name here -->
                <td align="center">
                    <a href="{{ route('show-group', $book->group_id) }}" class="btn btn-info btn-sm">View</a>
                </td>
            </tr>
            @endforeach

            @elseif(isset($data['itemrejected']))
            @foreach ($data['itemrejected'] as $item)
                @php
                    // Check if the payload is already an array, otherwise decode it
                    $payload = is_array($item->payload) ? $item->payload : json_decode($item->payload, true);
                    $groupId = isset($payload['group_id']) ? $payload['group_id'] : null; // Extract group_id
                @endphp

                <?php $imgtx='storage/media/logo.png';if (isset($item->cover_page) && trim($item->cover_page)!="" && trim($item->cover_page)!="media/logo.png") {
                    $imgtx='storage/'.$item->cover_page;
                } ?>
                
                <tr>
                    <td><img src="https://www.dixwix.com/{{ $imgtx }}" alt="Book Cover" style="width: 50px; height: 50px;"></td>
                    <td>{{ $item->book_title }}</td>
                    <td>{{ $item->user_name }}</td>
                    <td>{{ $item->disapprover_name }}</td>
                    <td>
                        <span class="badge bg-danger text-white">Rejected</span>
                    </td>
                    <td align="center">
                        @if ($groupId) <!-- Only show the button if group_id exists -->
                            <a href="{{ route('show-group', $groupId) }}" class="btn btn-info btn-sm">View</a>
                        @else
                            <span>No Group</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            @endif

                {{-- Add other conditions for items, rented items, and customers if needed --}}
            </tbody>
        </table>
    </div>
@endif

<!-- Include jQuery and DataTable script -->
<link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('.data-table-format').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            responsive: true,
            pageLength: 10
        });
    });
</script>

<style>
    .widget-bg {
        background-color: white !important;
    }
    .bg-head {
        background: #094042;
        color: white;
    }
    .bg-head th:first-child {
        border-top-left-radius: 10px;
    }
    .bg-head th:last-child {
        border-top-right-radius: 10px;
    }
</style>
