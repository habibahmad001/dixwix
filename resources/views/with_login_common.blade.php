<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/media/logo.png') }}" type="image/x-icon">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', $data['title']) }}</title>

    <!-- Scripts -->
    @include('common.w_login.start_scripts')

    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>

        /*
        let soundEnabled = false;
        const notificationSound = new Audio('https://cdn.freesound.org/previews/653/653820_200878-lq.mp3');

        document.addEventListener('click', () => {
            if (!soundEnabled) {
                soundEnabled = true;
                notificationSound.play().catch(err => {
                    console.error("Error enabling sound:", err);
                });
            }
        });
        */

        var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
        });

        const user_notify_id = '{{ auth()->id() }}';
        var channel = pusher.subscribe('notification-channel_' + user_notify_id);
            channel.bind('update-notifications', function(data) {

            const {notification} =  data;

            if (!notification || !notification.data) {
                return;
            }

           /* if (soundEnabled) {
                notificationSound.play().catch(err => {
                    console.error("Error playing notification sound:", err);
                });
            }
            */

            let notificationContainer = document.querySelector('.drop-content');
            let badgeCounter = document.querySelector('.badge-info');
            badgeCounter.classList.add("badge-danger");
            let notifyTitleCounter = document.querySelector('.notify-drop-title b');

            let createdAt = 'Just now';
            let title = notification.data.title || 'View details';
            let url = notification.data.url || '#';

            let newNotification = `
                <li class="border-bottom py-2 px-3">
                    <a href="${url}" class="d-flex align-items-center text-decoration-none">
                        <div class="flex-grow-1">
                            <p class="mb-0 text-dark">${title}</p>
                            <small class="text-muted">${createdAt}</small>
                        </div>
                    </a>
                </li>
            `;

            notificationContainer.insertAdjacentHTML('afterbegin', newNotification);

            let unreadCount = parseInt(badgeCounter.textContent) || 0;
            badgeCounter.textContent = unreadCount + 1;

            let totalCount = parseInt(notifyTitleCounter.textContent) || 0;
            notifyTitleCounter.textContent = totalCount + 1;
        });

    </script>
