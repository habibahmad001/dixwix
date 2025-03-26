<div class="inner_content">
    @if (count($borrowedItems) > 0)
    <div class="table-group-details table-responsive group-books-list">
        <table id="items_table_group" class="table table-striped table-rounded">
            <thead>
                <tr>
                    <th scope="col">Thumbnail</th>
                    <th scope="col">Latest Image</th>
                    <th scope="col">Name</th>
                    <th scope="col">Rental Price</th>
                    <th scope="col">Category</th>
                    <th scope="col">Name</th>
                    <th scope="col">Location</th>
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
                        <a href="javascript:void(0)" onclick="returnBook({{ $book['id'] }},{{ $book['book']['id'] }},this)" title="Return" class="btn btn-danger btn-sm">
                            <i class="fa fa-power-off" aria-hidden="true"></i>
                        </a>
                        <a href="javascript:void(0)" id="renewID-{!! $book['id'] !!}" title="Renew" data-book_id="{!! $book['id'] !!}" class="btn btn-info btn-sm renewID">
                            <i class="fa fa-bell" aria-hidden="true"></i>
                        </a>
                        @endif
                        <a href="{{ route('reviews', $book['book']['id']) }}" title="Reviews" class="btn btn-warning btn-sm">
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


<div class="modal" id="dixwix_book_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body py-4" id="modal_body" style="overflow-y: scroll; max-height: 450px;">
                <form id="book-status-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="book_id" id="book_id" value="" />
                    <div class="col">
                        <label for="book_duration">Select Duration</label>
                        <select class="form-control" name="duration" id="book_duration">
                            @foreach ($data['loanRules'] as $rule)
                                <option value="{{ $rule['id'] }}">{{ $rule['title'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col mt-4">
                        <button class="btn btn-secondary" id="reserve-book-btn">Reserve</button>
                        <button type="button" id="close-modal-reserve-modal" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('group.scripts')
