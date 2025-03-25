<div class="threerowsec">
    <div class="container">
        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">
                <div class="one-row">
                    <div class="form-cont">
                        @if (session()->has('error'))
                        <div class="alert alert-danger">*{{ session()->get('error') }}</div>
                        @endif
                        @if (session()->has('success'))
                        <div class="alert alert-success">{{ session()->get('success') }}</div>
                        @endif
                        <form class="main-form" name="add-contact-form" id="add-contact-form" method="post" action="{{route('store-contact')}}">
                            @csrf
                            <label>Your name</label>
                            <input name="name" type="text" placeholder="Enter Name" required="required">
                            @error('name')
                            <div class="error_msg">* {{ $message }}</div>
                            @enderror
                            <br>
                            <label>Enter Your Email Address</label>
                            <input name="email" type="email" placeholder="Enter Email Id" required="required">
                            @error('email')
                            <div class="error_msg">* {{ $message }}</div>
                            @enderror
                            <br>
                            <label>Comments/Questions</label>
                            <textarea name="comment" id="comment" cols="30" rows="10"></textarea>
                            @error('comment')
                            <div class="error_msg">* {{ $message }}</div>
                            @enderror
                            <br>
                            <!-- Google reCAPTCHA -->
                            <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}" data-expired-callback="recaptchaExpired"></div>
                            <div id="captcha_error_message" class="text-danger"></div>
                            <br>
                            <input type="submit" id="contact_btn" class="btn-cont2 center_element" style="width:120px;" value="Submit" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>

    function recaptchaExpired() {
        document.getElementById('captcha_error_message').innerText = 'The reCAPTCHA has expired. Please complete it again.';
    }

    document.getElementById('add-contact-form').addEventListener('submit', function(event) {
        var recaptchaResponse = grecaptcha.getResponse();

        if (!recaptchaResponse) {
            event.preventDefault();
            document.getElementById('captcha_error_message').innerText = 'Please verify that you are not a robot.';
            return;
        }

        var submitButton = document.getElementById('contact_btn');
        submitButton.disabled = true;
        submitButton.value = "Submitting";
    });
</script>
