
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
<style>
        .card-counter {
            box-shadow: 2px 2px 10px #DADADA;
            margin: 5px;
            padding: 20px 10px;
            background-color: #fff;
            height: 100px;
            border-radius: 5px;
            transition: .3s linear all;
            position: relative;
        }

        .card-counter:hover {
            box-shadow: 4px 4px 20px #DADADA;
            transition: .3s linear all;
        }

        /* Primary Card */
        .card-counter.primary {
            background-color: #007bff;
            color: #FFF;
        }

        /* Danger Card */
        .card-counter.danger {
            background-color: #ef5350;
            color: #FFF;
        }

        /* Success Card */
        .card-counter.success {
            background-color: #66bb6a;
            color: #FFF;
        }

        /* Info Card */
        .card-counter.info {
            background-color: #26c6da;
            color: #FFF;
        }

        .card-counter i {
            font-size: 5em;
            opacity: 0.2;
        }

        .card-counter .count-numbers {
            position: absolute;
            right: 35px;
            top: 20px;
            font-size: 32px;
            display: block;
        }

        .card-counter .count-name {
            position: absolute;
            right: 35px;
            top: 65px;
            font-style: italic;
            text-transform: capitalize;
            opacity: 0.5;
            display: block;
            font-size: 18px;
        }

        /*///////////////////////////////////////////////////////////////////////////////////////////*/
        .card-counter2 {
            box-shadow: 2px 2px 10px #DADADA;
            margin: 5px;
            padding: 20px 10px;
            background-color: #fff;
            height: 100px;
            border-radius: 5px;
            transition: .3s linear all;
            position: relative;
        }

        .card-counter2:hover {
            box-shadow: 4px 4px 20px #DADADA;
            transition: .3s linear all;
        }

        /* Primary Card */
        .card-counter2.primary {
            background-color: #007bff;
            color: #FFF;
        }

        /* Danger Card */
        .card-counter2.danger {
            background-color: #ef5350;
            color: #FFF;
        }

        /* Success Card */
        .card-counter2.success {
            background-color: #66bb6a;
            color: #FFF;
        }

        /* Info Card */
        .card-counter2.info {
            background-color: #26c6da;
            color: #FFF;
        }

        .card-counter2 i {
            font-size: 5em;
            opacity: 0.2;
        }

        .card-counter2 .count-numbers {
            position: absolute;
            right: 35px;
            top: 20px;
            font-size: 32px;
            display: block;
        }

        .card-counter2 .count-name {
            position: absolute;
            right: 35px;
            top: 65px;
            font-style: italic;
            text-transform: capitalize;
            opacity: 0.5;
            display: block;
            font-size: 18px;
        }


    </style>
<?php $isAdmin=false;$mCls=3;
        if (Auth::user()->hasRole('admin')) {
            $isAdmin=true;
            $mCls=3;
        } ?>
        <div class="row">
            <!-- Flowz Card -->
            <div class="col-md-{{$mCls}} mb-4">
                <div class="card-counter danger" id="flowz-card" data-id='overdue'>
                    <i class="fa fa-clock"></i>
                    <span class="count-numbers">{{$data['reservedBooks']}}</span>
                    <span class="count-name">Overdue</span>
                </div>
            </div>

            <!-- Instances Card -->
            <div class="col-md-{{$mCls}} mb-4">
                <div class="card-counter" style="background-color: #8775A7; color: #fff;" id="instances-card">
                    <i class="fa fa-ticket"></i>
                    <span class="count-numbers">0</span>
                    <span class="count-name">Completed Loans</span>
                </div>
            </div>

            
            <!-- Data Card -->
            <div class="col-md-{{$mCls}} mb-4">
                <div class="card-counter info" id="data-card">
                    <i class="fa fa-users"></i>
                    <span class="count-numbers"><?php if($isAdmin){echo$data['totalusers'];}else{echo'1';}?></span>
                    <span class="count-name">Customers</span>
                </div>
            </div>

            <!-- Users Card -->
            <div class="col-md-{{$mCls}} mb-4">
                <div class="card-counter success" id="users-card" data-id='itemsall'>
                    <i class="fa fa-list"></i>
                    <span class="count-numbers ">{{$data['totalItemsCount']}}</span>
                    <span class="count-name ">Items</span>
                </div>
            </div>
        </div>

