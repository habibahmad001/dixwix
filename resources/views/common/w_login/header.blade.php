<!-- Mobile Popup Menu -->
<div id="mobilePopupMenu" class="mobile-menu-popup">
    <div class="mobile-menu-header d-flex justify-content-between align-items-center px-3 py-2">
        <button id="closeMobileMenu" class="btn btn-light btn-sm">&times;</button>
    </div>
    <ul class="list-group">
        <!-- Dashboard Links -->
        @if (\Auth::user()->hasRole('admin'))
        <li class="list-group-item">
            <a class="d-flex align-items-center link-dark text-decoration-none" href="{{ route('admin-dashboard') }}">
                Dashboard
            </a>
        </li>
        @endif

        @if (\Auth::user()->hasRole('user') && \Auth::user()->can("dashboard"))
        <li class="list-group-item">
            <a class="d-flex align-items-center link-dark text-decoration-none" href="{{ route('dashboard') }}">
                Dashboard
            </a>
        </li>
        @endif
        <!-- Items Links with Submenu -->
        @if (\Auth::user()->hasRole('admin') || (\Auth::user()->hasRole('user') && \Auth::user()->group_type == 1 && (\Auth::user()->can("my-items") || \Auth::user()->can("add-book"))))
        <li class="list-group-item">
            <a class="submenu-toggle d-flex align-items-center link-dark text-decoration-none" href="javascript:void(0)" data-target="#mobileItemSubMenu">
                Items
            </a>
            <ul class="list-group ps-3 submenu" id="mobileItemSubMenu" style="display: none;">
                @if (\Auth::user()->can("all-items"))
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('all-items') }}">All Items</a>
                </li>
                @endif
                @if (\Auth::user()->can("my-items"))
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('my-items') }}">My Items</a>
                </li>
                @endif
                @if (\Auth::user()->hasRole('admin') || (\Auth::user()->hasRole('user') && \Auth::user()->can("add-book")))
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('add-book') }}">Add Item</a>
                </li>
                @endif
                @if (\Auth::user()->hasRole('user') && \Auth::user()->can("add-book"))
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('borrowed-items') }}">Borrowed Items</a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Groups Links with Submenu -->
        @if (\Auth::user()->hasRole('admin') || (\Auth::user()->hasRole('user') && (\Auth::user()->can("my-groups") || \Auth::user()->can("add-group") || \Auth::user()->can("join-group"))))
        <li class="list-group-item">
            <a class="submenu-toggle d-flex align-items-center link-dark text-decoration-none" href="javascript:void(0)" data-target="#mobileGroupSubMenu">
                Groups
            </a>
            <ul class="list-group ps-3 submenu" id="mobileGroupSubMenu" style="display: none;">
                @if (\Auth::user()->can("all-groups"))
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('all-groups') }}">All Groups</a>
                </li>
                @endif
                @if (\Auth::user()->hasRole('admin') || (\Auth::user()->hasRole('user') && \Auth::user()->can("add-group") && \Auth::user()->group_type == 1))
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('add-group') }}">Add Group</a>
                </li>
                @endif
                @if (\Auth::user()->can("my-groups"))
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('my-groups') }}">My Groups</a>
                </li>
                @endif
                @if (\Auth::user()->can("join-group"))
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('join-group') }}">Join Group</a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        @if (\Auth::user()->hasRole('user') && \Auth::user()->can("my-rewards"))
        <li class="list-group-item">
            <a class="d-flex align-items-center link-dark text-decoration-none" href="{{ route('my-rewards') }}">
                My Rewards
            </a>
        </li>
        @endif
        @if (\Auth::user()->hasRole('user') && \Auth::user()->can("my-rewards"))
        <li class="list-group-item">
            <a class="d-flex align-items-center link-dark text-decoration-none" href="{{ route('my-account') }}">
                Account
            </a>
        </li>
        @endif

        @if (\Auth::user()->hasRole('admin'))
        <li class="list-group-item">
            <a class="submenu-toggle d-flex align-items-center link-dark text-decoration-none" href="javascript:void(0)" data-target="#mobileUsersSubMenu">
                Users
            </a>
            <ul class="list-group ps-3 submenu" id="mobileUsersSubMenu" style="display: none;">
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('all-users') }}">All Users</a>
                </li>
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('add-user') }}">Add User</a>
                </li>
            </ul>
        </li>
        @endif

        @if (\Auth::user()->hasRole('admin'))
        <li class="list-group-item">
            <a class="submenu-toggle d-flex align-items-center link-dark text-decoration-none" href="javascript:void(0)" data-target="#mobileRentalsSubMenu">
                Rentals
            </a>
            <ul class="list-group ps-3 submenu" id="mobileRentalsSubMenu" style="display: none;">
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('loan-rules.create') }}">Add Loan Rule</a>
                </li>
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('loan-rules.index') }}">Loan Rules</a>
                </li>
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('admin.overdue-rent-items') }}">Over Due Items</a>
                </li>
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('admin.loan-history') }}">Loan History</a>
                </li>
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('admin.reservation-report') }}">Reservation Report</a>
                </li>
            </ul>
        </li>
        @endif

        @if (Auth::user()->hasRole('admin'))
        <li class="list-group-item">
            <a class="submenu-toggle d-flex align-items-center link-dark text-decoration-none" href="javascript:void(0)" data-target="#mobileGeneralSubMenu">
                General
            </a>
            <ul class="list-group ps-3 submenu" id="mobileGeneralSubMenu" style="display: none;">
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('add-settings') }}">Add Settings</a>
                </li>
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('view-all-settings') }}">View All Settings</a>
                </li>
            </ul>
        </li>
        <li class="list-group-item">
            <a class="submenu-toggle d-flex align-items-center link-dark text-decoration-none" href="javascript:void(0)" data-target="#mobileSettingsSubMenu">
                Settings
            </a>
            <ul class="list-group ps-3 submenu" id="mobileSettingsSubMenu" style="display: none;">
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('add-category') }}">Add Category</a>
                </li>
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('view-all-categories') }}">View All Categories</a>
                </li>
            </ul>
        </li>

        <li class="list-group-item">
            <a class="submenu-toggle d-flex align-items-center link-dark text-decoration-none" href="javascript:void(0)" data-target="#mobileBlogSubMenu">
                Blog
            </a>
            <ul class="list-group ps-3 submenu" id="mobileBlogSubMenu" style="display: none;">
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('blog-post.create') }}">Add Blog Post</a>
                </li>
                <li class="list-group-item">
                    <a class="link-dark text-decoration-none" href="{{ route('blog-post.index') }}">All Blog Posts</a>
                </li>
            </ul>
        </li>
        @endif

        @if (\Auth::user()->hasRole('admin'))
        <li class="list-group-item">
            <a class="d-flex align-items-center link-dark text-decoration-none" href="{{ route('subscription-plans') }}">
                Plans
            </a>
        </li>
        @endif

        @if (\Auth::user()->hasRole('admin'))
            <li class="list-group-item">
                <a class="submenu-toggle d-flex align-items-center link-dark text-decoration-none" href="javascript:void(0)" data-target="#mobileRewardSubMenu">
                    Reward Plans
                </a>
                <ul class="list-group ps-3 submenu" id="mobileRewardSubMenu" style="display: none;">
                    <li class="list-group-item">
                        <a class="link-dark text-decoration-none" href="#">Add Reward Plan</a>
                    </li>
                    <li class="list-group-item">
                        <a class="link-dark text-decoration-none" href="{{ route('reward-plans') }}">All  Reward Plans</a>
                    </li>
                </ul>
            </li>

            <li class="list-group-item">
                <a href="{{ route('redeem-requests') }}" class="d-flex align-items-center link-dark text-decoration-none">
                    Redeem Requests
                </a>
            </li>

            <li class="list-group-item">
                <a href="{{ route('transfer-requests') }}" class="d-flex align-items-center link-dark text-decoration-none">
                    Transfer Point Requests
                </a>
            </li>
        @endif

        @if (\Auth::user()->hasRole('admin'))
        <li class="list-group-item">
            <a class="d-flex align-items-center link-dark text-decoration-none" href="#" onclick="showSwalMessageWithCallback('Confirmation','The backup will take some time. Please don’t reload the page.','question',function() {runBackup()})">
                Backup
            </a>
        </li>
        @endif

        <li class="list-group-item">
            <a class="d-flex align-items-center link-dark text-decoration-none" href="{{ route('logout') }}">
                Logout
            </a>
        </li>
    </ul>
