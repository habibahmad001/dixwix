@include('common.wo_login.header')
@include('common.wo_login.start_scripts')
@include('common.wo_login.end_scripts')

<section class="<?=(isset($data['background-class']) && !empty($data['background-class']))?$data['background-class']:"heading"?>" id="box">
    <div class="text-box">
        <h1 style="color: #D94E29">{!! $data['banner_heading'] !!}</h1>
      	<p class="text-dark">Start sharing and renting your items within a trusted community. Create<br>
                              your online catalog and enjoy the benefits of DixWix.com — a platform<br>
                              that makes sharing easy, secure, and rewarding. </p>
        <?php if(isset($data['is_banner_link']) && $data['is_banner_link']) { ?>
        <div class="bannersecbtn">
            <a href="{{$data['banner_link']}}">{{$data['banner_text']}}</a>
        </div>
        <?php } else { ?> <p>{{$data['banner_text']}}</p> <?php } ?>
    </div>
</section>

<div class="container-fluid intro-wrapper position-relative">
    <img src="img/isolationimage.png" class="imagepattren">
    <div class="container intro-section text-center">
        <div class="row">
            <div class="col-md-12 bgimge mt-5">

			 <div class="head text-center">
            <p>At DixWix.com, we believe in the power of community, collaboration and sustainability. Our platform enables you to share resources, skills and expertise with friends and neighbors. Renting and borrowing resources within trusted groups, we reduce waste, promote sustainability, while earning rewards for contributing to the community</p>
        </div>
              <h1>Together, let's build a greener future in a<br> collaborative, secure and friendly environment”</h1>
            </div>
        </div>
    </div>
</div>


<section class="dixpricing">
    <div class="container">
        <div class="head text-center">
            <h2>Simple, Transparent <span>Pricing</span></h2>
            <div class="col-lg-8 col-md-12 mx-auto">
            <p>We don’t sell customer data or ad’s. Our pricing is based on a shared cost model designed to fit everyone’s needs. Everyone can get started for free with an option to upgrade for a larger number of items.</p>
            </div>
        </div>
        <div class="list row d-flex justify-content-center">
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="content">
                    <p>Basic</p>
                    <p class="price">Free</p>
                    <ul>
                        <li>25 Items</li>
                        <li>1 Group</li>
                        <li>Fixed Categories</li>
                        <li>Lend / Borrow included</li>
                        <li>QR Codes Included</li>
                        <li>Rewards Included</li>
                        <li>Google SSO included</li>
                        <li>Notifications</li>
                    </ul>
                    <a href="">Add to Cart</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="content">
                    <div class="d-flex justify-content-between">
                        <p>Pro</p>
                        <p class="tag">POPULAR</p>
                    </div>
                    <p class="price">$1/Month</p>
                    <ul>
                        <li>50 items</li>
                        <li>Unlimited Groups</li>
                        <li>Custom Categories</li>
                        <li>Lend / Borrow Included</li>
                        <li>QR Codes Included</li>
                        <li>Rewards Included</li>
                        <li>Google SSO included</li>
                        <li>Notifications</li>
                    </ul>
                    <a href="">Add to Cart</a>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Services End -->

 @include('common.wo_login.footer') <!-- Dynamic footer section -->

    <!-- Include core JavaScript files -->
    <script src="{{ asset('js/main.js') }}"></script>  <!-- Your main JavaScript file -->
