<style>
    a:hover, a:focus{
        text-decoration: none;
    }
    a h3.lead.main-heading:hover, a h3.lead.main-heading:focus{
        text-decoration: none;
        text-underline: none;
    }
</style>

<div class="container">

<?php if (isset($data['my_groups']) && count($data['my_groups'])>0) {?>
    <div class="heading">
        <h2><?=$data['title']?></h2>
    </div>
    <div class="row">
        <div class="MultiCarousel" data-items="1,3,5,6,1" data-slide="1" id="MultiCarousel" data-interval="1000">
            <div class="MultiCarousel-inner">
            @foreach($data['my_groups'] as $group)
                <div class="item">
                    <div class="pad15">
                        <div class="innerheader">
                            <a href="{{ route('show-group', ["id" => $group->id]) }}">
                                <h3 class="lead main-heading">{{ $group['title'] }}</h3>
                            </a>
                            <div class="post_image">
                                <a href="<?= route('show-group', ["id" => $group->id]) ?>"><img src="assets/media/eye-outline.png" alt="View Group"></a>
                                <?php if(Auth::user()->hasRole("admin") || $group["created_by"] == Auth::user()->id) { ?>
                                    <a href="<?= route('edit-group', ["id" => $group["id"]]) ?>" title="Edit Group"><img src="assets/media/edit-orange.png" alt="Edit Group"></a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="innerheader" style="display:none;">
                            <h3 class="lead main-heading">Status</h3>

                            <div class="post_image">
                                <label class="switch">
                                    <input id="group_{{ $group['id'] }}" onchange="updateGroupStatus({{ $group['id'] }})" type="checkbox" {{$group['status'] == 1 ? 'checked' : '' }}><span class="slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="divider">
                            <hr>
                        </div>
                        <div class="carousel-date">Created: <?= date('M/d/Y', strtotime($group['created_at'])) ?></div>
                        <div class="imagesection">
                            <?php if($group["group_picture"] != "") { ?>
                                <img class="im-wd" src="<?= asset('storage/'.$group["group_picture"]) ?>" alt="Group Image">
                            <?php } else { ?>
                                <img src="" alt="Group Image">
                            <?php } ?>

                            <?php if(!empty($group["to_be_deleted_at"])) { ?>
                                <span class="item-type-book">Marked Deleted</span>
                            <?php } else { ?>
                                <table>

                                <?php if($group["created_by"] == Auth::user()->id || Auth::user()->hasRole('admin')) { ?>
                                    <tr><td><a href="javascript:void(0)" data-group_id="{{ $group['id'] }}" data-group_type_id="{{ $group['group_type_id'] }}" id="getMembersToAdd" onclick="getMembersToAdd('<?= route('get-members-to-add', ['group_id' => $group['id'], 'group_type_id' => $group['group_type_id']]) ?>', '<?=$group['group_type_id']?>')" class="dark-btn btn link_with_img">
                                        <img src="assets/media/add-circle-outline.png"> Invite
                                    </a></td></tr>
                                <?php } ?>
                                <!-- Share Button -->
                               <tr><td> <button id="shareBtn_<?= $group['id'] ?>" class="dark-btn btn link_with_img" onclick="copyToClipboard('<?= route('show-group', ['id' => $group['id']]) ?>', 'shareBtn_<?= $group['id'] ?>')">
                                    <img src="https://icon-library.com/images/sharing-icon-png/sharing-icon-png-14.jpg"> Share
                                </button></td></tr>
                            </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            @endforeach

            <script>
                function copyToClipboard(url, buttonId) {
                    // Create a temporary input element to copy the URL to clipboard
                    var tempInput = document.createElement("input");
                    tempInput.value = url;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand("copy");
                    document.body.removeChild(tempInput);

                    // Get the button element and change the text to indicate success
                    var button = document.getElementById(buttonId);
                    button.innerHTML = '<img src="https://icon-library.com/images/sharing-icon-png/sharing-icon-png-14.jpg"> Copied!';

                    // Revert back to original text after 10 seconds
                    setTimeout(function() {
                        button.innerHTML = '<img src="https://icon-library.com/images/sharing-icon-png/sharing-icon-png-14.jpg"> Share';
                    }, 2200); // 10 seconds
                }
            </script>

            </div>
            <button class="btn-primary leftLst">
                <button class="btn-primary leftLst"><</button>
                <button class="btn-primary rightLst">></button>
        </div>
    </div>
</div>
<?php } else {
    ?>
         <div class="item search-result">
            <div class="text404">
              <img src="<?=url('assets/media/error 1.png')?>" alt="Book Image">
              <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">Group not found in library</p>
            </div>
        </div>
    <?php
} ?>
@if($data['title'] != 'Searched Groups')
<div class="container">
    <div class="heading">
        <h2>Joined Groups</h2>
    </div>
    <?php if (isset($data['joined_groups']) && count($data['joined_groups'])>0) {?>
    <div class="row">
        <div class="MultiCarousel" data-items="1,3,5,6,1" data-slide="1" id="MultiCarousel" data-interval="1000">
            <div class="MultiCarousel-inner">
            @foreach($data['joined_groups'] as $group)
                <div class="item">
                    <div class="pad15">
                        <div class="innerheader">
                            <h3 class="lead main-heading"><?=$group['title']?></h3>
                            <div class="post_image">
                                <a href="<?=route('show-group',["id"=>$group->id])?>"><img src="assets/media/eye-outline.png" alt="View Group"></a>
                                @if(Auth::user()->hasRole("admin") || $group["created_by"] == Auth::user()->id)
                                    <a href="{{ route('edit-group',["id"=>$group["id"]]) }}" title="Edit Group"><img src="assets/media/edit-orange.png" alt="Edit Group"></a>
                                @endif
                            </div>
                        </div>

                        <div class="divider">
                            <hr>
                        </div>
                        <div class="carousel-date">Created: <?=date('M/d/Y',strtotime($group['created_at']))?></div>
                        <div class="imagesection">
                        <?php if($group["group_picture"]!=""){ ?>
                        <img class="im-wd" src="<?= asset('storage/'.$group["group_picture"])?>" alt="Group Image">
                        <?php }
                        else{ ?>
                        <img src="" alt="Group Image">
                        <?php } ?>
                        @if(!empty($group['current_user'] && $group['current_user']['member_role'] == 'admin' && $group['current_user']['activated']))
                                <table><tr><td><a href="javascript:void(0)" 
                               data-group_id="{{ $group['id'] }}" 
                               data-group_type_id="{{ $group['group_type_id'] }}" 
                               id="getMembersToAdd" 
                               onclick="getMembersToAdd('<?= route('get-members-to-add', ['group_id' => $group['id'], 'group_type_id' => $group['group_type_id']]) ?>', '<?= $group['group_type_id'] ?>')" 
                               class="dark-btn btn link_with_img">
                                <img src="assets/media/add-circle-outline.png"> Invite
                            </a></td></tr><tr><td><button id="shareBtn_<?= $group['id'] ?>" 
                                    class="dark-btn btn link_with_img" 
                                    onclick="copyToClipboard('<?= route('show-group', ['id' => $group['id']]) ?>', 'shareBtn_<?= $group['id'] ?>')">
                                <img src="https://icon-library.com/images/sharing-icon-png/sharing-icon-png-14.jpg"> Share
                            </button></td></tr></table>

                            

                            <script>
                                // JavaScript function to copy text to clipboard
                                function copyToClipboard(url, buttonId) {
                                    // Create a temporary input element to copy the URL to clipboard
                                    var tempInput = document.createElement("input");
                                    tempInput.value = url;
                                    document.body.appendChild(tempInput);
                                    tempInput.select();
                                    document.execCommand("copy");
                                    document.body.removeChild(tempInput);
                                    
                                    // Change the button text or style to show the user it was copied
                                    var button = document.getElementById(buttonId);
                                    button.innerHTML = '<img src="https://icon-library.com/images/sharing-icon-png/sharing-icon-png-14.jpg"> Copied!';
                                    
                                    // Optionally, you can add a timeout to reset the button text after a few seconds
                                    setTimeout(function() {
                                        button.innerHTML = '<img src="https://icon-library.com/images/sharing-icon-png/sharing-icon-png-14.jpg"> Share';
                                    }, 3000);
                                }
                            </script>

                                @endif
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
            <button class="btn-primary leftLst">
                <button class="btn-primary leftLst"><</button>
                <button class="btn-primary rightLst">></button>
        </div>
    </div>
    <?php } else { ?>
        <div class="item search-result">
            <div class="text404">
              <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">No Group Joined</p>
            </div>
        </div>
    <?php } ?>
</div>
@endif
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