</head>
<body>
    <main>
       @php
        if(isset($data['search_action']))
        {
        $search_action = $data['search_action'];
        }
        else {
            $search_action = Request()->segment(count(Request()->segments()));
        }
        if(isset($search_action) && trim($search_action)!="" && strtolower($search_action)=='dashboard'){
            $search_action = 'my-groups';
        }
        @endphp

        <div class="container-fluid p-0">
            <button id="openMobileMenu" class="btn btn-primary d-md-none w-100">Menu</button>
            <div class="dashboard_wrapper">
                <!-- @include('common.wo_login.spinner') -->
                @include('common.w_login.header')
                <div id="content">
                    <div class="sticky-top">
                        <div class="disabled_header flex-column flex-md-row align-items-start">
                            <?php if (Auth::user()->hasRole("admin")) { ?>
                            <div class="heading">
                                <h2>Admin Dashboard</h2>
                            </div>
                            <?php } else { ?>
                            <form id="search-form" method="post" action="<?= route('search-item') ?>">
                                @csrf
                                <input type="hidden" name="search_action" value="{{ $search_action }}">
                                <div class="serach_input d-flex flex-row">
                                    <button type="submit" class="btn btn-primary" data-mdb-ripple-init value="">
                                        <img src="<?= url('assets/media/search-green.png') ?>">
                                    </button>
                                    <input type="search" id="search-item" name="search-item" class="form-control" placeholder="Search Items or Category or Group Details (Name, State, Zipcode)" value="<?= (isset($data['title']) && !empty($data['search-item']) ? $data['search-item'] : "") ?>" />
                                </div>
                            </form>
                            <?php } ?>

                            <div class="disabled_header_menu d-flex flex-column flex-md-row mt-2 mt-md-auto">
{{--                                <?php if (!Auth::user()->hasRole("admin")) { ?>--}}
{{--                                <a id="membership" href="<?=route("membership")?>" class="btn link_with_img">--}}
{{--                                    <img src="<?= url('assets/media/membership.png') ?>"> Membership--}}
{{--                                </a>--}}
{{--                                <?php } ?>--}}
                                <div class="d-flex align-items-center">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-bell">
                                            <span class="badge badge-info">{{ auth()->user()->unreadNotifications->count() }}</span>
                                        </i>
                                    </a>
                                    <ul class="dropdown-menu notify-drop">
                                        <div class="notify-drop-title">
                                            <div class="p-3 h5">Notification(s) (<b>{{ auth()->user()->notifications->count() }}</b>)</div>
                                            <hr />
                                        </div>
                                        <div class="drop-content">
                                            @if(auth()->user()->notifications->count() > 0)
                                            @foreach(auth()->user()->notifications()->latest()->take(8)->get() as $notification)
                                            <li class="border-bottom py-2 px-3">
                                                <a href="{{ $notification->data['url'] ?? '#' }}" class="d-flex align-items-center text-decoration-none">
                                                    <div class="flex-grow-1">
                                                        <p class="mb-0 text-dark">{{ isset($notification->data['title']) ? $notification->data['title'] : 'View details' }}</p>
                                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </a>
                                            </li>
                                            @endforeach
                                            @else
                                            <li class="text-center py-3">
                                                <p class="mb-0 text-muted">No notifications available.</p>
                                            </li>
                                            @endif
                                        </div>
                                        <div class="notify-drop-footer text-center">
                                            <a href="<?=route('my-notifications')?>"><i class="fa fa-eye"></i> View All</a>
                                        </div>
                                    </ul>
                                    <a id="disabled_header_menu_profile" href="#">
                                        <img src="<?= (isset(Auth::user()->profile_pic) && !empty(Auth::user()->profile_pic)) ? asset('storage/'.Auth::user()->profile_pic): url('assets/media/userimg.png') ?>" alt="User Profile Pic" width="30" class="rounded-circle">
                                    </a>
                                    <a id="disabled_header_menu_settings" href="<?= route('edit-profile') ?>" class="disabled_header_menu_icon">
                                        <img src="<?= url('assets/media/settings.png') ?>">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include($data['template'])
                </div>
            </div>
        </div>
    </main>
    <?php if (isset($data['script_file'])) {
        $file_script_name = "scripts." . $data['script_file']; ?>
    @include($file_script_name)
    <?php } ?>
    @include('scripts.group_listing')
    @include('common.w_login.end_scripts')
    @include('common.redirect_by_normal')
    @include('common.w_login.footer')
</body>
</html>
<style>
    .qr-code {
        width: 80px;
        height: 70px;
        border: 3px solid #094042;
        padding: 5px;
        border-radius: 10px;
        margin-right: 10px;
        box-shadow: 10px 10px 5px 0px rgba(0, 0, 0, 0.75);
    }

    @media (max-width: 767px) {
        .requested-memeber-img {
            width: 100% !important;
        }
    }

    @media (max-width: 768px) {
        .sticky-top {
            position: relative;
        }

        .disabled_header_menu {
            width: 100%;
        }

        .serach_input {
            width: 293px;
        }

        .dashboard_wrapper #content {
            width: 100%;
            padding: 0px 7px 15px 7px;
        }

        #toolbar_nav_dev {
            width: 100%;
        }

        .bottom-links {
            margin-top: 10px;
            flex-direction: column;
        }

        .main-box-amazon {
            padding: 10px;
        }

        .main-box {
            margin-top: 10px;
            padding: 10px;
        }

        #csv_tab {
            padding: 2px 15px;
        }

        #csv_tab .box-form {
            width: 100%;
        }

        #csv_tab .text-box {
            width: 100%;
        }

        .item_summary,
        .item_description {
            width: 100%;
        }

        .item_summary_actions {
            gap: 20px;
            flex-direction: column;
        }

        .tab-container>.tab-buttons {
            gap: 10px
        }

        .tab-buttons .tab-button {
            width: 100% !important;
        }

        .search-result-image img {
            width: 100%;
            margin-bottom: 5px;
            border-radius: 5px;
        }

        ul.list-group.list-group-horizontal-md.group-main-actions {
            flex-direction: row;
            align-items: center;
        }

        .group-show-qr-code-section {
            justify-content: start !important;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .group-desc-row .members-section .image-section {
            width: 100%;
        }

        .show-group-members-buttons {
            flex-direction: column !important;
            margin-top: 10px;
            gap: 10px;
        }

    }

</style>
