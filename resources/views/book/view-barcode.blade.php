<div class="item_description_barcode">
    <p>Scan QR code</p>
    <?php if (isset($data['barcode_url'])){ ?>
        <img src="<?=$data['barcode_url']?>"/>
    <?php } ?>
    
    <?php if (isset($data['image'])){ ?>
        <?=$data['image']?>
    <?php } ?>
    
</div>
<a href="<?=route('make-barcode')?>">Go Back to Make</a>