<style>
    /* Glassy Card Effect with Tile Border */
    .card-counter {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        margin: 5px;
        padding: 20px 10px;
        background: rgba(255, 255, 255, 0.1); /* Transparent background */
        backdrop-filter: blur(10px); /* Frosted glass effect */
        border-radius: 15px;
        transition: all 0.3s ease;
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.3); /* Light border to create contrast */
    }

    /* Tile Border - Left Side */
    .card-counter::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 8px;
        background: linear-gradient(to bottom, #0101, #0101, #0101, #0101); /* Gradient color */
        border-radius: 15px 0 0 5px; /* Rounded corners for the left side */
    }

    .card-counter:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15), 0 2px 4px rgba(0, 0, 0, 0.1);
        transform: translateY(-5px); /* Slight lift effect */
        transition: all 0.3s ease;
    }

    /* Color Variations for Cards */
    .card-counter.primary {
        background: rgba(0, 123, 255, 0.1);
        color: #007bff;
    }

    .card-counter.danger {
        background: rgba(239, 83, 80, 0.1);
        color: #ef5350;
    }

    .card-counter.success {
        background: rgba(102, 187, 106, 0.1);
        color: #66bb6a;
    }

    .card-counter.info {
        background: rgba(38, 198, 218, 0.1);
        color: #26c6da;
    }

    .card-counter i {
        font-size: 3.5em;
        opacity: 0.6;
    }

    .card-counter .count-numbers {
        position: absolute;
        right: 35px;
        top: 20px;
        font-size: 32px;
        font-weight: bold;
        display: block;
    }

    .card-counter .count-name {
        position: absolute;
        right: 35px;
        top: 65px;
        font-style: italic;
        text-transform: capitalize;
        opacity: 0.7;
        display: block;
        font-size: 18px;
    }
</style>

<h2>Items By Category</h2>
@if (count($data['itemMetrics']['total_items_by_category']) == 0)
    <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: red;">Items by category</p>
