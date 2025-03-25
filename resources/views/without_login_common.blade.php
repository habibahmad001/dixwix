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
    @include('common.wo_login.start_scripts')

    <style>
        .navbar-collapse {
            /*background-color: white; !* White background for mobile menu *!*/
            z-index: 999;
        }

        @media (max-width: 991.98px) {
            .navbar-light .navbar-collapse {
                margin-top: 0px;
            }
        }
        @media (max-width: 991px) {
            .navbar-collapse {
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: white; /* Avoid background image in mobile */
                padding: 20px;
                box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</head>
<body>

<main>

  <div class="container-fluid p-0">
    <div class="dashboard_wrapper">
    @include('common.wo_login.spinner')
    @include('common.wo_login.header')

    @include($data['template'])

    </div>

  </div>
</main>
    @include('common.wo_login.end_scripts')
    @include('common.wo_login.footer')
</body>
</html>
