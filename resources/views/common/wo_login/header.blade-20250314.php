<div class="container-fluid position-relative p-0">
    <nav class="navbar navbar-expand-lg navbar-light px-4 px-lg-5 py-3 py-lg-0">
        <a href="{{ route('home') }}" class="navbar-brand p-0">

            <img src="{{ url('img/logo.png') }}" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
            <span class="fa fa-bars"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto py-0">
                <a href="{{ route('home') }}" class="nav-item nav-link active">Home</a>
                {{-- <a href="{{ route('pricing') }}" class="nav-item nav-link">Pricing</a> --}}
                {{-- <a href="{{ route('howitworks') }}" class="nav-item nav-link">How it works?</a> --}}
                <!-- <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                    <div class="dropdown-menu m-0">
                        <a href="appointment.html" class="dropdown-item">Appointment</a>
                        <a href="feature.html" class="dropdown-item">Features</a>
                        <a href="blog.html" class="dropdown-item">Our Blog</a>
                        <a href="team.html" class="dropdown-item">Our Team</a>
                        <a href="testimonial.html" class="dropdown-item">Testimonial</a>
                        <a href="404.html" class="dropdown-item">404 Page</a>
                    </div>
                </div> -->
                <a href="{{ route('support') }}" class="nav-item nav-link">Support</a>
                <a href="{{ route('contactus') }}" class="nav-item nav-link">Contact Us</a>
            </div>
            @auth
            <a href="{{ route('dashboard') }}"
               class="btn btn-primary text-white py-2 px-4 flex-wrap flex-sm-shrink-0 btncss">Dashboard</a>

            <a class="btn btn-primary text-white py-2 px-4 flex-wrap flex-sm-shrink-0 btncss transp" href="{{ route('logout') }}">
                Logout
            </a>
            @endauth
            @guest
            <a href="{{ url('/login') }}"
               class="btn btn-primary text-white py-2 px-4 flex-wrap flex-sm-shrink-0 btncss">Login</a>
            <a href="{{ url('/signup') }}"
               class="btn btn-primary text-white py-2 px-4 flex-wrap flex-sm-shrink-0 btncss transp">Get started</a>
            @endguest
        </div>
    </nav>


    <!-- banner section Start -->
    <?php if(isset($data['is_banner']) && $data['is_banner']){?>
    @include('common.wo_login.banner')
    <?php } ?>
    <!-- banner section End -->
</div>