@else
    <div class="row">
        @php
            $totalItems = count($data['itemMetrics']['total_items_by_category']);
            $columnClass = $totalItems == 1 ? 'col-12' : ($totalItems == 2 ? 'col-md-6 col-sm-12' : ($totalItems == 3 ? 'col-md-4 col-sm-6' : 'col-md-3 col-sm-6'));
        @endphp

        @foreach ($data['itemMetrics']['total_items_by_category'] as $category)
            <div class="{{ $columnClass }} mb-4">
                <div class="card-counter2" style="background-color: #4caf50; color: white;">
                    <i class="fa fa-th-large"></i>
                    <span class="count-numbers">{{ $category['total'] }}</span>
                    <span class="count-name">
                        {{ isset($category['category']['name']) ? $category['category']['name'] : 'Category Name Not Available' }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
@endif


<span style="float: left;">Item Metrics</span><span style="float: right;">Group Metrics</span>
<br>
<div class="row d-flex justify-content-stretch">
    <!-- Rented Items Card -->
    <div class="col-md-4 col-sm-4 mb-4">
        <div class="card-counter primary" data-id='itemrented'>
            <i class="fa fa-cogs"></i>
            <span class="count-numbers">{{ $data['itemMetrics']['items_rented_out']->count() }}</span>
            <span class="count-name">My Items Rented Out</span>
        </div>
    </div>

    <!-- Rejected Items Card -->
    <div class="col-md-4 col-sm-4 mb-4">
        <div class="card-counter danger" data-id='itemrejected'>
            <i class="fa fa-ban"></i>
            <span class="count-numbers">{{ count($data['itemMetrics']['rejected_items']) }}</span>
            <span class="count-name">Rejected Items</span>
        </div>
    </div>

    @php
        $totalGroups = count($data['groupMetrics']);
        $columnClass = $totalGroups == 1 ? 'col-12' : ($totalGroups == 2 ? 'col-md-6 col-sm-12' : ($totalGroups == 3 ? 'col-md-4 col-sm-6' : 'col-md-3 col-sm-6'));
    @endphp

    @foreach ($data['groupMetrics'] as $group)
        <div class="col-md-4 col-sm-4 mb-4">
            <div class="card-counter2" style="background-color: #2196F3; color: white;">
                <i class="fa fa-users"></i>
                <span class="count-numbers">{{ $group['total_items'] }}</span>
                <?php /*<span class="count-name" style="margin-right: 80px !important;">{{ $group['group_title'] }}</span>*/ ?>
                <span class="count-name"><strong style="margin-right: 5px !important;">Total Savings:</strong> {{ number_format($group['savings'], 2) }}</span>
            </div>
        </div>
    @endforeach

</div>

<!-- <h2>Item Metrics</h2>
<div class="row d-flex justify-content-stretch">
    <div class="col-md-6 col-sm-6 mb-4">
        <div class="card shadow-sm h-100 widget-bg" style="border-top: 3px solid #094042;">
            <div class="card-body text-center" style="max-height: 400px !important; overflow: auto;">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="card-title">My Items Rented Out</h5>
                    <span class="badge badge-success p-2 d-flex justify-content-center align-items-center">{{ $data['itemMetrics']['items_rented_out']->count() }}</span>
                </div>
                @if($data['itemMetrics']['items_rented_out']->count() > 0)
                <ul class="list-group">
                    @foreach($data['itemMetrics']['items_rented_out'] as $entry)
                    <li class="list-group-item d-flex justify-content-between align-items-end shadow-sm mb-2">
                        <div class="ms-2 text-left">
                            <div class="item-type-book">{{ $entry['book']['name'] }}</div>
                            <p class="mb-1"><strong>Reserved By:</strong> {{ $entry['reserver']['name'] }}</p>
                            <p class="mb-1"><strong>Reserved At:</strong> {{ \Carbon\Carbon::parse($entry->reserved_at)->format('Y-m-d') }}</p>
                        </div>
                        <div>
                            <p class="mb-1"><strong>Item Group:</strong> {{ $entry['book']['group']['title'] }}</p>
                            <p class="mb-1"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($entry->due_date)->format('Y-m-d') }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="d-flex justify-content-center align-items-center h-50">No items rented out.</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6 col-sm-6 mb-4">
        <div class="card shadow-sm h-100 widget-bg" style="border-top: 3px solid #094042;">
            <div class="card-body text-center" style="max-height: 400px !important; overflow: auto;">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="card-title">Rejected Items</h5>
                    <span class="badge badge-success p-2 d-flex justify-content-center align-items-center">{{ count($data['itemMetrics']['rejected_items']) }}</span>
                </div>
                @if (count($data['itemMetrics']['rejected_items']) == 0)
                <div class="d-flex justify-content-center align-items-center h-50">No rejected items found.</div>
                @else
                <ul class="list-group">
                    @foreach ($data['itemMetrics']['rejected_items'] as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-end shadow-sm mb-2">
                        <div class="ms-2 text-left">
                            <div class="item-type-book">{{ $item['book']['name'] }}</div>
                            <p class="mb-1"><strong>Rejected By:</strong> {{ $item['disapprover']['name'] }}</p>
                            <p class="mb-1"><strong>Rejection Reason:</strong> {{ $item['reason'] }}</p>
                        </div>
                        <div>
                            <p class="mb-1"><strong>Rejection Date:</strong> {{ \Carbon\Carbon::parse($item['disapproved_at'])->format('Y-m-d') }}</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div> -->




<?php /*<h2>Group Metrics</h2>
<div class="row">
    @php
        $totalGroups = count($data['groupMetrics']);
        $columnClass = $totalGroups == 1 ? 'col-12' : ($totalGroups == 2 ? 'col-md-6 col-sm-12' : ($totalGroups == 3 ? 'col-md-4 col-sm-6' : 'col-md-3 col-sm-6'));
    @endphp

    @foreach ($data['groupMetrics'] as $group)
        <div class="{{ $columnClass }} mb-4">
            <div class="card-counter2" style="background-color: #2196F3; color: white;">
                <i class="fa fa-users"></i>
                <span class="count-numbers">{{ $group['total_items'] }}</span>
                <span class="count-name" style="margin-right: 80px !important;">{{ $group['group_title'] }}</span>
                <span class="count-name"><strong style="margin-right: 180px !important;">Total Savings:</strong> {{ number_format($group['savings'], 2) }}</span>
            </div>
        </div>
    @endforeach
</div>*/ ?>


@php
    use App\Models\Groupmember;
@endphp
@php
    $group_member = Groupmember::with(['group', 'member'])
    ->where('member_id', Auth::user()->id)
    ->where('member_role', 'admin')  
    ->get(); 
@endphp

@if(Auth::user()->hasRole('admin') || $group_member->isNotEmpty())

<h2>Items categories</h2>
@if(count($data["groups"]) == 0)
<p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: red;">Group not found</p>
@else
<div class="table-responsive">
    <table id="items_table1" class="table data-table-format table-bordered table-hover">
        <thead class="bg-head">
            <tr>
                <th>Name</th>
                <th>Rent Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['types'] as $category)
            <tr>
                <td>
                    <h3 class="lead main-heading">{{ $category['name'] }}</h3>
                </td>
                <td>
                    <p>{{ $category['percentage'] }}</p>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
