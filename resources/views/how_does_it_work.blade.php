@include('common.wo_login.header')
@include('common.wo_login.start_scripts')
@include('common.wo_login.end_scripts')

<section class="<?=(isset($data['background-class']) && !empty($data['background-class']))?$data['background-class']:"heading"?>" id="box">
    <div class="text-box">
        <h1 style="color: #D94E29">{!! $data['banner_heading'] !!}</h1>
        <p class="text-dark">Our peer-to-peer rental platform helps catalog items you own, and rent them privately with friends and neighbors. Here's how it works</p>
        @if(isset($data['is_banner_link']) && $data['is_banner_link'])
        <div class="bannersecbtn">
            <a href="{{$data['banner_link']}}">{{$data['banner_text']}}</a>
        </div>
        @else
        <p>{{ $data['banner_text']}}</p>
        @endif
    </div>
</section>

<div class="container-fluid intro-wrapper position-relative">
    <img src="img/isolationimage.png" class="imagepattren">
    <div class="container intro-section text-center">
        <div class="row">
            <div class="col-md-12 bgimge mt-5">
                <div class="head text-center">
                    <h2>How Does It<span> Work?</span></h2>
                    <p>Welcome to DixWix.com !! Our platform makes it easy for you to create an online catalog of items you own to rent them within a private community of trusted friends, neighbors, or colleagues. Here's how it works:</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container servicesset">
    <div class="row">
        <div class="col-md-6">
            <img src="img/register.png" class="imageset">
        </div>

        <div class="col-md-6 bgimge">
            <h1 class="headingallsec">Sign Up and<br>
                Create Your Profile</h1>
            <img src="img/isolationimage.png" class="imagepattren">
            <p class="alltextsetion mt-5" style="font-size:22px">Join by signing up with your email or google account. Confirm signup with link send on confirmation email, a required step. Update profile with address, state, country, zip code, phone # and pictures. Add at least one location detail for items at your home, office or community.
                For Example : Home - Garage, Floor #, Bedroom#, Shelf # or Closet #
                Office - Room #, Office Space # or Desk #
            </p>
        </div>
    </div>
</div>
<div class="container servicesset sec-two">
    <div class="row">
        <div class="col-md-6 bgimge">
            <h1 class="headingallsec">Join or Create<br>
                Community Groups</h1>
            <img src="img/isolation3.png" class="imagepattren">
            <p class="alltextsetion mt-5" style="font-size:22px">Create a private community group and invite your friends and neighbours. Join a community group by searching and sending a request to group owner to join. <br/> Search a Group to join within your local community.</p>
        </div>
        <div class="col-md-6">
            <img src="img/LoginDashboard.png" class="imageset">
        </div>
    </div>
</div>
<div class="container servicesset sec-three">
    <div class="row">
        <div class="col-md-6">
            <img src="img/support-banner.png" class="imageset">
        </div>
        <div class="col-md-6 bgimge">
            <h1 class="headingallsec">Create Your<br>
                Catalog</h1>
            <img src="img/isolationimage.png" class="imagepattren">
            <p class="alltextsetion mt-5" style="font-size:22px">Add items you own to your personal catalog. Include a title, description, photos, and any specific terms for borrowing or renting. <br/> Use the Amazon product API for quick and accurate item details. Use the bulk upload. Set Rental Prices.<br/>Our AI pricing engine helps you set competitive rental prices based on item condition,cost, demand and market trends. <br/>Choose to offer items for free or for a rental fee.</p>
        </div>
    </div>
</div>


<div class="container servicesset sec-two">
    <div class="row">
        <div class="col-md-6 bgimge">
            <h1 class="headingallsec">Rent and<br>
                Supportive Experience</h1>
            <img src="img/isolation3.png" class="imagepattren">
            <p class="alltextsetion mt-5" style="font-size:22px">Enjoy peace of mind with secure transactions and communication. <br/>Our platform offers features to ensure the safety and security of your items and interactions. <br/>Rate and review transactions to help build a trusted community. <br/>Share your experiences and connect with others to foster a supportive and collaborative environment.</p>
            <div class="bannersecbtn">
                <a href="{{route('signup')}}">Get started for free</a>
            </div>
        </div>
        <div class="col-md-6">
            <img src="img/chair_boy.png" class="imageset">
        </div>

        <div class="container servicesset sec-three">
            <div class="row">
                <div class="col-md-6">
                    <img src="img/security.png" class="imageset">
                </div>

                <div class="col-md-6 bgimge">
                    <h1 class="headingallsec">Enjoy a Secure and<br>
                        Compensation</h1>
                    <img src="img/isolationimage.png" class="imagepattren">
                    <p class="alltextsetion mt-5" style="font-size:22px">Resource owners earn reward points from borrowing members. These points can be redeemed for Gift Cards, providing an added incentive to share your resources and contribute to the community.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-12" style="background-image: url('img/hiws.png');background-position: center;background-size: contain;width: 100% !important;height: 400px !important;background-repeat: no-repeat;border: 1px solid #000;border-radius: 5px;margin-bottom: 5px;">
        </div>
    </div>
</div>

<!-- Services End -->

@include('common.wo_login.footer')
<!-- Dynamic footer section -->

<!-- Include core JavaScript files -->
<script src="{{ asset('js/main.js') }}"></script> <!-- Your main JavaScript file -->

@include('homepage_script')
