@if(!empty($data['books']) && count($data['books']) > 0)
@foreach ($data["books"] as $ky => $book)
<div class="items_list">
    <div class="post_item">
        <div class="post_image">
            <img src="{{ $book["cover_page"] }}" alt="Book Image">
        </div>
        <div class="item_details">
            <div class="item_summary_wrapper">
                <div class="item_summary">
                    <h2 class="item_title"><a href="{{ route('show-item',["id"=>$book->id]) }}">{{ $book["name"] }}</a></h2>
                    <p class="item_subitile">{{ $book["writers"] }}</p>
                    <div class="item_meta">
                        <span class="item_meta_tag">{{ $book["year"] }}</span>
                        <span class="item_meta_details">{{ !empty($book["pages"]) ? $book["pages"] . " Pages " : "" }} {{ !empty($book["journal_name"]) ? ", (" . $book["journal_name"] . ")" : "" }}</span>
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
                    <div class="item_meta">
                        <span class="item_meta_tag">Group:</span>
                        <span class="item_meta_details">{{ $book->group["title"]." (".$book->group["state"]." - ".$book->group["zip_code"].")" }}</span>
                    </div>
                    <div class="item_meta">
                        <span class="item_meta_tag">Category:</span>
                        <span class="item_meta_details">{{ $book->category["name"] }}</span>
                    </div>
                </div>
                <div class="item_summary_actions">
                    {!! $book["ref_type"] == "amazon" ? "<img src=\"assets/media/amazon.png\">" : "" !!}
                    <a class="dark-btn btn link_with_img" href="{{ route("show-item",["id"=>$book["id"]]) }}"><img src="assets/media/eye-outline.png"> View</a>

                    @if ($book["created_by"] == Auth::user()->id)
                    <a class="dark-btn btn link_with_img" href="{{ route('edit-book', [$book["id"]]) }}">
                        <img src="assets/media/edit.png"> Edit
                    </a>
                    <a class="dark-btn btn link_with_img" href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to delete item?','question',function(){deleteItem('{{ $book['id'] }}','{{ route('delete-item') }}')})">
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
                @if (!empty($book['barcode_url']))
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
@endif