<hr>
<h2>All Groups</h2>
@if(count($data["groups"]) == 0)
<p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: red;">Group not found</p>
@else
<div class="table-responsive">
    <table id="items_table1" class="table data-table-format table-bordered table-hover">
        <thead class="bg-head">
            <tr>
                <th>Image</th>
                <th>Title</th>
                <th>Created At</th>
                <th>Status</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['groups'] as $group)
            <tr>
                <td>
                    @if (!empty($group["group_picture"]))
                    <img src="{{ asset('storage/'.$group["group_picture"]) }}" alt="Group Image" style="width: 80px; height: auto;">
                    @else
                    <img src="assets/media/placeholder.png" alt="No Image" style="width: 80px; height: auto;">
                    @endif
                </td>
                <td>
                    <h3 class="lead main-heading">{{ $group['title'] }}</h3>
                </td>
                <td>
                    {{ date('M/d/Y', strtotime($group['created_at'])) }}
                </td>
                <td>
                    @if (!empty($group["to_be_deleted_at"]))
                    <span class="badge bg-danger text-white">Marked Deleted</span>
                    @else
                    <span class="badge bg-success text-white">Active</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex justify-content-center gap-2" style="gap:2px">
                        <a href="{{ route('show-group', ['id' => $group->id]) }}" class="btn btn-primary btn-sm">
                            <img src="assets/media/eye-outline.png" alt="View Group" style="width: 16px;"> View
                        </a>
                        @if (Auth::user()->hasRole("admin") || $group["created_by"] == Auth::user()->id)
                        <a href="{{ route('edit-group', ['id' => $group['id']]) }}" class="btn btn-warning btn-sm" title="Edit Group">
                            <img src="assets/media/edit-orange.png" alt="Edit Group" style="width: 16px;"> Edit
                        </a>
                        @endif
                        @if ($group["created_by"] == Auth::user()->id && empty($group["to_be_deleted_at"]))
                        <a href="javascript:void(0)" class="btn btn-secondary btn-sm" onclick="getMembersToAdd('{{ route('get-members-to-add', ['group_id' => $group['id'], 'group_type_id' => $group['group_type_id']]) }}','{{ $group['group_type_id'] }}')">
                            <img src="assets/media/add-circle-outline.png" alt="Invite" style="width: 16px;"> Invite
                        </a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif


