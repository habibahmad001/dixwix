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
