<div class="inner_content_table_actions d-flex flex-column flex-md-row mt-4">
    <h4 class="inner_content_title">{{ $data['title'] }}</h4>
</div>

<div class="table-responsive">
    <table id="items_table" class="table">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Allowed Groups</th>
                <th scope="col">Allowed Items</th>
                <th scope="col">Price</th>
                <th scope="col">Stripe Price ID</th>
                <th scope="col" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($plans as $plan)
            <tr>
                <td>{{ $plan->name }}</td>
                <td>{{ $plan->allowed_groups }}</td>
                <td>{{ $plan->allowed_items }}</td>
                <td>{{ $plan->price ?? 'Free' }}</td>
                <td>{{ $plan->stripe_price_id }}</td>
                <td>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route("edit-plan",["id"=>$plan->id]) }}">
                            <img src="{{ url("assets/media/edit-orange.png") }}" width="15px" height="15px" />
                        </a>
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
