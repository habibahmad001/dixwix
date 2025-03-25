<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/media/logo.png') }}" type="image/x-icon">
    <title>Login - {{ env('APP_NAME') }}</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    @if (env('APP_ENV') !== 'local')
        <script src="https://www.google.com/recaptcha/api.js?onload=renderCaptchaFn&render=explicit&hl=en" async defer></script>
    @endif

    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">

    @if (env('APP_ENV') !== 'local')
        <script>
            var RECAPTCHA_SITE_KEY = '{{ env('RECAPTCHA_SITE_KEY') }}'
        </script>
    @endif

    <style>
        .btn-check:focus+.btn-primary, .btn-primary:focus{
            box-shadow: none !important;
        }

        .btn-check:focus+.btn-outline-primary, .btn-outline-primary:focus{
            box-shadow: none !important;
        }
        .btn-primary:hover, .btn-primary:focus {
            color: var(--orange-01);
        }
        .btn-primary{
            color: var(--white);
        }
        .form_wrapper h2 {
            color: var(--white);
        }
        .form_wrapper h3 {
            color: var(--white);
        }
        .form_wrapper .checkbox .form-check {
            margin-bottom: 0;
            padding-left: 0px;
            min-height:0.5rem;
        }
        .form-control {
            height: calc(1.5em + .75rem + 2px);
            border-radius: .25rem;
        }
        .form_wrapper .fieldset input{
            color: #495057 !important;
        }
        .fieldset {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #797979
        }

        .text-danger {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 5px;
        }

    </style>
</head>
<body>
    <div class="container-fluid container_bg p-0">
        <div id="header">
            @include('common.wo_login.header')
        </div>
        <style>
            a.btn.btn-primary.text-white.py-2.px-4.flex-wrap.flex-sm-shrink-0.btncss:hover {
                color: white !important;
            }

            a.btn.btn-primary.text-white.py-2.px-4.flex-wrap.flex-sm-shrink-0.btncss.transp:hover {
                background: white !important;
            }

        </style>
        <div id="content">
            <div class="form_container position-relative">
                <div class="success_msg my-2" id="alert-success" style="position: absolute;color: green;font-size: large;text-align: center;top: 10px;left: 30%;transform: translateX(-50%);"></div>
                @if (isset($retdata['success_message']))
                <div class="success_msg" style="position: absolute;color: green;font-size: large;text-align: center;top: 10px;left: 30%;transform: translateX(-50%);">{{ $retdata['success_message'] }}</div>
                @elseif (isset($retdata['err_message']))
                <span id="error-message" class="error_msg" style="position: absolute;text-align: center;top: 10px;left: 30%;transform: translateX(-50%);">*{{ $retdata['err_message'] }}</span>
                @endif
                <div id="client_error_message" style="position: absolute;text-align: center;top: 30px;left: 30%;transform: translateX(-50%);"></div>
                <div class="form_image">
                    <img src="assets/media/account.png" alt="Image Description">
                </div>
                <div class="form_inner">
                    <div class="form_wrapper">
                        <div class="d-flex mb-2 justify-content-center flex-column">
                            <h2 class="text-center">Login With</h2>
                            <div class="social-icons d-flex justify-content-center">
                                {{-- <a href="#"><img src="assets/media/facebook.png" alt="Facebook"></a> --}}
                                <a href="{{url('google-login')}}"><img src="assets/media/google.png" alt="Google"></a>
                                {{-- <a href="#"><img src="assets/media/apple.png" alt="Apple"></a> --}}
                            </div>
                            <div class="divider">
                                <hr>
                                <span>or</span>
                                <hr>
                            </div>
                        </div>
                        <form name="login-form" id="login-form" method="post">
                            @csrf
                            <h2>Login to your account</h2>
                            <div class="fieldset">
                                <img src="assets/media/email.png">
                                <input type="email" id="signin_email" name="email" class="form-control" placeholder="Email" />
                            </div>
                            <div class="fieldset">
                                <img src="assets/media/password.png">
                                <input type="password" id="signin_password" name="password" class="form-control" placeholder="Password" />
                                <span id="togglePassword" class="toggle-password">
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                            <div class="remember_wrap">
                                <span class="checkbox">
                                    <input class="form-check input me-2" type="checkbox" value="1" id="remember_me" name="remember_me" />
                                    <label class="form-check label" for="remember_me">Remember Me</label>
                                </span>
                                <a href="{{ url('forgot-password') }}">Forgot Password?</a>
                            </div>

                            @if (env('APP_ENV') !== 'local')
                                <div style="width: 302px;" id="recaptcha1" class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}" data-expired-callback="recaptchaExpired"></div>
                            @else
                                <div id="recaptcha1"></div> <!-- Optionally, you can hide it or leave it empty -->
                            @endif
                            <div id="captcha_error_message" class="text-danger"></div>
                            <button type="submit" id="login_btn" class="btn btn-primary">Submit</button>
                        </form>
                        <h3>Don't have an account?</h3>
                        <a href="{{ route('signup') }}" class="btn btn-outline-primary">Create Account</a>
                        {{-- <div class="divider">
                            <hr>
                            <span>or</span>
                            <hr>
                        </div>
                        <h2>Login With</h2>
                        <div class="social-icons">
                            <a href="#"><img src="assets/media/facebook.png" alt="Facebook"></a>
                            <a href="{{url('google-login')}}"><img src="assets/media/google.png" alt="Google"></a>
                        <a href="#"><img src="assets/media/apple.png" alt="Apple"></a>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
    <div id="footer"></div>
    </div>

    <!-- Bootstrap JS and jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <!-- <script src="assets/js/scripts4.js"></script> -->

    <script>
        $(document).ready(function() {

            const errorMessageElement = $('#error-message');

            if (errorMessageElement.length) {

                const currentUrl = window.location.href;
                const url = new URL(currentUrl);
                url.pathname = '/login';
                url.search = '';
                window.history.replaceState({}, '', url.href);

                setTimeout(function() {
                    errorMessageElement.remove();
                }, 5000);
            }

            $('#login-form').on('submit', function(event) {
                event.preventDefault();

                let email = $("#signin_email").val();
                let email_regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                let email_format_check = email_regex.test(email);

                if (!email_format_check) {
                    $("#client_error_message").html("*Invalid Email Address");
                    return;
                }

                $.ajax({
                    url: "{{ url('login-user') }}"
                    , method: "POST"
                    , data: $(this).serialize()
                    , dataType: 'json'
                    , success: function(response) {
                        if (response.success) {
                            window.location.href = response.redirect_url;
                        } else {
                            $('#client_error_message').html(response.message).addClass('text-danger');
                        }
                    }
                    , error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        if (errors) {
                            var errorMsg = '';
                            $.each(errors, function(key, value) {
                                errorMsg += '<p>' + value[0] + '</p>';
                            });
                            $('#client_error_message').html(errorMsg).addClass('text-danger');
                        }
                    }
                });
            });
        });

        document.getElementById('togglePassword').addEventListener('click', function(e) {
            const passwordInput = document.getElementById('signin_password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

    </script>
</body>
</html>
