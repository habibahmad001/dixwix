@if(!empty($requested_members))
@foreach ($requested_members as $request_member)
@if($request_member["member"])
@if((($request_member["status"] == "added") || $request_member["status"] === "requested") || ($request_member["member_id"] == auth()->user()->id) || auth()->user()->hasRole('admin'))
<div class="messagesectionmain align-items-center">
    <div class="mainsection align-items-center">
        <div class="image-section">
            @if (!empty($request_member["member"]["profile_pic"]))
            <img style="width: 100px;height:100px" src="{{ asset('storage/'.$request_member["member"]["profile_pic"]) }}" alt="Profile Picture">
            @else
            <img style="width: 56px;" src="{{ url('assets/media/userimg.png') }}" alt="Profile Picture">
            @endif
        </div>
        <div class="text-section member-info">
            <h3>{{ $request_member["member"]["name"] }}</h3>
        </div>
    </div>
    <div class="msgsection">
        <ul class="list-group list-group-horizontal-md group-main-actions">
            <li class="list-group-item text-center d-flex align-items-center">
                @if (in_array($request_member["status"], ['requested', 'invited']) && Auth::user()->hasRole('admin'))
                <span class="btn accept-request-btn btn-primary btn-sm text-center mr-2" onclick="acceptJoiningRequest({{ $request_member['id'] }}, {{ $request_member['member_id'] }}, {{ $request_member['group_id'] }}, true)">Accept</span>
                <span class="btn decline-request-btn btn-danger text-center btn-sm" onclick="acceptJoiningRequest({{ $request_member['id'] }}, {{ $request_member['member_id'] }}, {{ $request_member['group_id'] }}, false)">Decline</span>
                @elseif ($request_member["status"] == 'requested' && !empty($user_status) && $user_status['member_role'] == 'admin' && $user_status['activated'])
                <span class="btn accept-request-btn btn-primary btn-sm text-center mr-2" onclick="acceptJoiningRequest({{ $request_member['id'] }}, {{ $request_member['member_id'] }}, {{ $request_member['group_id'] }}, true)">Accept</span>
                <span class="btn decline-request-btn btn-danger text-center btn-sm" onclick="acceptJoiningRequest({{ $request_member['id'] }}, {{ $request_member['member_id'] }}, {{ $request_member['group_id'] }}, false)">Decline</span>
                @elseif ($request_member["status"] === "requested" && Auth::user()->id == $group["created_by"])
                <span class="btn accept-request-btn btn-primary btn-sm text-center mr-2" onclick="acceptJoiningRequest({{ $request_member['id'] }}, {{ $request_member['member_id'] }}, {{ $request_member['group_id'] }}, true)">Accept</span>
                <span class="btn decline-request-btn btn-danger text-center btn-sm" onclick="acceptJoiningRequest({{ $request_member['id'] }}, {{ $request_member['member_id'] }}, {{ $request_member['group_id'] }}, false)">Decline</span>
                @elseif($request_member["member_id"] == auth()->user()->id && $request_member["status"] === "invited")
                <span id="accept-request-btn" class="btn accept-request-btn btn-primary mr-2 btn-sm" onclick="acceptInvitation({{ $request_member['group_id'] }}, {{ $request_member['member_id'] }},{{ $request_member['created_by'] }})">Accept</span>
                <span class="btn decline-request-btn btn-danger text-center btn-sm" onclick="acceptJoiningRequest({{ $request_member['id'] }}, {{ $request_member['member_id'] }}, {{ $request_member['group_id'] }}, false)">Decline</span>
                @else
                <span class="{{ $request_member['status'] === 'added' ? 'badge badge-success py-3 px-4' : 'badge badge-secondary py-3 px-4' }}">{{$request_member['status']}} </span>
                @endif
            </li>
        </ul>
    </div>
</div>
@endif
@endif
@endforeach
@endif
