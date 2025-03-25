@php if (isset($retdata)) {
extract($retdata);
}
$conditionsMet = array_map(function ($members) {
return ($members['member_id'] === Auth::user()->id && $members['status'] === 'requested');
}, $members);
@endphp

<div class="container p-0">
    @if (in_array(true, $conditionsMet, true))
    <div class="tab-container">
        <div class="item search-result">
            <div class="text404">
                <img src="{{url('assets/media/error 1.png') }}" alt="You are not a member of this group">
                <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">You are not a member of this group</p>
            </div>
        </div>
    </div>
    @elseif($user_status && !$user_status->activated)
    <div class="tab-container">
        <div class="item search-result">
            <div class="text404">
                <img src="{{url('assets/media/deactivates-user.png') }}" alt="You are deactivated from viewing this group">
                <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">You do not have access to view this group at the moment.</p>
            </div>
        </div>
    </div>
    @else
    <div class="tab-container">
        <div class="tab-buttons tab d-flex flex-column flex-md-row">
            <button class="tab-button tablinks  {{ $active_tab == 'details' ?  'active' : ''}}" data-tab="tab1">Group Details</button>
            <button class="tab-button tablinks  {{ $active_tab == 'requests' ?  'active' : ''}}" data-tab=" tab2">Participants</button>
            <button style="width:23%" class="tab-button tablinks {{ $active_tab == 'return-requests' ?  'active' : ''}}" data-tab="tab4">Rental & Returns</button>
            <button style="width:23%" class="tab-button tablinks {{ $active_tab == 'community' ? 'active' : '' }}" data-tab="tab5">Community</button>
        </div>
        <div class="tab-content {{ $active_tab == 'details' ?  'active' : ''}}" id="tab1">
            <div class="container p-0">
                <div class="heading">
                    <h2>Group details</h2>
                </div>
                <div class="item search-result d-flex flex-column flex-lg-row">
                    <div class="search-result-image">
                        @if ($group["group_picture"] != "")
                        <img class="cover_img" src="{{ asset('storage/'.$group["group_picture"]) }}" alt="group Image">
                        @else
                        <img class="cover_img" src="" alt="group Image">
                        @endif
                    </div>
                    <div class="container">
                        <div class="row group-desc-row">
                            <div class="col-md-8">
                                <div class="innerheader">
                                    <h3 class="lead main-heading">{{ $group["title"] }}</h3>
                                </div>
                                @php
                                $locations = isset($group["locations"]) ? json_decode($group["locations"], true) : [];
                                @endphp
                                <div class="member">
                                    Group Location: {{ !empty($locations) ? implode(", ", $locations) : "" }}
                                </div>
                                <div class="member">Country : {{ $group["country"] }}</div>
                                <div class="member">State : {{ $group["state"] }}</div>
                                <div class="member">Zip code : {{ $group["zip_code"] }}</div>
                                <div class="carousel-date">Created:
                                    {{ date("Y-m-d", strtotime($group["created_at"])) }}
                                </div>
                                <div class="member">Members: {{ count($members) }}</div>
                            </div>
                            <div class="col-md-4 group-show-qr-code-section p-0 {{ !empty($group['to_be_deleted_at']) ? 'text-right' : 'd-flex' }} justify-content-end">
                                @if (!empty($group["to_be_deleted_at"]))
                                <span class="item-type-book">Marked Deleted</span>
                                @else
                                <ul class="list-group list-group-horizontal-md group-main-actions">
                                    <li class="list-group-item">
                                        <img class="qr-code" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(url('/show-group/' . $group['id'])) }}" alt="QR Code for Group">
                                    </li>
                                    @if (Auth::user()->id == $group['created_by'] || (!empty($user_status) && $user_status['member_role'] == 'admin' && $user_status['activated']))
                                    <li class="list-group-item">
                                        <a href="javascript:void(0)" onclick="getMembersToAddNew('{{ route('get-members-to-add', ['group_id' => $group['id'], 'group_type_id' => $group['group_type_id']]) }}','{{ $group['group_type_id'] }}')" class="dark-btn btn link_with_img invite-link" data-group_id="{{ $group['id'] }}" data-group_type_id="{{ $group['group_type_id'] }}" id="getMembersToAdd">
                                            <img src="{{ url('assets/media/add-circle-outline.png') }}"> Invite
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                                @endif
                            </div>
                            <div class="col-12 divider">
                                <hr>
                            </div>
                            <div class="col-12">
                                <div class="pargheading">Group Description</div>
                                <div class="parg">{{ $group["description"] }}</div>
                            </div>
                            <div class="col-12">
                                <div class="pargheading">Standard Loan Rule</div>
                                <div class="parg">{{ $standard_load_rule }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container pt-0">
                <div class="row">
                    <div class="col-12 position-relative members-list p-0">
                        <div class="table-group-details table-responsive group-books-list">
                            <table id="items_table_group" class="table table-striped table-rounded">
                                <thead>
                                    <tr>
                                        <th scope="col">Thumbnail</th>
                                        <th scope="col">Latest Image</th>
                                        <th scope="col">Item Name</th>
                                        <th scope="col">Rental Price</th>
                                        <th scope="col">Item Category</th>
                                        <th scope="col">Owner Name</th>
                                        <th scope="col">Item Location</th>
                                        <th scope="col">No. of Copies</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">
                                            @if(collect($group['books'])->contains(fn($book) => $book['created_by'] !== auth()->user()->id))
                                            Due Date
                                            @endif
                                        </th>
                                        <th scope="col">Requests</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($group['books'] as $book)
                                    @php
                                    $status = "";
                                    $color_class = "";
                                    if ($book['is_reserved']) {
                                    $status = "Reserved";
                                    $color_class = "badge-secondary";
                                    } else if ($book['is_reserved_pending']) {
                                    $status = "Approval Pending";
                                    $color_class = "badge-warning";
                                    } else if ($book['status'] == 3) {
                                    $status = "Sold";
                                    $color_class = "badge-danger";
                                    } else if ($book['copies'] > 0) {
                                    $status = "Available";
                                    $color_class = "badge-primary";
                                    }
                                    @endphp
                                    <tr>
                                        <td>
                                            <a href="{{ $book['cover_page']}}" target="_blank">
                                                <img style="width: 100px;height: 100px;object-fit: cover;" src="{{ $book["cover_page"] }}" alt="Cover Page">
                                            </a>
                                        </td>
                                        <td>
                                            @if($book["latest_image"])
                                            <a href="{{ $book['cover_page']}}" target="_blank">
                                                <img style="width: 100px;height: 100px;object-fit: cover;" src="{{ $book["latest_image"] }}" alt="Latest Image">
                                            </a>
                                            @endif
                                        </td>
                                        <td>{{ $book['name'] }}</td>
                                        <td>{{ $book['rent_price'] }}</td>
                                        <td>{{ $book['category']['name'] }}</td>
                                        <td style="text-align: center">
                                            {{ isset($book['user']['name']) ? $book['user']['name'] : ""   }}
                                        </td>
                                        <td style="text-align: center">
                                            {{ isValidJson($book['locations']) ? implode(", ", json_decode($book['locations'])) : $book['locations']}}
                                        </td>
                                        <td style="text-align: center">
                                            <span class="badge badge-primary py-2 px-4 show-copies">{{ $book['copies'] }}</span>
                                        </td>

                                        <td style="text-align: center">
                                            @if($book['created_by'] !== auth()->user()->id)
                                            <span class="badge {{$color_class}} px-4 py-2">
                                                {{ ($book['copies'] == 0) ? ($book['reservation_deleted'] ? 'Requests cancelled all booked' : "All booked") : $status }}
                                            </span>
                                            @elseif($book['created_by'] == auth()->user()->id)
                                            @php
                                            $pending_count=0;
                                            $is_reserved = false;
                                            if($book['entries']):
                                            @endphp
                                            @foreach ($book['entries'] as $entry)
                                            @php
                                            if($entry['is_reserved'] == 2)
                                            {
                                            $pending_count++;
                                            $is_reserved = true;
                                            }
                                            @endphp
                                            @endforeach
                                            @if($is_reserved)
                                            <span class="badge badge-warning px-4 py-2"><span class="badge badge-success px-2 py-1">{{ $pending_count }} </span> Approval pending</span>
                                            @elseif($book['copies'] == 0)
                                            <span class="badge badge-success px-4 py-2">Booked</span>
                                            @else
                                            <span class="badge badge-primary px-4 py-2">Available</span>
                                            @endif
                                            @endif
                                            @endif
                                        </td>

                                        <td style="text-align: center">
                                            @if ($book['created_by'] !== auth()->user()->id)
                                            {{$book['due_date']}}
                                            @endif
                                        </td>

                                        <td style="text-align: center">
                                            @if ($book['created_by'] !== auth()->id())
                                            @if (!$book['is_reserved'] && $book['copies'] !== 0 && !$book['is_reserved_pending'])
                                            @if($enabled_member)
                                            <a href="javascript:void(0)" onclick="setBookStatus({{ $book['id'] }}, {{ $group['id'] }},'reserved')" id="set-status" class="btn btn-green"> Add Reserve</a>
                                            @else
                                            <span class="badge badge-warning px-4 py-2">You are disabled</span>
                                            @endif
                                            @endif
                                            @if($book['is_reserved'])
                                            @if($book['state'] == 'return-request')
                                            <span class="badge badge-warning px-4 py-2"> Return pending </span>
                                            @else
                                            <a href="javascript:void(0)" id="return-book-request-btn" onclick="returnBook({{ $book['entry_id'] }}, {{ $book['id'] }})" class="btn btn-red"> Return</a>
                                            @endif
                                            @endif
                                            @endif
                                            @if($enabled_member || $group['created_by'] == auth()->id() || auth()->user()->hasRole('admin'))
                                            {{-- <a class="btn btn-info btn-sm mt-2" href="{{ route('comments', $book['id']) }}">Comments</a> --}}
                                            <a class="btn btn-warning  btn-sm mt-2" href="{{ route('reviews', $book['id']) }}">Reviews</a>
                                            @endif
                                            @if ($book['created_by'] == auth()->id() || $group['created_by'] == auth()->id() || auth()->user()->hasRole('admin') || (!empty($user_status) && $user_status['member_role'] =='admin' && $user_status['activated']))
                                            <a href="javascript:void(0)" class="badge badge-secondary px-4 mt-2 py-2 show-copies" onclick="showCopies({{ $book['id'] }})">Action</a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content {{ $active_tab == 'requests' ?  'active' : ''}}" id=" tab2">
            <div class="container p-0">
                <div class="row pt-4">
                    <div class="col-5 members-list">
                        <div class="heading mt-0">
                            <h2>Requests</h2>
                        </div>
                    </div>
                    <div class="col-2 members-list">
                        @if (isset($requested_members))
                        <span class="requests-count text-nowrap btn btn-success">Total Request
                            <strong>{{ count($requested_members) }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        @include('group.requested')
                    </div>
                </div>
            </div>
            <hr />
            <div class="container p-0">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="heading mt-0">
                        <h2>Participants</h2>
                    </div>
                    @if(auth()->user()->hasRole('admin') || auth()->id() == $group['created_by'] || (!empty($user_status) && $user_status['member_role'] == 'admin'))
                    <a href="javascript:void(0)" onclick="getMembersToAddNew('{{ route('get-members-to-add', ['group_id' => $group['id'], 'group_type_id' => $group['group_type_id']]) }}','{{ $group['group_type_id'] }}')" class="dark-btn btn mb-3 link_with_img invite-link" data-group_id="{{ $group['id'] }}" data-group_type_id="{{ $group['group_type_id'] }}" id="getMembersToAdd">
                        <img src="{{ url('assets/media/add-circle-outline.png') }}"> Invite
                    </a>
                    @endif
                </div>
                <div class="item search-result">
                    <div class="container">
                        <div class="row group-desc-row">
                            <div class="col-12 members-section">
                                <div class="parg">
                                    @include('group.members')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content {{ $active_tab == 'return-requests' ?  'active' : ''}}" id="tab4">
            <div class="container p-0">
                <div class="heading mt-4">
                    <h2>Return Requests</h2>
                </div>
                <div class="item search-result">
                    <div class="container">
                        <div class="row group-desc-row">
                            <div class="col-12 members-section">
                                <div class="parg">
                                    @include('group.return-request')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            {{-- <div class="container p-0">
                <div class="heading mt-0">
                    <h2>Reservations canceled</h2>
                </div>
                <div class="item search-result">
                    <div class="container">
                        <div class="row group-desc-row">
                            <div class="col-12 members-section">
                                <div class="parg">
                                    @include('group.reservation-canceled')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
        <div class="tab-content {{ $active_tab == 'community' ? 'active' : '' }}" id="tab5">
            <div class="tab-buttons mt-3 tab d-flex flex-column flex-md-row">
                <button style="width:23%" class="tab-button-c tablinks active" data-tab="tab14">Tickets</button>
                <button style="width:23%" class="tab-button-c tablinks" data-tab="tab15">Posts</button>
            </div>
            <div class="tab-content-c mt-2 active" id="tab14">
                <div class="container p-0">
                    <div class="heading mt-0">
                        <h2>Tickets</h2>
                    </div>
                    <div class="item search-result">
                        <div class="container">
                            <form id="disputeForm">
                                @csrf
                                <input type="hidden" name="group_id" value="{{ $group['id'] }}">
                                <div class="form-group">
                                    <label>Add new ticket</label>
                                    <textarea name="description" id="disputeDescription" required class="form-control" placeholder="Raise a dispute"></textarea>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <hr />

                    <div id="ticketsList">
                        <h3>Existing Tickets</h3>
                        @forelse ($group['tickets'] as $ticket)
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title">Ticket #{{ $ticket['id'] }}</h5>
                                <p class="card-text">{!! $ticket['description'] !!}</p>
                                <p class="text-muted">
                                    Raised by: {{ $ticket['user']['name'] }} on {{ date('Y-m-d H:i:s', strtotime($ticket['created_at'])) }}
                                </p>
                                <p class="text-muted">Status: <strong>{{ ucfirst($ticket['status']) }}</strong></p>
                                @if(auth()->user()->hasRole('admin')
                                || auth()->id() == $group['created_by']
                                || auth()->id() == $ticket['user_id']
                                || auth()->id() == $ticket['admin_id'])
                                <div class="d-flex justify-content-between align-items-center">
                                    <button class="btn btn-danger btn-sm delete-ticket" data-id="{{ $ticket['id'] }}">Delete Ticket</button>
                                    <form method="POST" class="changeStatusForm">
                                        @csrf
                                        <div class="input-group">
                                            <input type="hidden" name="ticket_id" value="{{ $ticket['id'] }}">
                                            <select name="status" class="form-select form-control" required>
                                                <option value="open" {{ $ticket['status'] == 'open' ? 'selected' : '' }}>Open</option>
                                                <option value="resolved" {{ $ticket['status'] == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                                <option value="closed" {{ $ticket['status'] == 'closed' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary">Update Status</button>
                                        </div>
                                    </form>
                                </div>
                                @endif
                            </div>

                            <div class="card-footer">
                                <h6>Comments</h6>
                                <div id="commentsForTicket{{ $ticket['id'] }}">
                                    @if($ticket['status'] !== 'closed')
                                    @forelse ($ticket['comments'] as $comment)
                                    <div class="comment mb-2" id="postcomment{{ $comment['id'] }}">
                                        <strong>{{ $comment['user']['name'] }}:</strong> {{ $comment['comment'] }}
                                        <p class="text-muted small">
                                            Commented on {{ date('Y-m-d H:i:s', strtotime($comment['created_at'])) }}
                                        </p>
                                        @if(auth()->user()->hasRole('admin')
                                        || auth()->id() == $group['created_by']
                                        || auth()->id() == $comment['user_id']
                                        || auth()->id() == $ticket['user_id']
                                        || auth()->id() == $ticket['admin_id'])
                                        <button class="btn btn-danger btn-sm delete-post-comment" data-id="{{ $comment['id'] }}">Delete Comment</button>
                                        @endif
                                    </div>
                                    @empty
                                    <p>No comments yet.</p>
                                    @endforelse
                                    @else
                                    <p>Comments are disabled for closed tickets.</p>
                                    @endif
                                </div>

                                @if($ticket['status'] !== 'closed')
                                <form method="POST" id="ticket_comment" class="addCommentForm mt-3">
                                    @csrf
                                    <input type="hidden" name="ticket_id" value="{{ $ticket['id'] }}">
                                    <div class="form-group">
                                        <textarea name="comment" class="form-control" placeholder="Add a comment..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">Comment</button>
                                </form>
                                @endif
                            </div>
                        </div>
                        @empty
                        <p>No tickets available.</p>
                        @endforelse
                    </div>

                </div>
            </div>

            <div class="tab-content-c" id="tab15">
                <div class="container p-0">
                    <div class="heading mt-0">
                        <h2>Posts</h2>
                    </div>
                    <div class="item search-result">
                        <div class="container">
                            <form method="POST" id="community_post_form" class="mb-4">
                                @csrf
                                <input type="hidden" name="group_id" value="{{ $group['id'] }}">
                                <div class="form-group">
                                    <label>Add new post here</label>
                                    <textarea name="title" class="form-control mb-2" rows="3" placeholder="Write your note or wish list..." required></textarea>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="comments_enabled" id="comments_enabled" checked>
                                    <label class="form-check-label" for="comments_enabled">Allow responses on this post</label>
                                </div>
                                <button type="submit" class="btn btn-primary">Add Post</button>
                            </form>

                            @forelse($group['posts'] as $post)
                                <div class="post-card mb-4 p-3 border rounded">
                                    <p class="text-muted">
                                        Posted by: <strong>{{ $post['user']['name'] }}</strong> on {{ date('Y-m-d H:i:s', strtotime($post['created_at'])) }}
                                    </p>
                                    <p>{{ $post['title'] }}</p>

                                    @if(auth()->user()->hasRole('admin') || auth()->id() == $group['created_by'] || auth()->id() == $post['user_id'])
                                        <button class="btn btn-danger btn-sm delete-post" data-id="{{ $post['id'] }}">Delete Post</button>
                                    @endif

                                    @if($post['comments_enabled'])
                                        <div class="comments-section mt-3">
                                            <h6>Responses:</h6>
                                            <div id="commentsForPost{{ $post['id'] }}">
                                                @forelse($post['comments'] as $comment)
                                                    <div class="comment mb-2" id="comment_{{ $comment['id'] }}">
                                                        <strong>{{ $comment['user']['name'] }}:</strong> {{ $comment['comment'] }}
                                                        <p class="text-muted small">Commented on {{ date('Y-m-d H:i:s', strtotime($comment['created_at'])) }}</p>
                                                        @if(auth()->user()->hasRole('admin')
                                                        || auth()->id() == $group['created_by']
                                                        || auth()->id() == $comment['user_id']
                                                        || auth()->id() == $post['user_id'])
                                                        <button class="btn btn-danger btn-sm delete-comment" data-id="{{ $comment['id'] }}">Delete Comment</button>
                                                        @endif
                                                    </div>
                                                @empty
                                                    <p class="text-muted">No responses yet.</p>
                                                @endforelse
                                            </div>

                                            <form method="POST" id="post_comment_form_{{ $post['id'] }}" class="addCommentForm mt-3">
                                                @csrf
                                                <input type="hidden" name="post_id" value="{{ $post['id'] }}">
                                                <div class="form-group">
                                                    <textarea name="content" class="form-control" placeholder="Write a response..." required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary btn-sm">Respond</button>
                                            </form>
                                        </div>
                                    @else
                                        <p class="text-muted">Responses are disabled for this post.</p>
                                    @endif
                                </div>
                            @empty
                                <p>No posts available. Be the first to create one!</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="modal" id="dixwix_book_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body py-4" id="modal_body" style="overflow-y: scroll; max-height: 450px;">
                    <form id="book-status-form" enctype="multipart/form-data">
                        @csrf
                        <div class="col">
                            <label for="book_duration">Select Duration</label>
                            <select class="form-control" name="duration" id="book_duration">
                                @foreach ($group['loanRules'] as $rule)
                                <option value="{{ $rule['id'] }}">{{ $rule['title'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col mt-4">
                            <button class="btn btn-secondary" id="reserve-book-btn">Reserve</button>
                            <button type="button" id="close-modal-reserve-modal" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="dixwix_book_copies_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-body" id="modal_body" style="overflow-y: scroll; max-height: 450px;">
                    <button type="button" class="close book-modal" id="close-modal-rental-modal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <div class="table-group-details">
                        <table id="items_table_group" class="table">
                            <thead>
                                <tr>
                                    <th scope="col">Copy#</th>
                                    <th scope="col">Copy Name</th>
                                    <th scope="col">Reserved Status</th>
                                    <th scope="col">Reserved By</th>
                                    <th scope="col">Member Trust Score</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="modal_table_body_copies"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal" id="dixwix_modal_invite" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <input type="hidden" id="group_id_modal" name="group_id">
                <input type="hidden" id="group_type_id_modal" name="group_type_id">
                <div class="modal-body" id="modal_body_invite" style="overflow-y: scroll; max-height: 450px;"></div>

                <div class="container mt-5">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="email" id="email_to_invite" class="form-control" placeholder="Enter Email ID to invite" />
                            <div class="input-group-append">
                                <button class="btn btn-primary" id="invite_button" onclick="invite_by_email(1,2)">Invite</button>
                            </div>
                        </div>
                    </div>
                    <div id="loading" class="mt-2 text-center" style="display: none;">Inviting...</div>
                    <p id="response_message" class="text-center text-danger small mt-2"></p>
                </div>
                <div id="user_list_container" style="padding:20px">

                </div>
                <button id="close-modal-invite" class="btn btn-danger">Close</button>
            </div>
        </div>
    </div>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


    @include('group.scripts')
    <script>
        $('#close-modal-invite').click(function() {
            $('#dixwix_modal_invite').modal('hide');
        })
        $('#close-modal-reserve-modal').click(function() {
            $('#dixwix_book_modal').modal('hide');
        })
        $('#close-modal-rental-modal').click(function() {
            $('#dixwix_book_copies_modal').modal('hide');
        })
        $(document).ready(function() {

            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', () => {

                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    button.classList.add('active');
                    const tabId = button.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });

            const tabButtons_c = document.querySelectorAll('.tab-button-c');
            const tabContents_c = document.querySelectorAll('.tab-content-c');

            tabButtons_c.forEach(button_c => {
                button_c.addEventListener('click', () => {

                    tabButtons_c.forEach(btn_c => btn_c.classList.remove('active'));
                    tabContents_c.forEach(content_c => content_c.classList.remove('active'));

                    button_c.classList.add('active');
                    const tabId_c = button_c.getAttribute('data-tab');
                    document.getElementById(tabId_c).classList.add('active');
                });
            });

            new DataTable('#items_table_group', {});
            new DataTable('#items_table_requests', {});
            new DataTable('#items_table_requests_cancelled', {});

            $('#disputeForm').on('submit', function(e) {
                e.preventDefault();

                const description = $('#disputeDescription').val();

                if (!description) {
                    Swal.fire({
                        title: 'Warning!'
                        , text: 'Please enter a description.'
                        , icon: 'warning'
                        , confirmButtonText: 'OK'
                    , });
                    return;
                }

                const submitButton = $('#submitDispute');
                submitButton.prop('disabled', true).text('Submitting...');

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('ticket.store') }}'
                    , type: 'POST'
                    , data: formData
                    , processData: false
                    , contentType: false
                    , dataType: 'json'
                    , success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!'
                                , text: response.message
                                , icon: 'success'
                                , showConfirmButton: true
                            , }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!'
                                , text: response.message
                                , icon: 'error'
                            , });
                        }
                    }
                    , error: function(xhr) {
                        Swal.fire({
                            title: 'Error!'
                            , text: 'An error occurred while submitting the dispute. Please try again later.'
                            , icon: 'error'
                        , });
                    }
                    , complete: function() {
                        submitButton.prop('disabled', false).text('Submit');
                    }
                , });
            });

            $('#ticket_comment').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = new FormData(this);
                const submitButton = form.find('button[type="submit"]');
                const commentTextarea = form.find('textarea[name="comment"]');
                const commentsContainer = form.closest('.card-footer').find(`#commentsForTicket${form.find('input[name="ticket_id"]').val()}`);

                if (!commentTextarea.val().trim()) {
                    Swal.fire({
                        title: 'Warning!'
                        , text: 'Please enter a comment.'
                        , icon: 'warning'
                        , confirmButtonText: 'OK'
                    , });
                    return;
                }

                submitButton.prop('disabled', true).text('Submitting...');

                $.ajax({
                    url: '{{ route('ticket.comment.store') }}'
                    , type: 'POST'
                    , data: formData
                    , processData: false
                    , contentType: false
                    , dataType: 'json'
                    , success: function(response) {
                        if (response.success) {
                            commentsContainer.append(`
                                <div class="comment mb-2">
                                    <strong>${response.user.name}:</strong> ${response.comment.comment}
                                    <p class="text-muted small">Commented just now</p>
                                </div>
                            `);
                            commentTextarea.val('');

                            Swal.fire({
                                title: 'Success!'
                                , text: response.message
                                , icon: 'success'
                            , });
                        } else {
                            Swal.fire({
                                title: 'Error!'
                                , text: response.message
                                , icon: 'error'
                            , });
                        }
                    }
                    , error: function(xhr) {
                        Swal.fire({
                            title: 'Error!'
                            , text: 'An error occurred while submitting the comment. Please try again later.'
                            , icon: 'error'
                        , });
                    }
                    , complete: function() {
                        submitButton.prop('disabled', false).text('Comment');
                    }
                , });
            });

            $(document).on('click', '.delete-ticket', function() {
                const ticketId = $(this).data('id');
                const ticketCard = $(this).closest('.card');

                Swal.fire({
                    title: 'Are you sure?'
                    , text: 'This will delete the ticket and all associated comments.'
                    , icon: 'warning'
                    , showCancelButton: true
                    , confirmButtonText: 'Yes, delete it!'
                , }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/ticket/${ticketId}`
                            , type: 'DELETE'
                            , data: {
                                _token: '{{ csrf_token() }}'
                            , }
                            , success: function(response) {
                                if (response.success) {
                                    ticketCard.remove();
                                    Swal.fire('Deleted!', 'The ticket has been deleted.', 'success');
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            }
                            , error: function() {
                                Swal.fire('Error!', 'Failed to delete the ticket. Please try again.', 'error');
                            }
                        , });
                    }
                });
            });

            $(document).on('click', '.delete-comment', function() {
                const commentId = $(this).data('id');
                const commentDiv = $(`#comment${commentId}`);

                Swal.fire({
                    title: 'Are you sure?'
                    , text: 'This will delete the comment.'
                    , icon: 'warning'
                    , showCancelButton: true
                    , confirmButtonText: 'Yes, delete it!'
                , }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/ticket/comments/${commentId}`
                            , type: 'DELETE'
                            , data: {
                                _token: '{{ csrf_token() }}'
                            , }
                            , success: function(response) {
                                if (response.success) {
                                    commentDiv.remove();
                                    Swal.fire('Deleted!', 'The comment has been deleted.', 'success');
                                } else {
                                    Swal.fire('Error!', response.message, 'error');
                                }
                            }
                            , error: function() {
                                Swal.fire('Error!', 'Failed to delete the comment. Please try again.', 'error');
                            }
                        , });
                    }
                });
            });


            $(document).on('submit', '.changeStatusForm', function (e) {
                e.preventDefault();

                const form = $(this);
                const ticketId = form.find('input[name="ticket_id"]').val();
                const status = form.find('select[name="status"]').val();

                $.ajax({
                    url: `/ticket/${ticketId}/status`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status,
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success').then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', 'Failed to update ticket status. Please try again.', 'error');
                    },
                });
            });

            $('#community_post_form').on('submit', function (e) {
                e.preventDefault();

                const title = $('textarea[name="title"]').val();

                if (!title.trim()) {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Please write something for your post.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                    });
                    return;
                }

                const submitButton = $(this).find('button[type="submit"]');
                submitButton.prop('disabled', true).text('Submitting...');

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('post.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Your post has been added successfully.',
                                icon: 'success',
                                showConfirmButton: true,
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'An error occurred while adding your post.',
                                icon: 'error',
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while submitting the post. Please try again later.',
                            icon: 'error',
                        });
                    },
                    complete: function () {
                        submitButton.prop('disabled', false).text('Add Post');
                    },
                });
            });

            $('.delete-post').on('click', function () {
                const postId = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/post/${postId}`,
                            type: 'DELETE',
                            success: function () {
                                Swal.fire('Deleted!', 'Post has been deleted.', 'success').then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function () {
                                Swal.fire('Error!', 'Failed to delete post.', 'error');
                            },
                        });
                    }
                });
            });

            $('.addCommentForm').on('submit', function (e) {
                e.preventDefault();

                const form = $(this);
                const formData = new FormData(this);
                const submitButton = form.find('button[type="submit"]');
                const commentTextarea = form.find('textarea[name="content"]');
                const postId = form.find('input[name="post_id"]').val();
                const commentsContainer = form.closest('.comments-section').find(`#commentsForPost${postId}`);

                if (!commentTextarea.val().trim()) {
                    Swal.fire({
                        title: 'Warning!',
                        text: 'Please enter a response.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                    });
                    return;
                }

                submitButton.prop('disabled', true).text('Submitting...');

                $.ajax({
                    url: '{{ route("post.comment.store") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            commentsContainer.append(`
                                <div class="comment mb-2">
                                    <strong>${response.user.name}:</strong> ${response.comment.comment}
                                    <p class="text-muted small">Commented just now</p>
                                </div>
                            `);
                            commentTextarea.val('');

                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while submitting the response. Please try again later.',
                            icon: 'error',
                        });
                    },
                    complete: function () {
                        submitButton.prop('disabled', false).text('Respond');
                    },
                });
            });

            $(document).on('click', '.delete-post-comment', function () {
                const button = $(this);
                const commentId = button.data('id')
                const commentElement = $(`#postcomment${commentId}`);

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/post/comments/${commentId}`,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            },
                            success: function (response) {
                                if (response.success) {
                                    commentElement.remove();
                                    Swal.fire('Deleted!', 'Comment has been deleted.', 'success');
                                } else {
                                    Swal.fire('Error!', response.message || 'Failed to delete comment.', 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error!', 'An error occurred while deleting the comment.', 'error');
                            },
                        });
                    }
                });
            });



        });

        $(document).on('click', '#getMembersToAdd', function() {
            let group_id = $(this).data('group_id');
            let group_type_id = $(this).data('group_type_id');

            $('#group_id_modal').val(group_id);
            $('#group_type_id_modal').val(group_type_id);

        });

        function searchUsers() {
            let searchQuery = document.getElementById('search_user').value;
            let group_id = $('#group_id_modal').val();
            let group_type_id = $('#group_type_id_modal').val();

            $.ajax({
                url: "{{ url('search-users') }}"
                , method: 'GET'
                , data: {
                    search_user: searchQuery
                    , group_type_id: group_type_id
                    , group_id: group_id
                }
                , success: function(result) {
                    let dataJson = JSON.parse(result);

                    if (dataJson.success == true) {
                        jQuery("#user_list_container").html(dataJson.data);
                        jQuery("#modal_title").text("Add Members");
                    }
                }
                , error: function() {
                    console.error("An error occurred while fetching users.");
                }
            });
        }

        function updateGroupStatus(groupId) {
            var checkbox = document.getElementById('group_' + groupId);
            var statusText = document.getElementById('status-text-' + groupId);

            if (checkbox.checked) {
                statusText.textContent = 'Disable';
            } else {
                statusText.textContent = 'Enable';
            }
        }

    </script>

    <style>
        #dixwix_book_modal #modal_body {
            overflow: hidden !important;
        }

        #dixwix_book_modal .close.book-modal {
            position: absolute;
            top: 0;
            right: 0;
        }

        .show-copies {
            cursor: pointer;
        }

        .show-copies {
            cursor: pointer;
        }


        .tab-container {
            padding: 30px 0px;
            display: flex;
            flex-direction: column;
        }

        .tab-button {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            margin-right: 5px;
            border-bottom: none;
        }

        .tab-button.active {
            background-color: #ffffff;
            border-bottom: 1px solid #ffffff;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }




        .tab-button-c {
            padding: 10px 20px;
            cursor: pointer;
            background-color: #f1f1f1;
            border: 1px solid #ccc;
            margin-right: 5px;
            border-bottom: none;
        }

        .tab-button-c.active {
            background-color: #ffffff;
            border-bottom: 1px solid #ffffff;
        }

        .tab-content-c {
            display: none;
        }

        .tab-content-c.active {
            display: block;
        }

    </style>
