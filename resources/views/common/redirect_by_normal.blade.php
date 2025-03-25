@if (isset($retdata["success"]))
    @if (Auth::user()->hasRole("admin"))
        <script> document.location = admin_redirect_url; </script>
    @else
        <script> document.location = redirect_url; </script>
    @endif    
@endif