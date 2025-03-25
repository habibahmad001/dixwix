<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="{{ asset('assets/media/logo.png') }}" type="image/x-icon">
    <title>Signup - {{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="{{ url('assets/css/style.css') }}">
    <script src="{{ url('assets/js/sweetalert2.min.js') }}"></script>
    <link rel="stylesheet" href="{{ url('assets/css/sweetalert2.min.css') }}">
    <!-- Include CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" />

    <!-- Include JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>

    <style>
        .btn-check:focus+.btn-primary, .btn-primary:focus{
            box-shadow: none !important;
        }

        .btn-check:focus+.btn-outline-primary, .btn-outline-primary:focus{
            box-shadow: none !important;
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
        .iti__country-list {
            width: 300px;
            color: black;
        }

        @media (max-width: 768px) {
            .iti__country-list {
                width: 100%;
            }
        }

        .text-link {
            color: #d94e29;
            text-decoration: none;
        }

        .text-link:hover, .text-link:focus, .text-link:active {
            color: #d94e29;
            text-decoration: none;
        }

    </style>

</head>
<body>
    <div class="container-fluid container_bg">
        <div id="header">
            @include('common.wo_login.header')
        </div>
        <div id="content">
            <div class="form_container position-relative">
                <div class="form_image">
                    @if (isset($err_message))
                    <div class="error_msg" style="position: absolute;text-align: center;top: 10px;left: 30%;transform: translateX(-50%);">*{{ $err_message }}</div>
                    @endif
                    <div id="client_error_message" style="position: absolute;text-align: center;top: 10px;left: 30%;transform: translateX(-50%);"></div>
                    <img src="{{ url('assets/media/account.png') }}" alt="Image Description">
                </div>
                <div class="form_inner">
                    <div class="form_wrapper">
                        <?php if(isset($retdata)){extract($retdata);} ?>
                        @if(!isset($data['referrer_id']))
                        <div class="d-flex mb-2 justify-content-center flex-column">
                            <h2 class="text-center">Signup With</h2>
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
                        @endif
                        <?php $password_guidelines = "Password Must Criteria: &#013;Atleast 6 Character&#013;Atleast 1 Capital Letter&#013;Atleast 1 special character&#013;Atleast 1 number"; ?>
                        <form name="signup-form" id="signup-form" method="post" action="{{ (!isset($data['referrer_id'])?url('store-user'):url('store-via-user')) }}">
                            @csrf
                            <h2>Create Account</h2>
                            <div class="fieldset">
                                <img src="{{ url('assets/media/user.png') }}">
                                <input type="text" id="user_name" name="user[name]" value="{{ (isset($user)?$user["name"]:"") }}" class="form-control" placeholder="Your Name" />
                                @if (isset($errs['name']))
                                <div class="error_msg">*{{ $errs['name'] }}</div>
                                @endif
                            </div>
                            <div class="fieldset">
                                <img src="{{ url('assets/media/email.png') }}">
                                <input required type="email" id="user_email" name="user[email]" value="{{ (isset($data['email_id']) ? $data['email_id'] : '') }}" class="form-control" placeholder="Email" {{ (isset($data['referrer_id']) ? "readonly" : "") }} />
                            </div>
                            <div class="fieldset">
                                <input type="tel" id="user_phone" name="user[phone]" class="form-control" />
                            </div>
                            <div class="fieldset">
                                <img src="{{ url('assets/media/password.png') }}">
                                <input type="password" id="signup_password" name="user[password]" class="form-control" minlength="6" placeholder="Password" />
                                <span id="togglePassword1" class="toggle-password" style="position: absolute;right: 10px;">
                                    <i class="fa text-dark fa-eye"></i>
                                </span>
                                @if (isset($errs['password']))
                                <div class="error_msg">*{{ $errs['password'] }}</div>
                                @endif
                            </div>
                            <div class="fieldset">
                                <img src="{{ url('assets/media/password.png') }}">
                                <input type="password" id="signup_confirm_password" name="user[confirm_password]" class="form-control" minlength="6" placeholder="Retype password" />
                                <span id="togglePassword2" class="toggle-password" style="position: absolute;right: 10px;">
                                    <i class="fa text-dark fa-eye"></i>
                                </span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="strength_label">Password Strength</span>
                                <img class="password_guideline" style="width:25px" src="{{ url('assets/media/question.png') }}" data-toggle="tooltip" title="{{ $password_guidelines }}">
                            </div>
                            <div id="password-message"></div>
                            <div class="password_strength">
                                <span class="strength_bar">
                                    <span class="strength_level strength-0"></span>
                                    <span class="strength_level strength-1"></span>
                                    <span class="strength_level strength-2"></span>
                                    <span class="strength_level strength-3"></span>
                                    <span class="strength_level strength-4"></span>
                                </span>
                            </div>
                            <div class="checkbox">
                                <input class="form-check input me-2" type="checkbox" value="1" id="term_condition_check" />
                                <label class="form-check label" for="term_condition_check" style="font-size: 12px">
                                    I agree to the
                                    <a href="{{ route('terms-of-service') }}" target="_blank" class="text-link">Terms of Service</a> &
                                    <a href="{{ route('privacy') }}" target="_blank" class="text-link">Privacy Policy</a>
                                </label>
                            </div>
                            <input type="hidden" name="referrer_id" value="{{ (isset($data['referrer_id'])?$data['referrer_id']:"") }}" />
                            <input type="hidden" name="group_id" value="{{ (isset($data['group_id'])?$data['group_id']:"") }}" />
                            <input type="hidden" name="group_type_id" value="{{ (isset($data['group_type_id'])?$data['group_type_id']:"") }}" />
                            <button type="submit" class="btn btn-primary" id="signup_submit">Submit</button>
                        </form>
                        @if(!isset($data['referrer_id']))
                        {{-- <div class="divider">
                            <hr>
                            <span>or</span>
                            <hr>
                        </div>
                        <h2>Signup With</h2>
                        <div class="social-icons">
                            <a href="#"><img src="{{ url('assets/media/facebook.png') }}" alt="Facebook"></a>
                        <a href="{{url('google-login')}}"><img src="{{ url('assets/media/google.png') }}" alt="Google"></a>
                        <a href="#"><img src="{{ url('assets/media/apple.png') }}" alt="Apple"></a>
                    </div> --}}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div id="footer">
    </div>
    </div>

    <!-- Bootstrap JS and jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- Your JS File -->
    <script src="{{ url('assets/js/scripts4.js') }}"></script>
    <script>
        document.getElementById('togglePassword1').addEventListener('click', function(e) {
            const passwordInput = document.getElementById('signup_password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
        document.getElementById('togglePassword2').addEventListener('click', function(e) {
            const passwordInput = document.getElementById('signup_confirm_password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        $(document).ready(function() {
            const phoneInputField = document.querySelector("#user_phone");
            const iti = window.intlTelInput(phoneInputField, {
                initialCountry: "auto"
                , geoIpLookup: function(callback) {
                    fetch("https://ipinfo.io/json?token=c6edd5b7da2c96")
                        .then((response) => response.json())
                        .then((data) => callback(data.country))
                        .catch(() => callback("us"));
                }
                , utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
            });

            phoneInputField.addEventListener("countrychange", function() {
                const countryCode = iti.getSelectedCountryData().dialCode;
                phoneInputField.value = `+${countryCode}`;
            });


            $("#signup_submit").on("click", function(event) {
                event.preventDefault();

                const phoneNumber = iti.getNumber();
                const isPhoneValid = iti.isValidNumber();

                let terms_check = $("#term_condition_check").is(":checked");
                let name_check = $("#user_name").val().trim().length > 0;
                let phone_check = phoneNumber.trim().length > 0;
                let email_check = $("#user_email").val().trim().length > 0;
                let pass_check = $("#signup_password").val().trim().length > 0;

                let email = $("#user_email").val().trim();
                let pass = $("#signup_password").val().trim();
                let con_pass = $("#signup_confirm_password").val().trim();
                let pass_strength_check = $("#password-message").text().indexOf("Strong") >= 0;

                let email_regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                let email_format_check = email_regex.test(email);

                $("#client_error_message").html("");

                if (!name_check || !email_check || !phone_check || !pass_check) {
                    $("#client_error_message").html("*All fields are required");
                    return;
                }

                if (!email_format_check) {
                    $("#client_error_message").html("*Invalid Email Address");
                    return;
                }

                if (!isPhoneValid) {
                    $("#client_error_message").html("*Invalid phone number. Format: +CountryCode number");
                    return;
                }

                if (pass !== con_pass) {
                    $("#client_error_message").html("*Password and confirm password do not match");
                    return;
                }

                if (!pass_strength_check) {
                    $("#client_error_message").html("*Password must be strong");
                    return;
                }

                if (!terms_check) {
                    $("#client_error_message").html("*Please accept the Terms and Conditions");
                    return;
                }

                phoneInputField.value = phoneNumber;
                $("#signup_submit").prop("disabled", true);
                $("#signup-form").submit();

            });
        });

    </script>
    @if (isset($success))
    <script>
        window.location.href = '/login?email=verify';

    </script>
    @endif
</body>
</html>
