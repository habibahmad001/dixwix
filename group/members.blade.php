@foreach ($members as $group_member)
@php
$group_type_id = $group_member['group']['group_type_id'];
$checked = $group_member['member_role'] === "admin" ? "checked" : "";
$activated = $group_member['activated'] === 1 ? "checked" : "";
@endphp

@if($group_member["member"])
@if($group_member["status"] === "added" || $group_member["status"] === "requested" || $group_member["member_id"] === auth()->user()->id)
<div class="messagesectionmain">
    <div class="mainsection align-items-center">
        <div class="image-section">
            @if($group_member["member"]["profile_pic"] != "")
            <img class="requested-memeber-img" style="width: 56px;" src="{{ asset('storage/'.$group_member['member']['profile_pic']) }}" alt="Profile Picture">
            @else
            <img style="width: 56px;" src="{{ url('assets/media/userimg.png') }}" alt="Profile Picture">
            @endif
        </div>
        <div class="text-section member-info">
            <h3>{{ $group_member["member"]["name"] }}
                {{ $group_member['member_role'] == 'admin' ? '(Admin)' : '' }}
                @if($group_member['member']['id'] != $group['created_by'])
                {{ $group_member['activated'] ? '' :  '(Deactivated)' }}
                @endif
            </h3>
        </div>
    </div>
    <div class="msgsection">
        <ul class="list-group list-group-horizontal-md group-main-actions show-group-members-buttons">
            @if($group['created_by'] != $group_member['member_id'])
            <li class="list-group-item text-center d-flex align-items-center">
                <span class="badge {{ $group_member['status'] === 'added' ? 'badge-success' : 'badge-secondary' }} py-3 px-4">
                    {{ ucfirst($group_member['status']) }}
                </span>
            </li>
            @endif
            <li class="list-group-item d-flex flex-row text-center align-items-center">
                @if(
                (Auth::user()->id == $group['created_by'] && Auth::user()->id != $group_member['member_id']) ||
                (Auth::user()->hasRole('admin') && $group['created_by'] != $group_member['member_id']) ||
                ($group['created_by'] != $group_member['member_id'] && auth()->id() != $group_member['member_id'] && $group_member['member_role'] != 'admin' && !empty($user_status) && $user_status['member_role'] == 'admin' && $user_status['activated'])
                )
                <a href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation',
       'Are you sure you want to remove this person from the group?',
       'question',
       function() { deleteUserFromGroup('{{ $group_member['member_id'] }}', '{{ $group_member['group_id'] }}', '{{ $group_type_id }}'); })">
                    <img src="{{ url('assets/media/delete1.png') }}" style="width: 30px">
                </a>
                @endif

                @if(
                (
                (Auth::id() == $group['created_by'] || Auth::user()->hasRole('admin')) &&
                $group['created_by'] != $group_member['member_id']
                ) ||
                (
                !empty($user_status) &&
                isset($user_status['member_role'], $user_status['activated']) &&
                $user_status['member_role'] == 'admin' &&
                $user_status['activated'] &&
                Auth::id() != $group_member['member_id']
                )
                )
                @if($group['created_by'] != $group_member['member_id'] && auth()->id() != $group_member['member_id'])
                <div class="d-flex flex-column justify-content-center">
                    <small>Admin</small>
                    <label title="Toggle to make or remove participant as admin" class="switch">
                        <input id="member_{{ $group_member['member_id'] }}" onchange="updateMember({{ $group_member['member_id'] }}, {{ $group_member['group_id'] }}, {{ $group_type_id }})" type="checkbox" {{ $checked }}>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="d-flex flex-column">
                    <small>{{ $activated ? 'Deactivate' : 'Activate' }}</small>
                    <label title="Toggle to activate or deactivate participant" class="switch">
                        <input id="member_status_{{ $group_member['member_id'] }}" onchange="updateMemberStatus({{ $group_member['member_id'] }}, {{ $group_member['group_id'] }}, {{ $group_type_id }})" type="checkbox" {{ $activated }}>
                        <span class="slider"></span>
                    </label>
                </div>
                @endif
                @endif
                @if($group['created_by'] == $group_member['member_id'])
                <span class="btn btn-dark rounded">Owner</span>
                @endif
            </li>
        </ul>
    </div>
</div>
@endif
@endif
@endforeach
