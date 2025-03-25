<style>
    a:hover, a:focus{
        text-decoration: none;
    }
    h2 .item_title a:hover, h2 .item_title a:focus{
        text-decoration: none;
        text-underline: none;
        color: var(--green-dark-01);
    }

    .item_title a {
        color: var(--green-dark-01);
    }
</style>

<div class="inner_content">
    <h3>{{ $data['title'] }}</h3>
    <form method="GET">
        <div class="row my-3">
            <div class="col-md-4">
                <select class="form-control" name="group">
                    <option value="">Select group</option>
                    @if(!empty($data['groups']))
                    @foreach ($data['groups'] as $group)
                    <option value="{{ $group['title'] }}" {{ request('group') == $group['title'] ? 'selected' : '' }}>{{ $group['title'] }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-control" name="category">
                    <option value="">Select category</option>
                    @if(!empty($data['categories']))
                    @foreach ($data['categories'] as $category)
                    <option value="{{ $category['name'] }}" {{ request('category') == $category['name'] ? 'selected' : '' }}>{{ $category['name'] }}</option>
                    @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-4 d-flex justify-content-end">
                <button type="button" class="btn btn-primary mr-1 text-nowrap" id="bulk-qrcode-btn">Print Bulk QR Codes</button>
                <button type="button" class="btn btn-primary mr-1 text-nowrap" id="bulk-edit-btn">Bulk Edit</button>
                <button type="submit" class="dark-btn btn link_with_img text-nowrap">Filter items</button>
            </div>
        </div>
    </form>
    @if (isset($data['books']) && count($data['books'])>0)

    @foreach ($data["books"] as $ky => $book)
    <div class="items_list">
        <div class="post_item d-flex flex-column flex-md-row">
            <div class="post_image">
                <img src="{{ $book["cover_page"] }}" alt="Book Image">
            </div>
            <div class="item_details">
                <div class="item_summary_wrapper d-flex flex-column flex-md-row">
                    <div class="item_summary">
                        <h2 class="item_title"><a href="{{ route("show-item",["id"=>$book["id"]]) }}"> {{ $book["name"] }}</a></h2>
                        <p class="item_subitile">{{ $book["writers"] }}</p>
                        <div class="item_meta">
                            <span class="item_meta_tag">{{ date('Y',strtotime($book["added_date"])) }}</span>
                            <span class="item_meta_details">{{ !empty($book["pages"]) ? $book["pages"] . " Pages " : "" }} {{ !empty($book["journal_name"]) ? ", (" . $book["journal_name"] . ")" : "" }}</span>
                        </div>
                        <div class="item_meta">
                            <span class="item_meta_tag">Category:</span>

                          <span class="item_meta_details">
                            {{ $book->category ? $book->category['name'] : 'Category not available' }}
                          </span>

                      </div>
                        <div class="item_meta_2">
                            @if (!empty($book["ean_isbn_no"]))
                            <div class="item_meta">
                                <span class="item_meta_tag">EAN/ISBN13:</span>
                                <span class="item_meta_details">{{ $book["ean_isbn_no"] }}</span>
                            </div>
                            @endif
                            @if (!empty($book["upc_isbn_no"]))
                            <div class="item_meta">
                                <span class="item_meta_tag">UPC / ISBN10:</span>
                                <span class="item_meta_details">{{ $book["upc_isbn_no"] }}</span>
                            </div>
                            @endif
                        </div>
                        <div class="item_meta">
                            <span class="item_meta_tag">Added:</span>
                            <span class="item_meta_details">{{ $book["added_date"] }}</span>
                        </div>
                        <div class="item_meta_2">
                            <div class="item_meta">
                                <span class="item_meta_tag">Copies:</span>
                                <span class="item_meta_details">{{ count($book['availableentries']) }}</span>
                            </div>
                            <div class="item_meta">
                                <span class="item_meta_tag">Price:</span>
                                <span class="item_meta_details">$ {{ $book['price'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="item_summary_actions">
                        <div class="form-check" style="background: #094042;border-radius: 10px;color: white;padding: 6px 8px 6px 30px !important;">
                            <input type="checkbox" id="bulk-edit{{ $book['id'] }}" class="form-check-input select-item" value="{{$book['id'] }}" data-id="{{$book['id'] }}">
                            <label for="bulk-edit{{ $book['id'] }}" class="custom-checkbox-label text-nowrap mb-0">Select Bulk</label>
                        </div>
                        <input class="dark-btn btn link_with_img" type="button" value="Print QR Codes" onclick="printQRCodes('{{route('get-qr-codes')}}',{{$book['id'] }})" />
                        {!! $book["ref_type"] == "amazon" ? "<img src=\"assets/media/amazon.png\">" : "" !!}
                        @if ((Auth::user()->hasRole("admin")) || $book["created_by"] == Auth::user()->id)
                        <a class="dark-btn btn link_with_img" href="{{ route('edit-book', [$book["id"]]) }}">
                            <img src="assets/media/edit.png"> Edit
                        </a>
                        <a class="dark-btn btn link_with_img" href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to delete item?','question',function(){deleteItem('{{$book['id']}}','{{route('delete-item')}}')})">
                            <img src="assets/media/delete.png"> Delete
                        </a>
                        @endif
                    </div>
                </div>
                <div class="item_description_wrapper">
                    <div class="item_description">
                        <h4>Descriptions:</h4>
                        <p>{{ $book["description"] }}</p>
                    </div>
                    @if (isset($book['barcode_url']) && !empty($book['barcode_url']))
                    <div class="item_description_barcode">
                        <p>Scan QR code</p>
                        <img src="{{ url($book["barcode_url"]) }}" alt="QR Code" />
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @else
    <div class="items_list">
        <div class="item search-result">
            <div class="text404">
                <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">No Item Exist</p>
            </div>
        </div>
    </div>
    @endif
</div>
@include('book.modal')

<script>
    $(document).ready(function() {
        $('#bulk-qrcode-btn').on('click', function() {
            const selectedItems = [];
            const url_val = '{{ route('get-qr-codes') }}'
            $('.select-item:checked').each(function() {
                selectedItems.push($(this).data('id'));
            });

            if (selectedItems.length === 0) {
                Swal.fire({
                    icon: 'error'
                    , title: 'Item not selected'
                    , text: 'Please select at least one item\'s "Select Bulk" option to edit".'
                    , confirmButtonText: 'OK'
                });
                return;
            } else {
                $.ajax({
                    type: 'GET'
                    , url: url_val
                    , data: {
                        "itemIds": selectedItems
                        , "_token": "<?=csrf_token()?>"
                    , }
                    , success: function(result) {
                        resultJson = JSON.parse(result);
                        if (resultJson.success == true) {
                            jQuery("#modal_body").html(resultJson.data);
                            $('#search_user').hide();
                            $('#email_to_invite').hide();
                            $('#invite_button').hide();
                            jQuery("#modal_title").text("QR Codes");
                            jQuery("#modal_print_btn").show();
                            jQuery('#dixwix_modal').modal('show');
                        }
                    }
                });
            }
        });

        $('#bulk-edit-btn').on('click', function() {
            const selectedItems = [];
            $('.select-item:checked').each(function() {
                selectedItems.push($(this).data('id'));
            });

            if (selectedItems.length === 0) {
                Swal.fire({
                    icon: 'error'
                    , title: 'Item not selected'
                    , text: 'Please select at least one item\'s "Select Bulk" option to edit".'
                    , confirmButtonText: 'OK'
                });
                return;
            } else {
                window.location.href = '/bulk-items-edit?ids=' + selectedItems.join(',');
            }
        });
    });

</script>
