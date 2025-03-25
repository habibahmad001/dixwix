<script>
    var redirect_url = '<?= route('dashboard') ?>';
</script>
<?php if (isset($retdata)) {
    extract($retdata);
} ?>
<div class="container">
    <div class="heading">
        <h2><?= $data['title'] ?></h2>
    </div>
    <div class="divider">
        <hr>
    </div>

    <?php if(isset($group_limit_reached)) { ?>
    <div class="item search-result">
        <div class="text404">
            <img src="<?= url('assets/media/error 1.png') ?>" alt="No Group Joined">
            <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">Group Creation Limit
                Reached Upgrade <a href="<?= route('membership') ?>">Membership</a> to create further groups</p>
        </div>
    </div>
    <?php } else { ?>
    <form class="main-form" name="add-group-form" enctype="multipart/form-data" id="add-group-form" method="post"
        action="{{ route('store-group') }}">
        @csrf
        <input type="hidden" name="mode" value="<?= $mode ?>">
        <input type="hidden" name="group_id" value="<?= isset($group_id) ? $group_id : '' ?>">

        <!-- <div class="form-group">
                <h3 class="lead main-heading">Select Group Type</h3>
                <select class="form-control" id="group_type_id" name="group[group_type_id]">
                    <?php /* foreach ($data["group_types"] as $group_type) { ?> ?> ?> ?> ?> ?>
                        <option value="<?= $group_type['id'] ?>" <?= isset($group) ? ($group['group_type_id'] === $group_type['id'] ? 'selected' : '') : '' ?>><?= $group_type['name'] ?></option>
                    <?php } */ ?>
                </select>
                @if (isset($errs['group_type_id']))
<div class="error_msg">*<?php /* echo $errs['group_type_id'] */ ?></div>
@endif
                <p>Choose the group type you're adding groups to.</p>
            </div> -->
        <div class="form-group">
            <h3 class="lead main-heading">Title</h3>
            <input type="text" class="form-control" id="title" name="group[title]"
                value="<?= isset($group) ? $group['title'] : '' ?>" placeholder="Group Title">
            @if (isset($errs['title']))
                <div class="error_msg">*<?= $errs['title'] ?></div>
            @endif
        </div>
        <div class="form-group textarea">
            <h3 class="lead main-heading">Description</h3>
            <textarea type="textarea" class="form-control" id="description" name="group[description]"
                placeholder="Describe your group here"><?= isset($group) ? $group['description'] : '' ?></textarea>
            @if (isset($errs['description']))
                <div class="error_msg">*<?= $errs['description'] ?></div>
            @endif
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col">
                    <h3 class="lead main-heading">State</h3>
                    <input type="text" class="form-control" id="state" name="group[state]"
                        value="<?= isset($group) ? $group['state'] : '' ?>" placeholder="Enter State">
                    @if (isset($errs['state']))
                        <div class="error_msg">*<?= $errs['state'] ?></div>
                    @endif
                </div>
                <div class="col">
                    <h3 class="lead main-heading">Zip Code</h3>
                    <input type="text" class="form-control" id="zip_code" name="group[zip_code]"
                        value="<?= isset($group) ? $group['zip_code'] : '' ?>" placeholder="Enter Zipcode">
                    @if (isset($errs['zip_code']))
                        <div class="error_msg">*<?= $errs['zip_code'] ?></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col">
                    <h3 class="lead main-heading">Country</h3>
                    {{-- <input type="text" class="form-control" id="state" name="group[country]"
                        value="<?= isset($group) ? $group['country'] : '' ?>" placeholder="Enter Country"> --}}
                    <select class="form-control" name="group[country]">
                        <option value="">Please Select Country</option>
                        @foreach ($data['countries'] as $country)
                            <option value="{{ $country->country_name }}">
                                {{ $country->country_name }}
                            </option>
                        @endforeach

                    </select>
                    @if (isset($errs['country']))
                        <div class="error_msg">*<?= $errs['country'] ?></div>
                    @endif
                </div>
                <div class="col">

                </div>
            </div>
        </div>
        <div class="form-group">
            <h3 class="lead main-heading">Group Picture</h3>
            <?php if($mode=="edit"){ ?>
            Existing Cover:
            <?php if(isset($group) && $group["group_picture"]!="") { ?><img src="<?= Storage::disk('local')->url($group['group_picture']) ?>" alt="Group Image"
                style="width:150px"> <?php }
                            else{ ?> <img src="" alt="Group Cover" style="width:150px">
            <?php } ?>
            <a href="javascript:void()"
                onclick='document.getElementById("cover_page_span").style.display = "block";'>Change Picture</a>
            <?php } ?>

            <span id="cover_page_span" <?= $mode == 'edit' ? 'style="display:none;"' : '' ?>>
                <div class="lasfrm-sec">
                    <div class="file-drop-area">
                        <span class="choose-file-button">Choose file</span>
                        <br>
                        <br>
                        <span class="file-message">or drag file here</span>
                        <input class="file-input" type="file" name="group_picture"
                            value="<?= isset($group) ? $group['group_picture'] : '' ?>" />
                    </div>
                </div>
            </span>
        </div>
        <!-- <h3 class="lead main-heading">Add members</h3>
            <div class="form-group btnfrom">
                <a id="edit_item" href="#" class="btn first-btn">
                    Add
                </a>
                <a id="edit_item" href="#" class="btn second-btn">
                    Maybe later
                </a>
            </div> -->
        <div class="form-group">
            <button type="submit" class="btn lastbtn submit_btn">Submit</button>
        </div>
        @if (isset($err_message))
            <div class="error_msg">*<?= $err_message ?></div>
        @endif
    </form>
    <?php } ?>
</div>
