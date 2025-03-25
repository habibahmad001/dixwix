<div class="table-group-details mt-5">
    <table id="items_table_requests" class="table table-striped table-rounded">
        <thead>
        <tr>
            <th scope="col">Thumbnail</th>
            <th scope="col">Item Name</th>
            <th scope="col">Rental ID</th>
            <th scope="col">Requested By</th>
            <th scope="col">Due Date</th>
            <th scope="col">Requested Date</th>
            <th scope="col">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($group['entries'] as $entry): ?>
            <?php
            $user = \App\Models\User::where('id', $entry['reserved_by'])->first();
            $requestedBy = $user->name;
            $requestedEmail = $user->email;
            ?>
            <tr>
                <td>
                    <img style="width: 125px;"
                         src="<?= asset('public/storage/'.$entry['book']['cover_page']) ?>"
                         alt="Cover Page">
                </td>
                <td>{{$entry['book']['name']}}</td>
                <td>{{$entry['book']['item_id']}}</td>
                <td>{{$requestedBy}}</td>
                <td>{{date('d-m-Y',strtotime($entry['due_date']))}}</td>
                <td>{{date('d-m-Y',strtotime($entry['reserved_at']))}}</td>
                <td>
                    <ul class="list-inline d-flex">
                        <li class="list-inline-item">
                            <a class="btn btn-success btn-sm" href="javascript:void(0)" id="approve-reserve"
                               onclick="approveDisapprove('approve',<?= $entry['id'] ?>, <?= $entry['book']['id'] ?>)">Approve</a>
                        </li>
                        <li class="list-inline-item">
                            <a class="btn btn-danger btn-sm" href="javascript:void(0)" id="disapprove-reserve"
                               onclick="approveDisapprove('disapprove',<?= $entry['id'] ?>, <?= $entry['book']['id'] ?>)">Reject</a>
                        </li>
                    </ul>


                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
