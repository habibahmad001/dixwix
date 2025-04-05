<div class="inner_content_table_actions d-flex flex-column flex-md-row mt-4">
    <h4 class="inner_content_title">{{ $data['title'] }}</h4>
</div>

@if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
@endif

<div class="table-responsive">
    <table id="items_table" class="table">
        <thead>
            <tr>
                <th scope="col">Campaign Name</th>
                <th scope="col">Campaign Status</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($campaigns as $campaign)
            <tr>
                <td>{{ $campaign["name"] }}</td>
                <td>
                    @if(getSetting('gifto_gram_uuid') === $campaign["id"])
                        <a href="{{ route('update-gifto-requests', ['id' => $campaign["id"]])  }}" title="Click here to enable it.">
                            <span class="badge badge-success p-2">Enabled</span>
                        </a>
                    @else
                        <a href="{{ route('update-gifto-requests', ['id' => $campaign["id"]])  }}" title="Click here to enable it.">
                            <span class="badge badge-danger p-2">Disabled</span>
                        </a>
                    @endif
                </td>
                <td>
                    <div class="d-flex justify-content-center">
                        @if(getCampaignUUID($campaign["id"]))
                            <a href="{{ route('campaign-configuration-view', ['id' => $campaign["id"]])  }}">
                                <img src="{{ url("assets/media/edit-orange.png") }}" width="15px" height="15px" />
                            </a>
                        @else
                            <a href="{{ route('campaign-configuration-view', ['id' => $campaign["id"]])  }}">
                                <img src="{{ url("assets/media/settings.png") }}" width="15px" height="15px" />
                            </a>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" />
<script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#items_table').DataTable({
            paging: true
            , searching: true
            , ordering: true
            , responsive: true
            , lengthChange: true
            , pageLength: 10
            , info: true
            , autoWidth: false
            , columnDefs: [{
                orderable: false
                , targets: -1
            }]
            , language: {
                search: "Search:"
                , lengthMenu: "Show _MENU_ entries"
                , info: "Showing _START_ to _END_ of _TOTAL_ entries"
                , paginate: {
                    first: "First"
                    , last: "Last"
                    , next: "Next"
                    , previous: "Previous"
                }
            }
        });
    });

</script>
