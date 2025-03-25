<div class="inner_content">
    <div class="heading">
        <h2><?=$data["title"]?></h2>
    </div>
    <?php
    if (count($data["books"]) == 0){ ?>
          <div class="item search-result">
            <div class="text404">
              <img src="<?=url('assets/media/error 1.png')?>" alt="Book Image">
              <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">Item not found in library</p>
            </div>
        </div>
    <?php } else {
        foreach ($data["books"] as $ky => $book) { ?>
            <div class="items_list">
                <div class="post_item">
                    <div class="post_image">
                        <?php if ($book["cover_page"] != "") {
                        ?>
                            <img src="<?= $book["cover_page"] ?>" alt="Book Image">
                        <?php } else { ?>
                            <img src="" alt="Book Image">
                        <?php } ?>
                    </div>
                    <div class="item_details">
                        <div class="item_summary_wrapper">
                            <div class="item_summary">
                                <a href="<?=route("show-item",["id"=>$book["id"]])?>"><h2 class="item_title"><?= $book["name"] ?></h2></a>
                                <p class="item_subitile"><?= $book["writers"] ?></p>
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
                                <div class="item_meta">
                                    <span class="item_meta_tag">Available For:</span>
                                    <span class="item_meta_details"><?= $book["sale_or_rent"] ?></span>
                                </div>
                                <div class="item_meta_2">
                                    <div class="item_meta">
                                        <span class="item_meta_tag">Copies:</span>
                                        <span class="item_meta_details"><?=count($book['availableentries'])?></span>
                                    </div>
                                    <div class="item_meta">
                                        <span class="item_meta_tag">Price:</span>
                                        <span class="item_meta_details">$ <?=$book['price']?></span>
                                    </div>
                                </div>
                                <div class="item_meta">
                                    <span class="item_meta_tag">Group:</span>
                                    <span class="item_meta_details"><?= $book->group["title"]." (".$book->group["state"]." - ".$book->group["zip_code"].")" ?></span>
                                </div>
                                <div class="item_meta">
                                    <span class="item_meta_tag">Category:</span>
                                    <span class="item_meta_details"><?= $book->category["name"] ?></span>
                                </div>

                            </div>
                            <div class="item_summary_actions">
                                <?= ($book["ref_type"] == "amazon" ? "<img src=\"assets/media/amazon.png\">" : "") ?>

                                <?php if ($book["created_by"] == Auth::user()->id) { ?>
                                    <a class="dark-btn btn link_with_img" href="<?= route('edit-book', [$book["id"]]) ?>">
                                        <img src="assets/media/edit.png"> Edit
                                    </a>
                                    <a class="dark-btn btn link_with_img" href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to delete item?','question',function(){deleteItem('<?=$book['id']?>','<?=route('delete-item')?>')})">
                                        <img src="assets/media/delete.png"> Delete
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
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
                                    <img src="<?= $book['barcode_url'] ?>" alt="Barcode">
                                </div>
                            <?php } ?>
                        </div>
                        <?php if($book['created_by'] != Auth::user()->id && count($book['availableentries']) > 0){?>
                        <div class="btonlin">
                            <!-- <a id="edit_item" href="#" class="btn link_with_img"><img src="assets/media/group1.png"> Add to group</a> -->
                            <a href="#" class="dark-btn btn link_with_img"><img src="assets/media/list3.png">Reserve item</a>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            </div>

        <?php } ?>
  <div class="heading">
  <h2><?=$data["search_group"]?></h2>
    </div>
        @forelse ($data["groups"] as $group)
        <div class="row">
            <div class="MultiCarousel" data-items="1,3,5,6,1" data-slide="1" id="MultiCarousel" data-interval="1000">
            <div class="MultiCarousel-inner">
            <div class="item">
                <div class="pad15">
                    <div class="innerheader">
                        <h3 class="lead main-heading"><?=$group['title']?></h3>

                        <div class="post_image">
                            <a href="<?=route('show-group',["id"=>$group->id])?>"><img src="assets/media/eye-outline.png" alt="View Group"></a>
                            <?php if(Auth::user()->hasRole("admin") || $group["created_by"] == Auth::user()->id){ ?>
                                <a href="<?=route('edit-group',["id"=>$group["id"]])?>" title="Edit Group"><img src="assets/media/edit-orange.png" alt="Edit Group"></a>
                            <?php } ?>
                        </div>
                    </div>



                    <div class="divider">
                        <hr>
                    </div>
                    <div class="carousel-date">Created: <?=date('M/d/Y',strtotime($group['created_at']))?></div>
                    <!-- <div class="member">Members: 12</div> -->
                    <div class="imagesection">
                    <?php if($group["group_picture"]!=""){ ?>
                    <img class="im-wd" src="<?= asset('storage/'.$group["group_picture"])?>" alt="Group Image">
                    <?php }
                    else{ ?>
                    <img src="" alt="Group Image">
                    <?php } ?>
                    <?php if(!empty($group["to_be_deleted_at"])) {?>
                        <span class="item-type-book">Marked Deleted</span>
                    <?php } else { ?>
                        <?php if($group["created_by"] == Auth::user()->id){ ?>
                            <a href="javascript:void(0)" data-group_id="{{ $group['id'] }}" data-group_type_id="{{ $group['group_type_id'] }}" id="getMembersToAdd" onclick="getMembersToAdd('<?=route('get-members-to-add',['group_id'=>$group['id'], 'group_type_id'=> $group['group_type_id']])?>','<?=$group['group_type_id']?>')" class="dark-btn btn link_with_img">
                                <img src="assets/media/add-circle-outline.png"> Invite
                            </a>
                        <?php } ?>
                    <?php } ?>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="item search-result">
            <div class="text404">
              <img src="<?=url('assets/media/error 1.png')?>" alt="Book Image">
              <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">Group not found in library</p>
            </div>
        </div>
        @endforelse

</div>
<script>

    $(document).on('click', '#getMembersToAdd', function() {
       let group_id = $(this).data('group_id');
       let group_type_id = $(this).data('group_type_id');

       // Set the values into the modal's hidden fields or data attributes
       $('#group_id_modal').val(group_id);
       $('#group_type_id_modal').val(group_type_id);

   });

</script>

   @include('book.modal')
