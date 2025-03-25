<section class="<?=(isset($data['background-class']) && !empty($data['background-class']))?$data['background-class']:"heading"?>" id="box">
    <div class="text-box">
        <h1>{!! $data['banner_heading'] !!}</h1>
        <?php if(isset($data['is_banner_link']) && $data['is_banner_link']) { ?>
        <div class="bannersecbtn">
            <a href="{{$data['banner_link']}}">{{$data['banner_text']}}</a>
        </div>
        <?php } else { ?> <p>{{$data['banner_text']}}</p> <?php } ?>
    </div>
</section>
