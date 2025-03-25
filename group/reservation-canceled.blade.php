<div class="table-group-details table-responsive group-books-list">
    <table id="items_table_requests_cancelled" class="table table-striped table-rounded">
        <thead>
            <tr>
                <th>Thumbnail</th>
                <th>Item Name</th>
                <th>Rental Price</th>
                <th>Item Category</th>
                <th>Owner Name</th>
                <th>Item Location</th>
                <th>Requested By</th>
                <th>Cancel By</th>
                <th>Cancel Reason</th>
                <th>Canceled At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($group['canceledReservations'] as $reservation)
            <tr>
                <td>
                    <img style="width: 125px;" src="{{ $reservation['book']['cover_page'] }}" alt="Cover Page">
                </td>
                <td>{{ $reservation['book']['name'] }}</td>
                <td>{{ $reservation['book']['rent_price'] }}</td>
                <td>{{ $reservation['book']['category']['name'] }}</td>
                <td style="text-align: center">
                    {{ $reservation['book']['user']['name'] ?? 'Unknown' }}
                </td>
                <td style="text-align: center">
                    {{ is_array(json_decode($reservation['book']['locations'], true))
                        ? implode(", ", json_decode($reservation['book']['locations']))
                        : $reservation['book']['locations'] }}
                </td>
                <td style="text-align: center">
                    @if($reservation['reserved_by']['id'] == auth()->user()->id)
                    Me
                    @else
                    {{ $reservation['reserved_by']['name'] ?? 'Unknown' }}
                    @endif
                </td>
                <td style="text-align: center">
                    @if($reservation['canceled_by']['id'] == auth()->user()->id)
                    Me
                    @else
                    {{ $reservation['canceled_by']['name'] ?? 'Unknown' }}
                    @endif
                </td>
                <td>{{ $reservation['cancel_reason'] }}</td>
                <td>{{ \Carbon\Carbon::parse($reservation['canceled_at'])->format('d-m-Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
