@if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
@endif
<table id="items_table" class="table">
    <thead>
        <tr>
            <th scope="col">Name</th>
            <th scope="col">Value</th>
            <th scope="col">Date Added</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data["settings"] as $setting)
            <tr>
                <td>{{ $setting->name }}</td>
                <td>{{ $setting->type == 1 ? ($setting->value == 1 ? 'Yes' : 'No') : $setting->value }}</td>
                <td>{{ date("m/d/Y",strtotime($setting->created_at)) }}</td>
                <td>
                    <a href="{{ route("edit-settings",["id"=>$setting->id]) }}">
                        <img src="{{ url("assets/media/edit-orange.png") }}" width="15px" height="15px"/>
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
