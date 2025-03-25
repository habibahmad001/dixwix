<?php if (isset($retdata)) {
    extract($retdata);
} ?>

<style>
    .disabled-card {
      position: relative; /* To position the message correctly */
      opacity: 0.5;
      pointer-events: none; /* Disable interaction */
    }

    .disabled-message {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      padding: 10px;
      border-radius: 5px;
      font-size: 14px;
      text-align: center;
      z-index: 2; /* Make sure it's above other elements */
    }

</style>
<div class="content">
    <?php //if (count($week_group) > 0) { ?>
        <div class="container">
            <div class="heading">
                <h2>All Groups</h2>
            </div>
            <?php /* if(count($week_group_lender)>0){ ?>
            <div class="divider">
                <hr>
            </div>
            <div class="heading">
                <h2>Lender</h2>
            </div>
            <div class="row">
                <div class="MultiCarousel" data-items="1,3,5,6,1" data-slide="1" id="MultiCarousel" data-interval="1000">
                    <div class="MultiCarousel-inner">
                        <?php foreach($week_group_lender as $wgroup){ ?>
                            <div class="item">
                                <div class="pad15">
                                    <div class="innerheader">
                                        <h3 class="lead main-heading"><?=$wgroup["title"]?></h3>
                                        <div class="post_image">
                                            <a href="<?=route('show-group',["id"=>$wgroup["id"]])?>"><img src="assets/media/eye-outline.png" alt="View Group"/></a>
                                        </div>
                                    </div>
                                    <div class="divider">
                                        <hr>
                                    </div>
                                    <div class="carousel-date">Created: <?=date("Y-m-d",strtotime($wgroup["created_at"]))?> </div>
                                    <div class="member">Members: <?= count($wgroup["addedmembers"]) ?></div>
                                    <div class="imagesection">
                                        <?php if($wgroup["group_picture"]!=""){ ?>
                                            <img class="im-wd" src="<?=Storage::disk('public')->url($wgroup["group_picture"])?>" alt="Group Image">
                                        <?php }
                                        else{ ?> <img src="" alt="Group Image"> <?php } ?>
                                        <?php
                                        $joined = false;
                                        $requested = false;
                                        foreach($wgroup['groupmembers'] as $mem){
                                            if($mem['member_id'] == Auth::user()->id){
                                                if($mem["status"] == "added"){$joined = true;}
                                                if($mem["status"] == "requested"){$requested = true;}
                                                break;
                                            }
                                        }
                                        if ($joined) { ?>
                                            <span class="item-type-book">Joined</span>
                                        <?php } else if($requested){ ?>
                                            <span class="item-type-book">Requested to Join</span>
                                        <?php } else{ ?>
                                            <a href="javascript:void(0)" class="dark-btn btn link_with_img" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to join the group?','question',function(){RequestJoinGroup('<?=route('request-join')?>','<?=$wgroup['id']?>','<?=Auth::user()->id?>');})">
                                                <img src="assets/media/add-circle-outline.png"> Join Group
                                            </a>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <button class="btn-primary leftLst"> < </button>
                    <button class="btn-primary rightLst"> > </button>
                </div>
            </div>
        <?php } ?>

        <?php if(count($week_group_borrower)>0){ ?>
            <div class="divider">
                <hr>
            </div>
            <div class="heading">
                <h2>Borrower</h2>
            </div>
            <div class="row">
                <div class="MultiCarousel" data-items="1,3,5,6,1" data-slide="1" id="MultiCarousel" data-interval="1000">
                    <div class="MultiCarousel-inner">
                        <?php foreach($week_group_borrower as $wgroup){ ?>
                            <div class="item">
                                <div class="pad15">
                                    <div class="innerheader">
                                        <h3 class="lead main-heading"><?=$wgroup["title"]?></h3>
                                        <div class="post_image">
                                            <a href="<?=route('show-group',["id"=>$wgroup["id"]])?>"><img src="assets/media/eye-outline.png" alt="View Group"/></a>
                                        </div>
                                    </div>
                                    <div class="divider">
                                        <hr>
                                    </div>
                                    <div class="carousel-date">Created: <?=date("Y-m-d",strtotime($wgroup["created_at"]))?> </div>
                                    <div class="member">Members: <?= count($wgroup["addedmembers"]) ?></div>
                                    <div class="imagesection">
                                        <?php if($wgroup["group_picture"]!=""){ ?>
                                            <img class="im-wd" src="<?=Storage::disk('local')->url($wgroup["group_picture"])?>" alt="Group Image">
                                        <?php }
                                        else{ ?> <img src="" alt="Group Image"> <?php } ?>
                                        <?php
                                        $joined = false;
                                        $requested = false;
                                        foreach($wgroup['groupmembers'] as $mem){
                                            if($mem['member_id'] == Auth::user()->id){
                                                if($mem["status"] == "added"){$joined = true;}
                                                if($mem["status"] == "requested"){$requested = true;}
                                                break;
                                            }
                                        }
                                        if ($joined) { ?>
                                            <span class="item-type-book">Joined</span>
                                        <?php } else if($requested){ ?>
                                            <span class="item-type-book">Requested to Join</span>
                                        <?php } else{ ?>
                                            <a href="javascript:void(0)" class="dark-btn btn link_with_img" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to join the group?','question',function(){RequestJoinGroup('<?=route('request-join')?>','<?=$wgroup['id']?>','<?=Auth::user()->id?>');})">
                                                <img src="assets/media/add-circle-outline.png"> Join Group
                                            </a>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <button class="btn-primary leftLst"> < </button>
                    <button class="btn-primary rightLst"> > </button>
                </div>
            </div>
        <?php } */ ?>

            <div class="divider">
                <hr>
            </div>
            <div class="row">
                <div class="MultiCarousel" data-items="1,3,5,6,1" data-slide="1" id="MultiCarousel"
                     data-interval="1000">
                    <div class="MultiCarousel-inner">
                      @if($week_group)
                        <?php foreach ($week_group as $wgroup) {

                            $joined = false;
                            $requested = false;
                            $invited = false;
                            foreach ($wgroup['groupmembers'] as $mem) {
                                if ($mem['member_id'] == Auth::user()->id) {
                                    if ($mem["status"] == "added") {
                                        $joined = $mem["status"];
                                    }
                                    if ($mem["status"] == "requested") {
                                        $requested = true;
                                    }
                                    if ($mem["status"] == "invited") {
                                        $invited = true;
                                    }

                                    break;
                                }
                            }
                      	?>
                            <div class="item {{ $wgroup["status"] == '0'?'disabled-card':'' }}"">
                                <div class="pad15">
                                    <div class="innerheader">
                                        <h3 class="lead main-heading"><?= $wgroup["title"] ?></h3>
                                        <div class="post_image">
                                             @if($joined)
                                            <a href="<?= route('show-group', ["id" => $wgroup["id"]]) ?>">
                                                <img src="assets/media/eye-outline.png" alt="View Group"/></a>
                                            @else
                                            <img src="assets/media/eye-close.svg" alt="View Group" title="join the group to view details"/></a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="divider">
                                        <hr>
                                    </div>
                                    <div class="carousel-date">
                                        Created: <?= date("Y-m-d", strtotime($wgroup["created_at"])) ?> </div>
                                    <div class="member">Members: <?= count($wgroup["addedmembers"]) ?></div>
                                    <div class="imagesection">
                                        <?php if ($wgroup["group_picture"] != "") { ?>
                                             <img class="im-wd"
                                                 src="<?= asset('storage/'.$wgroup["group_picture"]) ?>"
                                                 alt="Group Image">
                                        <?php } else { ?> <img src="" alt="Group Image"> <?php } ?>
                                        <?php

                                        if ($joined) { ?>
                                            <span class="item-type-book">Joined</span>
                                        <?php } else if ($requested) { ?>
                                            <span class="item-type-book">Requested to Join</span>
                                      <?php } else if ($invited) { ?>
                                                <span class="item-type-book">Invited</span>
                                        <?php } else { ?>
                                            <a href="javascript:void(0)" class="dark-btn btn link_with_img"
                                               onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to join the group?','question',function(){RequestJoinGroup('<?= route('request-join') ?>','<?= $wgroup['id'] ?>','<?= Auth::user()->id ?>');})">
                                                <img src="assets/media/add-circle-outline.png"> Join Group
                                            </a>
                                        <?php } ?>
                                    </div>
              					   @if($wgroup['status'] == '0')
                                    <div class="disabled-message">
                                        This group is disabled
                                    </div>
                                    @endif
                                </div>
                            </div>
                        <?php } ?>
  						@else
                       <div class="item search-result">
                        <div class="text404">
                          <img src="<?=url('assets/media/error 1.png')?>" alt="No Group Joined">
                          <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">No Results Found</p>
                        </div>
                        </div>
                       @endif
                    </div>
                    <button class="btn-primary leftLst"> <</button>
                    <button class="btn-primary rightLst"> ></button>
                </div>
            </div>
        </div>
    <?php // } ?>
</div>
