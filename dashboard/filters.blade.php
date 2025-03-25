<div id="filterSideBar" class="sidepanel">
  <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
    <form action="<?=route('dashboard')?>" method="GET">
    Group:
        <select name="group_filter" id="group_filter" class="form-control">
        <option value="">Select Group</option>
        <?php foreach ($data["groups"] as $group) { ?>
            <option value="<?= $group["id"] ?>" <?= (isset($data["group_filter"]) && $data["group_filter"] == $group["id"] ? "selected" : "") ?>><?= $group["title"] ?></option>
        <?php } ?>
        </select>
    Category:
        <select name="type_filter" id="type_filter" class="form-control">
        <option value="">Select Category</option>
        <?php foreach ($data["types"] as $type) { ?>
            <option value="<?= $type["id"] ?>" <?= (isset($data["type_filter"]) && $data["type_filter"] == $type["id"] ? "selected" : "") ?>><?= $type["name"] ?></option>
        <?php } ?>
        </select>
        <br/>
        <input type="hidden" name="view_type" value="<?=$data['view_type']?>"/>
        <input type="hidden" name="selected_alphabet" value="<?=$data['selected_alphabet']?>"/>
        <input type="submit" class="btn-primary" value="Apply Filter"/>
        <input type="reset" class="btn-secondary" value="Clear"/>
    </form>
</div>