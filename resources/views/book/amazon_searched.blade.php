<h3>Search Results</h3>

@forelse($dbItems as $item)
<div class="items_list">
    <div class="post_item d-flex flex-column flex-md-row">
        <div class="post_image">
            <img src="{{ $item->cover_page }}" alt="Book Image">
        </div>
        <div class="item_details">
            <div class="item_summary_wrapper d-flex flex-column flex-md-row">
                <div class="item_summary">
                    <h2 class="item_title">{{ $item->name }}</h2>
                    <p class="item_subitile">{{ $item->writers }}</p>
                    <div class="item_meta">
                        <span class="item_meta_tag">{{ date('Y',strtotime($item->added_date)) }}</span>
                        <span class="item_meta_details">
                            {{ isset($item->pages) && !empty($item->pages) ? "{$item->pages} Pages " : "" }}
                            {{ isset($item->journal_name) && !empty($item->journal_name) ? ", ({$item->journal_name})" : "" }}
                        </span>
                    </div>
                    <div class="item_meta">
                        <span class="item_meta_tag">Category:</span>
                        <span class="item_meta_details">{{ $item?->category?->name }}</span>
                    </div>
                    <div class="item_meta">
                        <span class="item_meta_tag">Price:</span>
                        <span class="item_meta_details">$ {{ $item?->price }}</span>
                    </div>
                    <div class="item_meta">
                        <span class="item_meta_tag">User Name:</span>
                        <span class="item_meta_details">{{ $item?->user?->name }}</span>
                    </div>
                </div>
            </div>
            <div class="form-group mt-3">
                <button type="button" class="btn lastbtn submit_btn text-nowrap px-5 add-to-group" data-item-id="{{ $item->id }}">
                    Add to Group
                </button>
            </div>
        </div>
    </div>
</div>
<div class="divider" style="height: 2px;background: #094042;"></div>
@empty
<div class="item search-result">
    <div class="text404">
        <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">Items not found in the library</p>
    </div>
</div>
@endforelse

