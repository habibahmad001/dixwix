<div class="inner_content">
    @if (count($borrowedItems) > 0)
    <div class="table-group-details table-responsive group-books-list">
        <table id="items_table_group" class="table table-striped table-rounded">
            <thead>
                <tr>
                    <th scope="col">Thumbnail</th>
                    <th scope="col">Latest Image</th>
                    <th scope="col">Item Name</th>
                    <th scope="col">Rental Price</th>
                    <th scope="col">Item Category</th>
                    <th scope="col">Owner Name</th>
                    <th scope="col">Item Location</th>
                    <th scope="col">Due Date</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($borrowedItems as $book)
                <tr>
                    <!-- Thumbnail -->
                    <td>
                        <a href="{{ $book['book']['cover_page'] }}" target="_blank">
                            <img src="{{ $book['book']['cover_page'] }}" alt="Cover Page" style="width: 100px; height: 100px; object-fit: cover;">
                        </a>
                    </td>

                    <!-- Latest Image -->
                    <td>
                        @if (!empty($book['book']['latest_image']))
                        <a href="{{ $book['book']['latest_image'] }}" target="_blank">
                            <img src="{{ $book['book']['latest_image'] }}" alt="Latest Image" style="width: 100px; height: 100px; object-fit: cover;">
                        </a>
                        @else
                        <span>N/A</span>
                        @endif
                    </td>

                    <!-- Item Details -->
                    <td>{{ $book['book']['name'] }}</td>
                    <td>${{ number_format($book['book']['rent_price'], 2) }}</td>
                    <td>{{ $book['book']['category']['name'] }}</td>

                    <!-- Owner -->
                    <td class="text-center">
                        {{ $book['book']['user']['name'] ?? 'Unknown' }}
                    </td>

                    <!-- Item Location -->
                    <td class="text-center">
                        {{ isValidJson($book['book']['locations'])
                            ? implode(', ', json_decode($book['book']['locations']))
                            : $book['book']['locations'] ?? 'N/A'
                        }}
                    </td>

                    <!-- Due Date -->
                    <td class="text-center">{{ $book['due_date'] ?? 'N/A' }}</td>

                    <!-- Actions -->
                    <td class="text-center">
                        @if ($book['state'] == 'return-request')
                        <span class="badge badge-warning px-4 py-2">Return Pending</span>
                        @else
                        <a href="javascript:void(0)" onclick="returnBook({{ $book['id'] }},{{ $book['book']['id'] }},this)" class="btn btn-danger btn-sm">
                            Return
                        </a>
                        @endif
                        <a href="{{ route('reviews', $book['book']['id']) }}" class="btn btn-warning btn-sm mt-2">
                            Reviews
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="alert alert-info text-center">
        No borrowed items found.
    </div>
    @endif
</div>

@include('group.scripts')
