<?php if(isset($retdata)){extract($retdata);} ?>
<br/>
<div class="container justify-content-center">
    <div class="row">
        <div class="col-2">
            <?php if ($group["group_picture"] != "") { ?>
                <img class="cover_img" src="<?= Storage::disk('local')->url($group["group_picture"]) ?>" alt="group Image">
            <?php } else { ?>
                <img class="cover_img" src="" alt="group Image">
            <?php } ?>
        </div>
        <div class="col-10">
            <div class="row">
                <div class="col-7">
                    <h2 class="item_title"><img src="<?=url("assets/media/items.png")?>" style="width:20px"/> DixWix</h2>
                    <h3 class="lead main-heading"><?=$group["title"]?></h3>
                </div>
                    <div class="col-5">
                    <?php if ($group["created_by"] == Auth::user()->id) { ?>
                        <div class="item_summary_actions">
                            <a class="dark-btn btn link_with_img" href="<?= route('edit-group', [$group["id"]]) ?>">
                                <img src="<?=url("assets/media/edit.png")?>"> Edit
                            </a>
                        </div>
                    <?php } ?>
                    </div>
            </div>

            <div class="item_meta">
                <span class="item_meta_tag"><?= $group["state"] ?></span>
                <span class="item_meta_details"><?= $group["zip_code"] ?></span>
            </div>

            <div class="item_meta">
                <span class="item_meta_tag">Members</span>
                <span class="item_meta_details"><?= count($group["groupmembers"]) ?></span>
            </div>

            <div class="item_meta">
                <span class="item_meta_tag">Books</span>
                <span class="item_meta_details"><?= count($group["books"]) ?></span>
            </div>

            <hr>
            <div class="item_description_wrapper">
                <div class="item_description">
                    <h4>Descriptions:</h4>
                    <p>
                        <?= $group["description"] ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>