
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

    </style>
<div class="container mt-5">
        <div class="row">
            <!-- Flowz Card -->
            <div class="col-md-3 mb-4">
                <div class="card-counter danger" id="flowz-card">
                    <i class="fa fa-clock"></i>
                    <span class="count-numbers">{{$data['reservedBooks']}}</span>
                    <span class="count-name">Overdue</span>
                </div>
            </div>

            <!-- Instances Card -->
            <div class="col-md-3 mb-4">
                <div class="card-counter" style="background-color: #8775A7; color: #fff;" id="instances-card">
                    <i class="fa fa-ticket"></i>
                    <span class="count-numbers">0</span>
                    <span class="count-name">Completed Loans</span>
                </div>
            </div>

            <!-- Data Card -->
            <div class="col-md-3 mb-4">
                <div class="card-counter info" id="data-card">
                    <i class="fa fa-users"></i>
                    <span class="count-numbers">{{$data['totalusers']}}</span>
                    <span class="count-name">Customers</span>
                </div>
            </div>

            <!-- Users Card -->
            <div class="col-md-3 mb-4">
                <div class="card-counter success" id="users-card">
                    <i class="fa fa-list"></i>
                    <span class="count-numbers text-white">{{$data['totalItemsCount']}}</span>
                    <span class="count-name text-white">Items</span>
                </div>
            </div>
        </div>
    </div>

<h2>Item Metrics</h2>
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
</div>

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
        <div class="card shadow-sm widget-bg" style="border-top: 3px solid #094042;">
            <div class="card-body text-center">
                <p class="card-text"><span class="item-type-book">{{ $category['category']['name'] }}</span></p>
                <p class="card-text"><strong>Total items:</strong> {{ $category['total'] }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<h2>Group Metrics</h2>
<div class="row">
    @php
    $totalGroups = count($data['groupMetrics']);
    $columnClass = $totalGroups == 1 ? 'col-12' : ($totalGroups == 2 ? 'col-md-6 col-sm-12' : ($totalGroups == 3 ? 'col-md-4 col-sm-6' : 'col-md-3 col-sm-6'));
    @endphp

    @foreach ($data['groupMetrics'] as $group)
    <div class="{{ $columnClass }} mb-4">
        <div class="card shadow-sm widget-bg" style="border-top: 3px solid #094042;">
            <div class="card-body text-center">
                <p class="card-text mb-0"><span class="item-type-book">{{ $group['group_title'] }}</span></p>
                <p class="card-text mb-0"><strong>Group Total Items:</strong> {{ $group['total_items'] }}</p>
                <p class="card-text mb-0"><strong>Total Savings:</strong> {{ number_format($group['savings'], 2) }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
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