</div>

<style>
    .mobile-menu-popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        color: #fff;
        overflow-y: auto;
        z-index: 1050;
        display: none;
    }

    .mobile-menu-header {
        background-color: #D94E29;
        border-bottom: 1px solid #555;
    }

    .submenu {
        background-color: #D94E29;
    }

    .submenu-toggle {
        cursor: pointer;
    }

    .submenu .list-group-item {
        background-color: #D94E29;
    }

    .submenu .list-group-item a {
        color: white !important;
    }

</style>

<div id="sidebar" class="sticky-top d-none d-md-block">
    <div class="sidebar_wrapper d-flex flex-column">
        <a href="{{ route('dashboard') }}" class="dashboard-logo d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
            <img src="{{ url('assets/media/logo.png') }}" alt="Logo" width="125px">
        </a>
        <hr />
        <ul class="nav nav-pills flex-column mb-auto flex-nowrap">
            @if (\Auth::user()->hasRole('admin'))
            <li class="nav-item">
                <a href="{{ route('admin-dashboard') }}" {!! $data['title'] === "Dashboard" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' !!}>
                    <img src="{{ url('assets/media/dashboard.png') }}">
                    <span>Dashboard {!! $data['title'] === "Dashboard" ? '<i class="fa fa-solid fa-angle-right"></i>' : '' !!}</span>
                </a>
            </li>
            @endif
            @if (\Auth::user()->hasRole('user') && \Auth::user()->can("dashboard"))
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" {!! $data['title'] === "Dashboard" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' !!}>
                    <img src="{{ url('assets/media/dashboard.png') }}">
                    <span>Dashboard {!! $data['title'] === "Dashboard" ? '<i class="fa fa-solid fa-angle-right"></i>' : '' !!}</span>
                </a>
            </li>
            @endif
            @if (\Auth::user()->hasRole('admin') || (\Auth::user()->hasRole('user') && \Auth::user()->group_type == 1 && (\Auth::user()->can("my-items") || \Auth::user()->can("add-book"))))
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#item_sub_menu" role="button" aria-expanded="false" aria-controls="item_sub_menu">
                    <img src="{{ url('assets/media/items.png') }}">
                    <span>Items</span>
                     <i class="fa fa-chevron-down"></i>
                </a>
                </a>
                <ul class="sub-menu collapse {!! ($data['title'] === "All Items" || $data['title'] === "My Items" || $data['title'] === "Add Item" || $data['title'] === "Edit Item") || $data['title'] == 'Borrowed Items' ? "show" : "" !!}" id="item_sub_menu">
                    @if (\Auth::user()->can("all-items"))
                    <li>
                        <a href="{{ route('all-items') }}" {!! $data['title'] === "All Items" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' !!}>
                            <span>All Items {!! $data['title'] === "My Items" ? '<i class="fa fa-solid fa-angle-right"></i>' : '' !!}</span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->can("my-items"))
                    <li>
                        <a href="{{ route('my-items') }}" {!! $data['title'] === "My Items" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' !!}>
                            <span>My Items {!! $data['title'] === "My Items" ? '<i class="fa fa-solid fa-angle-right"></i>' : '' !!}</span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->hasRole('admin') || (\Auth::user()->hasRole('user') && \Auth::user()->can("add-book")))
                    <li>
                        <a href="{{ route('add-book') }}" {!! $data['title'] === "Add Item" || $data['title'] === "Edit Item" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' !!}>
                            <span>Add Item {!! $data['title'] === "Add Item" || $data['title'] === "Edit Item" ? '<i class="fa fa-solid fa-angle-right"></i>' : '' !!}</span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->hasRole('user') && \Auth::user()->can("add-book"))
                    <li>
                        <a href="{{ route('borrowed-items') }}" {!! $data['title'] === "Borrowed Items" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' !!}>
                            <span>Borrowed Items {!! $data['title'] === "Borrowed Items" ? '<i class="fa fa-solid fa-angle-right"></i>' : '' !!}</span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasRole('admin') || (\Auth::user()->hasRole('user') && (\Auth::user()->can("my-groups") || \Auth::user()->can("add-group") || \Auth::user()->can("join-group"))))
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#group_sub_menu" role="button" aria-expanded="false" aria-controls="group_sub_menu">
                    <img src="<?= url('assets/media/groups.png') ?>">
                    <span>Groups</span>
                    <i class="fa fa-chevron-down"></i>
                </a>
                <ul id="group_sub_menu" class="sub-menu collapse <?= (($data['title'] === "All Groups" || $data['title'] === "My Groups" || $data['title'] === "Add Group" || $data['title'] === "Edit Group" || $data['title'] === "Lender Groups" || $data['title'] === "Borrower Groups" || $data['title'] === "Join Group") ? "show" : "") ?>">
                    @if (\Auth::user()->can("all-groups"))
                    <li>
                        <a href="<?= route('all-groups') ?>" <?= ($data['title'] === "All Groups" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>All Groups <?= ($data['title'] === "All Groups" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->hasRole('admin') || (\Auth::user()->hasRole('user') && \Auth::user()->can("add-group") && \Auth::user()->group_type == 1))
                    <li>
                        <a href="<?= route('add-group') ?>" <?= ($data['title'] === "Add Group" || $data['title'] === "Edit Group" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>Add Group <?= ($data['title'] === "Add Group" || $data['title'] === "Edit Group" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->can("my-groups"))
                    <li>
                        <a href="<?= route('my-groups') ?>" <?= ($data['title'] === "My Groups" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>My Groups <?= ($data['title'] === "My Groups" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    @endif
                    @if (\Auth::user()->can("join-group"))
                    <li>
                        <a href="<?= route('join-group') ?>" <?= ($data['title'] === "Join Group" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>Join Group <?= ($data['title'] === "Join Group" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    @endif
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasRole('user') && \Auth::user()->can("my-rewards"))
            <li class="nav-item">
                <a href="{{ route('my-rewards') }}" {!! ($data['title']==="My Rewards" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' ) !!}>
                    <img src="<?= url('assets/media/rewards.png') ?>">
                    <span>Rewards {!! ($data['title'] === "My Rewards" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') !!}</span>
                </a>
            </li>
            @endif

            @if (\Auth::user()->hasRole('user') && \Auth::user()->can("my-rewards"))
            <li class="nav-item">
                <a href="{{ route('my-account') }}" {!! ($data['title']==="My Account" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' ) !!}>
                    <img src="<?= url('assets/media/my-account.png') ?>">
                    <span>Account {!! ($data['title'] === "My Account" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') !!}</span>
                </a>
            </li>
            @endif

            @if (Auth::user()->hasRole('admin'))
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#users_sub_menu" role="button" aria-expanded="false" aria-controls="users_sub_menu">
                    <img src="<?= url('assets/media/users-icon.png') ?>">
                    <span>Users</span>
                    <i class="fa fa-chevron-down"></i>
                </a>
                <ul class="sub-menu collapse <?= (($data['title'] === "Add User" || $data['title'] === "Edit User" || $data['title'] === "View All User") ? "show" : "") ?>" id="users_sub_menu">
                    <li>
                        <a href="<?= route('add-user') ?>" <?= ($data['title'] === "Add User" || $data['title'] === "Edit User" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>Add User <?= ($data['title'] === "Add User" || $data['title'] === "Edit User" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= route('all-users') ?>" <?= ($data['title'] === "All Users" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>All Users <?= ($data['title'] === "All Users" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#loan_rule_sub_menu" role="button" aria-expanded="false" aria-controls="loan_rule_sub_menu">
                    <img src="<?= url('assets/media/rental.png') ?>">
                    <span>Rentals</span>
                    <i class="fa fa-chevron-down"></i>
                </a>
                <ul class="sub-menu collapse <?= (($data['title'] === "Add Loan Rule" || $data['title'] === "Edit Loan Rule" || $data['title'] === "Loan Rules") || $data['title'] == "Over Due Rent Items" ? "show" : "") ?>" id="loan_rule_sub_menu">
                    <li>
                        <a href="<?= route('loan-rules.create') ?>" <?= ($data['title'] === "Add Loan Rule" || $data['title'] === "Edit Loan Rule" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>Add Loan Rule <?= ($data['title'] === "Add Loan Rule" || $data['title'] === "Edit Loan Rule" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= route('loan-rules.index') ?>" <?= ($data['title'] === "Loan Rules" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>Loan Rules <?= ($data['title'] === "Loan Rules" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= route('admin.overdue-rent-items') ?>" <?= ($data['title'] == "Over Due Rent Items" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>Over Due Items <?= ($data['title'] === "Over Due Rent Items" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= route('admin.loan-history') ?>" <?= ($data['title'] == "Loan History" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>Loan History <?= ($data['title'] === "Loan History" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= route('admin.reservation-report') ?>" <?= ($data['title'] == "Reservation Report" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>Reservation Report <?= ($data['title'] === "Reservation Report" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#general_sub_menu" role="button" aria-expanded="false" aria-controls="general_sub_menu">
                    <img src="<?= url('assets/media/setting.png') ?>">
                    <span>General</span>
                    <i class="fa fa-chevron-down"></i>
                </a>
                <ul class="sub-menu collapse <?= (($data['title'] === "Add Settings" || $data['title'] === "Edit Settings" || $data['title'] === "View All Settings") ? "show" : "") ?>" id="general_sub_menu">
                    <li>
                        <a href="<?= route('add-settings') ?>" <?= ($data['title'] === "Add Settings" || $data['title'] === "Edit Settings" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>General <?= ($data['title'] === "Add Settings" || $data['title'] === "Edit Settings" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= route('view-all-settings') ?>" <?= ($data['title'] === "View All Settings" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>View All Settings <?= ($data['title'] === "View All Settings" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#settings_sub_menu" role="button" aria-expanded="false" aria-controls="settings_sub_menu">
                    <img src="<?= url('assets/media/setting.png') ?>">
                    <span>Settings</span>
                    <i class="fa fa-chevron-down"></i>
                </a>
                <ul class="sub-menu collapse <?= (($data['title'] === "Add Category" || $data['title'] === "Edit Category" || $data['title'] === "View All Categories") ? "show" : "") ?>" id="settings_sub_menu">
                    <li>
                        <a href="<?= route('add-category') ?>" <?= ($data['title'] === "Add Category" || $data['title'] === "Edit Category" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>Categories <?= ($data['title'] === "Add Category" || $data['title'] === "Edit Category" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= route('view-all-categories') ?>" <?= ($data['title'] === "View All Categories" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"') ?>>
                            <span>View All Categories <?= ($data['title'] === "View All Categories" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') ?></span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if(Auth::user()->hasRole('admin'))
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#blogs_sub_menu" role="button" aria-expanded="false" aria-controls="blogs_sub_menu">
                    <img src="{{ url('assets/media/users-icon.png') }}">
                    <span>Blog</span>
                    <i class="fa fa-chevron-down"></i>
                </a>
                <ul class="sub-menu collapse {{ ($data['title'] === "Add Blog Post" || $data['title'] === "Edit Blog Post" || $data['title'] === "View All Blog Posts") ? "show" : "" }}" id="blogs_sub_menu">
                    <li>
                        <a href="{{ route('blog-post.create') }}" {!! $data['title']==="Add Blog Post" || $data['title']==="Edit Blog Post" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' !!}>
                            <span>Add Blog Post {!! $data['title'] === "Add Blog Post" || $data['title'] === "Edit Blog Post" ? '<i class="fa fa-solid fa-angle-right"></i>' : '' !!}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('blog-post.index') }}" {!! $data['title'] ==="All Blog Posts" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' !!}>
                            <span>All Blog Posts {!! $data['title'] === "All Blog Posts" ? '<i class="fa fa-solid fa-angle-right"></i>' : '' !!}</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif

            @if (\Auth::user()->hasRole('admin'))
            <li class="nav-item">
                <a href="{{ route('subscription-plans') }}" {!! ($data['title']==="Subscription Plans" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' ) !!}>
                    <img src="<?= url('assets/media/plans.png') ?>">
                    <span>Plans {!! ($data['title'] === "Subscription Plans" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') !!}</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#reward_plan_sub_menu" role="button" aria-expanded="false" aria-controls="reward_plan_sub_menu">
                    <img src="{{ url('assets/media/plans.png') }}">
                    <span>Reward Plans</span>
                    <i class="fa fa-chevron-down"></i>
                </a>
                <ul class="sub-menu collapse {{ ($data['title'] === "Reward Plans" || $data['title'] === "Add Reward Plan" || $data['title'] === "Edit Reward Plan" ) ? "show" : "" }}" id="reward_plan_sub_menu">
                    <li>
                        <a href="{{ route('create-reward') }}" {!! $data['title']==="Add Reward Plan" || $data['title']==="Edit Reward Plan" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' !!}>
                            <span>Add Reward Plans</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('reward-plans') }}" {!! $data['title'] ==="All Reward Plans" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' !!}>
                            <span>All Reward Plans</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="{{ route('redeem-requests') }}" {!! ($data['title']==="Redeem Requests" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' ) !!}>
                    <img src="<?= url('assets/media/plans.png') ?>">
                    <span>Redeem Requests {!! ($data['title'] === "Redeem Requests" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') !!}</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('transfer-requests') }}" {!! ($data['title']==="Transfer Point Requests" ? 'class="nav-link active" aria-current="page"' : 'class="nav-link"' ) !!}>
                    <img src="<?= url('assets/media/plans.png') ?>">
                    <span>Transfer Point Requests {!! ($data['title'] === "Transfer Point Requests" ? '<i class="fa fa-solid fa-angle-right"></i>' : '') !!}</span>
                </a>
            </li>
            @endif

            @if (\Auth::user()->hasRole('admin'))
            <li class="nav-item">
                <a href="#" onclick="showSwalMessageWithCallback('Confirmation','The backup will take some time. Please don’t reload the page.','question',function() {runBackup()})" class="nav-link">
                    <img src="<?= url('assets/media/backupdata.png') ?>">
                    <span>Backup</span>
                </a>
            </li>
            @endif

            <li class="nav-item">
                <a href="<?= route('logout') ?>" class="nav-link">
                    <img src="<?= url('assets/media/dashboard.png') ?>">
                    <span>Logout</span>
                </a>
            </li>
        </ul>
        <hr>
        @if (!Auth::user()->hasRole('admin'))
        <div class="sidebar_footer">
            <a href="<?= route('my-groups') ?>" class="invite btn">Invite</a>
            <div class="social-links">
                <ul class="list-inline">
                    <li class="list-inline-item"><a href="#"><img src="<?= url('assets/media/fb-grey.png') ?>"></a>
                    </li>
                    <li class="list-inline-item"><a href="#"><img src="<?= url('assets/media/google-grey.png') ?>"></a>
                    </li>
                    <li class="list-inline-item"><a href="#"><img src="<?= url('assets/media/twitter-grey.png') ?>"></a>
                    </li>
                    <li class="list-inline-item"><a href="#"><img src="<?= url('assets/media/linkedin-grey.png') ?>"></a></li>
                </ul>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
    function runBackup() {

        $.ajax({
            url: "{{ route('admin.backup') }}"
            , method: "POST"
            , headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
            , success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Success!'
                        , text: response.message
                        , icon: 'success'
                        , timer: 3000
                        , showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Error'
                        , text: response.message
                        , icon: 'error'
                    });
                }
            }
            , error: function(xhr) {
                Swal.fire({
                    title: 'Error'
                    , text: 'An error occurred while running the backup.'
                    , icon: 'error'
                });
            }
        });

    }

    $(document).ready(function() {

        $('#openMobileMenu').on('click', function() {
            $('#mobilePopupMenu').fadeIn();
        });

        $('#closeMobileMenu').on('click', function() {
            $('#mobilePopupMenu').fadeOut();
        });

        $('.submenu-toggle').on('click', function() {
            const target = $(this).data('target');
            $(target).slideToggle();
        });

    });

</script>
