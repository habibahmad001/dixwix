@if (isset($retdata["success"]))
    <script> showSwalMessageWithCallback("Note","{{$retdata['success']}}","success",function(){document.location = redirect_url;});</script>
@endif