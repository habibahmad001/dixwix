<form name="barcode-form" id="barcode-form" method="post" action="{{route('view-barcode')}}">
@csrf
<input type="text" name="barcode_data"/>
<input type="submit" value="Generate"/>
</form>