@endif

<link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<script src="//code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).on('click', '#getMembersToAdd', function() {
        let group_id = $(this).data('group_id');
        let group_type_id = $(this).data('group_type_id');
        $('#group_id_modal').val(group_id);
        $('#group_type_id_modal').val(group_type_id);
    });

    $(document).ready(function() {
        $('.data-table-format').DataTable({
            paging: true
            , searching: true
            , ordering: true
            , responsive: true
            , pageLength: 10
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

<div class="modal" id="customModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Item Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Data will be populated here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">$(document).ready(function() {
    // Attach double-click event to all cards
    $('.card-counter').on('dblclick', function() {
        var cardId = $(this).data('id'); // Get the data-id of the clicked card
        
        // Send AJAX request
        $.ajax({
            url: "/get-item-details", // Replace with your API URL
            method: "GET",
            data: {
                card_id: cardId
            },
            success: function(response) {
                if (response.success) {
                    // Populate modal with data
                    $('#modalTitle').text(response.data.title); // Assuming response has title field
                    
                    // Check if it's the overdue books case
                    if (cardId === 'overdue') {
                        // Reserved count

                        /*<p>Total Overdue Books: ${response.data.reservedCount}</p>*/

                        $('#modalBody').html(`
                            <ul>
                                ${response.data.overdueBooks.map(book => `
                                    <li>
                                        <strong>${book.title}</strong><br>
                                        Due Date: ${book.due_date} <br>
                                        Reserved By: ${book.reserved_by}
                                    </li>
                                `).join('')}
                            </ul>
                        `);
                    }else if (cardId === 'itemsall') {
                        $('#modalBody').html(`
                            <p>Total Items: ${response.data.totalItemsCount}</p>
                            <ul>
                                ${response.data.items.map(item => `
                                    <li>
                                        <strong>${item.title}</strong> by ${item.author} <br>
                                        Created At: ${item.created_at}
                                    </li>
                                `).join('')}
                            </ul>
                        `);
                    }else if (cardId === 'itemrented') {
                        $('#modalBody').html(`
                            <ul>
                                ${response.data.renteditem.map(entry => `
                                    <li>
                                        <strong>Book Title:</strong> ${entry.name} <br>
                                        <strong>Reserved By:</strong> ${entry.usersName} <br>
                                        <strong>Reserved At:</strong> ${entry.reserved_at} <br>
                                        <strong>Due Date:</strong> ${entry.due_date} <br>
                                    </li>
                                `).join('')}
                            </ul>
                        `);

                    }else if (cardId === 'itemrejected') {
                        $('#modalBody').html(`
                            <ul>
                                ${response.data.rejectedItems.map(item => `
                                    <li>
                                        <strong>Book Title:</strong> ${item.book_title} <br>
                                        <strong>Rejected By:</strong> ${item.disapprover_name} <br>
                                        <strong>Requested By:</strong> ${item.user_name} <br>
                                        <strong>Rejection Reason:</strong> ${item.reason} <br>
                                        <strong>Rejection Date:</strong> ${item.disapproved_at} <br>
                                    </li>
                                `).join('')}
                            </ul>
                        `);

                    } else {
                        $('#modalBody').html(response.data.details); // Populate other details for different cards
                    }

                    // Show the modal
                    $('#customModal').modal('show');
                } else {
                    alert("No data found!");
                }
            },
            error: function(xhr, status, error) {
                console.error("An error occurred: " + error);
            }
        });
    });
});
</script>