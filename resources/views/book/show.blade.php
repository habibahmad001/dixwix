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

<br />
<br />
    <div class="row">
        <div class="col-12">
            @forelse($data['posts'] as $post)
                <div class="post-card mb-4 p-3 border rounded">
                    <p class="text-muted">
                        Posted by: <strong>{{ $post['user']['name'] }}</strong> on {{ date('Y-m-d H:i:s', strtotime($post['created_at'])) }}
                    </p>
                    <p>{{ $post['title'] }}</p>

{{--                    @if(auth()->user()->hasRole('admin') || auth()->id() == $data['created_by'] || auth()->id() == $post['user_id'])--}}
{{--                        <button class="btn btn-danger btn-sm delete-post" data-id="{{ $post['id'] }}">Delete Post</button>--}}
{{--                    @endif--}}
                </div>
                @endforeach
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