<?php foreach ($group['books'] as $book) { ?>
    <div class="items_list">
        <div class="row">
            <div class="col-2">
                <?php if ($book["cover_page"] != "") { ?>
                    <img class="im-wd" src="<?= $book["cover_page"] ?>" alt="Book Image">
                <?php } else { ?>
                    <img class="im-wd" src="" alt="Book Image">
                <?php } ?>
            </div>
            <div class="col-2">
                <div class="lead main-heading"><a
                        href="<?= route("show-item", ["id" => $book["id"]]) ?>"> <?= $book["name"] ?></a></div>
                <div class="description">
                    <p>
                        <?= $book["description"] ?>
                    </p>
                </div>
            </div>
            <div class="col-8">
                <span class="item-type-book ">Book</span>

                <div class="item-author"><?= $book["writers"] ?></div>
                <div>

                    <div class="item_meta">
                        <span class="item_meta_tag">EAN / ISBN13:</span>
                        <?php if ($book["ean_isbn_no"] != "") { ?>
                            <span class="item_meta_details"><?= $book["ean_isbn_no"] ?></span>
                        <?php } ?>
                    </div>

                    <?php if ($book["upc_isbn_no"] != "") { ?>
                        <div class="item_meta">
                            <span class="item_meta_tag">UPC / ISBN10:</span>
                            <span class="item_meta_details"><?= $book["upc_isbn_no"] ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>
    <hr>
<?php } ?>
