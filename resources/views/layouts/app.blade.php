<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'DixWix') }}</title>

    <!-- Link to main CSS -->
  	@include('common.wo_login.start_scripts')
    @include('common.wo_login.end_scripts')
    <!-- Link to any additional CSS or specific page styles -->
    @yield('styles')  <!-- Custom styles for individual pages -->

    <!-- Meta Tags (Optional, you can add other meta tags like description or social meta tags) -->
    @yield('meta')

    <!-- Dynamic header section (includes any external files or page-specific header data) -->
</head>
<body>
  	
   @include('common.wo_login.header')
    <!-- Main content section -->
    <main>
        @yield('content')  <!-- Dynamic content for each page -->
    </main>

    <!-- Footer Section -->
   @include('common.wo_login.footer') <!-- Dynamic footer section -->

    <!-- Include core JavaScript files -->
    <script src="{{ asset('js/main.js') }}"></script>  <!-- Your main JavaScript file -->

    <!-- Any page-specific JavaScript -->
    @yield('scripts')  <!-- Dynamic scripts for individual pages -->
    
    <!-- Optional: Add any third-party scripts that are required globally -->
    @yield('extra-scripts')

</body>
</html>
