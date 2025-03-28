<?php if(isset($retdata)){extract($retdata);} ?>
<br/>
<div class="container justify-content-center">
    <div class="row">
        <div class="col-2">
            <?php if ($book["cover_page"] != "") { ?>
                <img class="cover_img" src="<?= $book["cover_page"] ?>" alt="Book Image">
            <?php } else { ?>
                <img class="cover_img" src="" alt="Book Image">
            <?php } ?>
        </div>
        <div class="col-10">
            <div class="row">
                <div class="col-7">
                    <h2 class="item_title"><img src="<?=url("assets/media/items.png")?>" style="width:20px"/> DixWix</h2> <span class="item-type-book"><?=$book["category"]["name"]?></span>
                    <?= ($book["ref_type"] == "amazon" ? '<img src="assets/media/amazon.png">' : '') ?>
                    <h3 class="lead main-heading"><?=$book["name"]?></h3>
                </div>
                    <div class="col-5">
                    <?php if ($book["created_by"] == Auth::user()->id || Auth::user()->hasRole('admin')) { ?>
                        <div class="item_summary_actions">
                            <a class="dark-btn btn link_with_img" href="<?= route('edit-book', [$book["id"]]) ?>">
                                <img src="<?=url("assets/media/edit.png")?>"> Edit
                            </a>
                            <?php /*<a class="dark-btn btn link_with_img" href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to delete item?','question',function(){deleteItem('<?= $book['id'] ?>','<?= route('delete-item') ?>')})">
                                <img src="<?=url("assets/media/delete.png")?>"> Delete
                            </a>*/ ?>

                            <a class="dark-btn btn link_with_img" href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to delete item?','question',function(){deleteItemCr('<?= $book['id'] ?>','<?= route('delete-item') ?>')})">
                                <img src="<?=url('assets/media/delete.png')?>"> Delete
                            </a>

                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="item_meta">
                <span class="item_meta_tag"><?= $book["year"] ?></span>
                <span class="item_meta_details"><?= (isset($book["pages"]) && !empty($book["pages"]) ? $book["pages"] . " Pages " : "") ?> <?= (isset($book["journal_name"]) && !empty($book["journal_name"]) ? ", (" . $book["journal_name"] . ")" : "") ?></span>
            </div>
            <div class="item_meta_2">
                <?php if (isset($book["ean_isbn_no"]) && !empty($book["ean_isbn_no"])) { ?>
                    <div class="item_meta">
                        <span class="item_meta_tag">EAN/ISBN13:</span>
                        <span class="item_meta_details"><?= $book["ean_isbn_no"] ?></span>
                    </div>
                <?php } ?>
                <?php if (isset($book["upc_isbn_no"]) && !empty($book["upc_isbn_no"])) { ?>
                    <div class="item_meta">
                        <span class="item_meta_tag">UPC / ISBN10:</span>
                        <span class="item_meta_details"><?= $book["upc_isbn_no"] ?></span>
                    </div>
                <?php } ?>
            </div>
            <div class="item_meta">
                <span class="item_meta_tag">Added:</span>
                <span class="item_meta_details"><?= $book["added_date"] ?></span>
            </div>
            <div class="item_meta_2">
                <div class="item_meta">
                    <span class="item_meta_tag">Copies:</span>
                    <span class="item_meta_details"><?=count($book['availableentries'])?></span>
                </div>
                <div class="item_meta">
                    <span class="item_meta_tag">Available For:</span>
                    <span class="item_meta_details"><?= $book["sale_or_rent"] ?></span>
                </div>
                <div class="item_meta">
                    <span class="item_meta_tag">Price:</span>
                    <span class="item_meta_details">$ <?=$book['price']?></span>
                </div>
            </div>
            <div class="item_meta">
                <span class="item_meta_tag">Group:</span>
                <span class="item_meta_details"><?= $book["group"]["title"]." (".$book["group"]["state"]." - ".$book["group"]["zip_code"].")" ?></span>
            </div>
            <hr>
            <div class="item_description_wrapper">
                <div class="item_description">
                    <h4>Descriptions:</h4>
                    <p>
                        <?= $book["description"] ?>
                    </p>
                </div>
                <?php if (isset($book['barcode_url']) && !empty($book['barcode_url'])) { ?>
                    <div class="item_description_barcode">
                        <p>Scan QR code</p>
                        <!-- <img src="<?= url($book['barcode_url']) ?>" alt="QR Code"> -->
                      <img style="width: 80px; margin-top:15px;
    height: 70px;
    border: 3px solid #094042;
    padding: 5px;
    border-radius: 10px;
    margin-right: 10px;
    box-shadow: 10px 10px 5px 0px rgba(0, 0, 0, 0.75);
    -webkit-box-shadow: 10px 10px 5px 0px rgba(0, 0, 0, 0.75);
    -moz-box-shadow: 10px 10px 5px 0px rgba(0, 0, 0, 0.75);" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(url('/show-item/' . $book['id'])) }}" alt="QR Code for Group">
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


<div class="row col-md-11 mx-auto mt-5" style="display: flex; flex-direction: column; gap: 20px;">

    <div class="mb-4 d-flex justify-content-between">
        @if($reviews && $reviews->isNotEmpty() && ((auth()->user()->hasRole('admin') || auth()->id() == $book->created_by) || auth()->id() == $book['group']->created_by || (!empty($status) && $status->activated && $status->member_role == 'admin')))
            <div>
                <form action="{{ route('reviews.deleteAll') }}" method="POST" style="display: inline;">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $book->id }}" />
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding: 10px 20px; border-radius: 5px;">
                        Delete All Reviews
                    </button>
                </form>
            </div>
        @endif
    </div>

    @forelse($reviews as $review)
        <div class="review-item" style="display: flex; align-items: flex-start; gap: 15px;">
            <div class="review-content" style="flex-grow: 1; background-color: {{ $review->approved ? '#E0E0E0' : '#D9FBE4' }}; border-radius: 10px; padding: 20px;">
                <h5 style="margin: 0 0 10px; font-family: Poppins; font-weight: 600; font-size: 16px; color: #094042;">
                    {{ $review->title ?? 'Review' }}
                </h5>
                <hr style="border: 1px solid white; margin-bottom: 10px;">
                @if(!empty($review->user))
                    <div class="user-info" style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <img src="{{ !empty($review->user->profile_pic) ? asset('storage/'.$review->user->profile_pic) : asset('assets/media/userimg.png') }}" alt="User" style="width: 48px; border-radius: 50%;">
                        <p style="margin: 0; font-size: 15px; font-weight: 600; color: #094042;">{{ $review->user->name }}</p>
                    </div>
                @endif
                <p style="margin: 0 0 10px; font-size: 14px; color: #094042;">
                    {!! $review->review !!}
                </p>
                <p style="margin: 0 0 10px; font-size: 14px; color: #094042;">
                    <strong>Rating:</strong> {{ $review->rating }}/5
                </p>
                <div style="font-size: 13px; color: #094042; margin-bottom: 10px;">
                    <span>Date: {{ \Carbon\Carbon::parse($review->created_at)->format('Y-m-d') }}</span> |
                    <span>Time: {{ \Carbon\Carbon::parse($review->created_at)->format('h:i') }}</span>
                </div>
                <div class="text-right">
                    @if(auth()->user()->hasRole('admin') || auth()->id() == $book->created_by || auth()->id() == $book['group']->created_by || auth()->id() == $review->user_id || (!empty($status) && $status->activated && $status->member_role == 'admin'))
                        <form action="{{ route('reviews.delete', $review->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-warning">
                                Delete Review
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <h4>No reviews found</h4>
    @endforelse

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if($canAddReview)
            <form method="POST" action="{{ route('add-review') }}">
                @csrf
                <input type="hidden" name="item_id" value="{{ $book->id }}" />
                <div class="form-group">
                    <label for="rating">Rating</label>
                    <select name="rating" id="rating" class="form-control mb-2" required>
                        <option value="" disabled selected>Select a rating</option>
                        <option value="5">5 - Excellent</option>
                        <option value="4">4 - Good</option>
                        <option value="3">3 - Average</option>
                        <option value="2">2 - Poor</option>
                        <option value="1">1 - Terrible</option>
                    </select>
                    @error('rating')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="review">Review</label>
                    <textarea id="review" name="review" class="form-control mb-2" placeholder="Add a review..."></textarea>
                    @error('review')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-6">
                        <strong>Rating:</strong> {{ number_format($averageRating, 1) }}/5</p>
                    </div>
                    <div class="col-6">
                        <button class="btn lastbtn submit_btn" type="submit" style="float: right;">Post Review</button>
                    </div>
                </div>

            </form>
        @endif

    </div>
    </div>
</div>

<script>
    function deleteItemCr(item_id, url_val) {
        jQuery.ajax({
            type: 'DELETE',
            url: url_val,
            data: {
                "_token": "<?= csrf_token() ?>",
                "item_id": item_id
            },
            success: function(result) {
                if (result.success) {
                    Swal.fire({
                        icon: "success"
                        , title: "Success"
                        , text: result.message
                        , confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = '{{ route("my-items") }}';
                    });
                }else{
                    Swal.fire({
                        icon: "error"
                        , title: "Oops..."
                        , text: result.message
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: "error"
                    , title: "Oops..."
                    , text: "An error occurred while deleting the item."
                });
            }
        });
    }
</script>
