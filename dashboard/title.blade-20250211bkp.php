<h2>Item Metrics</h2>
<div class="row">
    <div class="col-md-6 col-sm-6 mb-4">
        <div class="card shadow-sm widget-bg">
            <div class="card-body text-center">
                <h5 class="card-title">My Items Rented Out</h5>
                <p class="card-text display-4">{{ $data['itemMetrics']['items_rented_out']->count() }}</p>
                @if($data['itemMetrics']['items_rented_out']->count() > 0)
                <ul class="list-group">
                    @foreach($data['itemMetrics']['items_rented_out'] as $entry)
                    <li class="list-group-item d-flex align-items-center justify-content-between align-items-start shadow-sm mb-3">
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
                <p>No items rented out.</p>
                @endif
            </div>
        </div>
    </div>


    <div class="col-md-6 col-sm-6 mb-4">
        <div class="card shadow-sm widget-bg">
            <div class="card-body text-center">
                <h5 class="card-title">Rentals Overdue</h5>
                <p class="card-text display-4">{{ $data['itemMetrics']['overdue_items_count'] }}</p>
                @if (count($data['itemMetrics']['rentals_overdue']) == 0)
                <p>No rental items found.</p>
                @else
                <ul class="list-group">
                    @foreach ($data['itemMetrics']['rentals_overdue'] as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center shadow-sm mb-3">
                        <div class="ms-2 text-left">
                            <div class="item-type-book">{{ $item['book']['name'] }}</div>
                            <p class="mb-1"><strong>Item Owner:</strong> {{ $item['book']['user']['name'] }}</p>
                            <p class="mb-1"><strong>Reserved By:</strong> {{ $item['reserver']['name'] }}</p>
                        </div>
                        <div>
                            @if($item['is_due'])
                            <span class="badge bg-danger text-white">Overdue</span>
                            @endif
                            <p class="mb-1"><strong>Due Date:</strong> {{ $item['due_date'] }}</p>
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
    @foreach ($data['itemMetrics']['total_items_by_category'] as $category)
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="card shadow-sm widget-bg">
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
    @foreach ($data['groupMetrics'] as $group)
    <div class="col-md-4 col-sm-6 mb-4">
        <div class="card shadow-sm widget-bg">
            <div class="card-body text-center">
                <p class="card-text"><span class="item-type-book">{{ $group['group_title'] }}</span></p>
                <p class="card-text"><strong>Group Total Items:</strong> {{ $group['total_items'] }}</p>
                <p class="card-text"><strong>Total Savings:</strong> {{ number_format($group['savings'], 2) }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>


<h2>My Items</h2>
@if (count($data["books"]) == 0)
<p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: red;">Item not found in library</p>
@else
<div class="MultiCarousel">
    <div class="MultiCarousel-inner">
        @foreach ($data["books"] as $ky => $book)
        <div class="item">
            <div class="pad15">
                <div class="innerheader">
                    <a href="{{ route("show-item", ["id" => $book["id"]]) }}">
                        <h3 class="lead main-heading" style="cursor:pointer;" title="{{ $book["name"] }}">{{ strlen($book["name"]) > 15 ? substr($book["name"], 0, 15) . "..." : $book["name"] }}</h3>
                    </a>
                    <div class="post_image">
                        @if ($book["created_by"] == Auth::user()->id)
                        <a href="{{ route('edit-book', [$book["id"]]) }}">
                            <img src="{{ url('assets/media/edit-orange.png') }}">
                        </a>
                        <a href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to delete item?','question',function(){deleteItem('{{ $book['id'] }}','{{ route('delete-item') }}')})">
                            <img src="{{ url('assets/media/delete.png') }}">
                        </a>
                        @endif
                    </div>
                </div>
                <span class="item-type-book">{{ $book["category"]["name"] }}</span>
                <hr style="margin:0px -2px;">
                <div class="row">
                    <div class="col">
                        <div class="imagesection">
                            @if ($book["cover_page"] != "")
                            <img class="im-wd" src="{{ $book["cover_page"] }}">
                            @else
                            <img class="im-wd" src="">
                            @endif
                        </div>
                    </div>
                    <div class="col">
                        <b>Added:</b><br> {{ $book["added_date"] }} <br />
                        <b>Copies:</b><br> {{ count($book["availableentries"]) }} <br />
                        <b>Price:</b><br> ${{ $book['price'] }} <br />
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
<h2>My Groups</h2>
@if(count($data["groups"]) == 0)
<p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: red;">Group not found</p>
@else
<div class="MultiCarousel">
    <div class="MultiCarousel-inner">
        @foreach ($data['groups'] as $group)
        <div class="item">
            <div class="pad15">
                <div class="innerheader">
                    <h3 class="lead main-heading"><?= $group['title'] ?></h3>
                    <div class="post_image">
                        <a href="<?= route('show-group', ["id" => $group->id]) ?>"><img src="assets/media/eye-outline.png" alt="View Group"></a>
                        <?php if (Auth::user()->hasRole("admin") || $group["created_by"] == Auth::user()->id) { ?>
                        <a href="<?= route('edit-group', ["id" => $group["id"]]) ?>" title="Edit Group"><img src="assets/media/edit-orange.png" alt="Edit Group"></a>
                        <?php } ?>
                    </div>
                </div>

                <div class="divider">
                    <hr>
                </div>
                <div class="carousel-date">Created: <?= date('M/d/Y', strtotime($group['created_at'])) ?></div>
                <div class="imagesection">
                    <?php if ($group["group_picture"] != "") { ?>
                    <img class="im-wd" src="<?= asset('storage/'.$group["group_picture"]) ?>" alt="Group Image">
                    <?php } else { ?>
                    <img src="" alt="Group Image">
                    <?php } ?>
                    <?php if (!empty($group["to_be_deleted_at"])) { ?>
                    <span class="item-type-book">Marked Deleted</span>
                    <?php } else { ?>
                    <?php if ($group["created_by"] == Auth::user()->id) { ?>
                    <a href="javascript:void(0)" data-group_id="{{ $group['id'] }}" data-group_type_id="{{ $group['group_type_id'] }}" id="getMembersToAdd" onclick="getMembersToAdd('<?= route('get-members-to-add', ['group_id' => $group['id'], 'group_type_id' => $group['group_type_id']]) ?>','<?= $group['group_type_id'] ?>')" class="dark-btn btn link_with_img">
                        <img src="assets/media/add-circle-outline.png"> Invite
                    </a>
                    <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
<script>
    $(document).on('click', '#getMembersToAdd', function() {
        let group_id = $(this).data('group_id');
        let group_type_id = $(this).data('group_type_id');

        $('#group_id_modal').val(group_id);
        $('#group_type_id_modal').val(group_type_id);

    });

</script>

<style>
    .widget-bg {
        background-color: #F0F0F0 !important;
    }

</style>
