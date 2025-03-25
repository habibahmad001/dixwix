<div class="container-wrapper col-md-11 mx-auto mt-5" style="display: flex; flex-direction: column; gap: 20px;">

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="mb-4 d-flex justify-content-between">
        <form action="{{ route('my-notifications') }}" method="GET" class="d-flex" style="gap: 10px; flex-grow: 1;">
            <div class="form-group m-0">
                <div class="input-group mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search notifications..." value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-success" type="submit">Search</button>
                    </div>
                </div>
            </div>
        </form>
        @if(!empty($data['notifications']) && count($data['notifications']) > 0)
        <div>
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-primary" style="background-color: #094042; color: white; padding: 13px 20px; border-radius: 5px;">
                    Mark All as Read
                </button>
            </form>
            <form action="{{ route('notifications.deleteAll') }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" style="padding: 10px 20px; border-radius: 5px;">
                    Delete All
                </button>
            </form>
        </div>
        @endif
    </div>

    @forelse($data['notifications'] as $notification)
    <div class="notification-item" style="display: flex; align-items: flex-start; gap: 15px;">
        <div class="notification-time" style="font-size: medium; color: #606060; min-width: 60px;">
            {{ \Carbon\Carbon::parse($notification->created_at)->format('h:i') ?? '00:00' }}
        </div>
        <div class="notification-icon" style="min-width: 20px;">
            <img src="assets/media/{{ $notification->type == 'add' ? 'add-circle.png' : 'chatbox-ellipses.png' }}" alt="action" style="width: 20px;">
        </div>
        <div class="notification-content" style="flex-grow: 1; background-color: {{ $notification->read_at ? '#E0E0E0' : '#D9FBE4' }}; border-radius: 10px; padding: 20px;">
            <h5 style="margin: 0 0 10px; font-family: Poppins; font-weight: 600; font-size: 16px; color: #094042;">
                {{ isset($notification->data['title']) ? $notification->data['title'] : 'N/A' }}
            </h5>
            <hr style="border: 1px solid white; margin-bottom: 10px;">
            @if(!empty($notification->user))
            <div class="user-info" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <img src="{{ !empty($notification->user->profile_pic) ? asset('storage/'.$notification->user->profile_pic) : asset('assets/media/userimg.png') }}" alt="User" style="width: 48px; border-radius: 50%;">
                <p style="margin: 0; font-size: 15px; font-weight: 600; color: #094042;">{{ $notification->user->name }}</p>
            </div>
            @else
            <div class="user-info" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <img src="{{ asset('assets/media/userimg.png') }}" alt="User" style="width: 48px; border-radius: 50%;">
                <p style="margin: 0; font-size: 15px; font-weight: 600; color: #094042;">System</p>
            </div>
            @endif
            <p style="margin: 0 0 10px; font-size: 14px; color: #094042;">
                {!! $notification->data['message'] !!}
            </p>
            <div style="font-size: 13px; color: #094042; margin-bottom: 10px;">
                <span>Date: {{ \Carbon\Carbon::parse($notification->created_at)->format('Y-m-d') }}</span> |
                <span>Time: {{ \Carbon\Carbon::parse($notification->created_at)->format('h:i') }}</span>
            </div>
            <div class="text-right">
                @if(empty($notification->read_at) && !empty($notification->data['type']) && $notification->data['type'] == 'group_join_request' && !empty($notification->data['id']))
                <button class="btn btn-success text-center mr-2" onclick="acceptJoiningRequest(<?= $notification->data['id'] ?>, <?= $notification->data['member_id'] ?>, <?= $notification->data['group_id'] ?>, true, '<?=$notification->id?>')">Accept</button>
                <button class="btn btn-danger text-center" onclick="acceptJoiningRequest(<?= $notification->data['id'] ?>, <?= $notification->data['member_id'] ?>, <?= $notification->data['group_id'] ?>, false,'<?=$notification->id?>')">Decline</button>
                @endif
                <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-warning">
                        Delete Notification
                    </button>
                </form>
                <a href="{{ $notification->data['url'] }}" class="btn" style="display: inline-block; background-color: #094042; color: white; text-decoration: none;">
                    {{ $notification->data['action'] }}
                </a>
            </div>
        </div>
    </div>
    @empty
    <h4>No data found</h4>
    @endforelse
</div>
