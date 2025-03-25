jQuery(document).ready(function ($) {
    // let table = new DataTable('#items_table');
    // let items_table_group = new DataTable('#items_table_group');

    $('#alert-success').hide();
    window.onload = function() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const paramName = 'email';
        const paramValue = urlParams.get(paramName);
        if(paramValue === "verify"){
            $('#alert-success').show()
                .append("Please verify your email address");
        }
        setTimeout(function() {
            $('#alert-success').hide().html("");
        }, 4000);
    };

    $('#signup_password').on('input', function () {
        var password = $(this).val();
        var passwordMessage = $('#password-message');
        $(".strength_level").removeClass('active');
        chkArray = checkStrength(password);
        for (let i = 0; i <= chkArray["count"]; i++) {
            $('.strength-' + i).addClass('active');
        }
        if (chkArray["message"] !== "") {
            passwordMessage.html('<span style="color:purple;">' + chkArray["message"] + '</span>');
        }
    });
    $('#signup_submit').on('click', function (event) {
        event.preventDefault();
        let terms_check = document.getElementById('term_condition_check').checked;
        let name_check = $("#user_name").val().length > 0;
        let email_check = $("#user_email").val().length > 0;
        let pass_check = $("#signup_password").val().length > 0;
        let pass = $("#signup_password").val();
        let con_pass = $("#signup_confirm_password").val();
        let pass_strength_check = $("#password-message").text().indexOf("Strong") >= 0;
        if (name_check && email_check && pass_check) {
            if (pass !== con_pass) {
                // showSwalMessage("Important","Password and confirm password are not same","error");
                $("#client_error_message").html("*Password and confirm password are not same");
                return;
            } else if (!pass_strength_check) {
                // showSwalMessage("Important","Password Must be Strong","warning");
                $("#client_error_message").html("*Password Must be Strong");
                return;
            }
            if (!terms_check) {
                // showSwalMessage("Important","Please Check on Terms And Condition","warning");
                $("#client_error_message").html("*Please Check on Terms And Condition");
                return;
            }
            $('#signup-form').submit();
        }
        else {
            // showSwalMessage("Important","Some fields are missing !","error");
            $("#client_error_message").html("*Some fields are missing !");
        }
    })


    $('#login_btn').on('click', function (event) {
        event.preventDefault();

        let email_check = $("#signin_email").val().length > 0;
        let pass_val = $("#signin_password").val().trim();
        let pass_check = pass_val.length > 0;
        // let captchaResponse = grecaptcha.getResponse();
        let captchaResponse = true;

        if (email_check && pass_check && captchaResponse) {
            $('#login-form').submit();
        } else {
            let errorMessage = "";

            if (!email_check || !pass_check) {
                errorMessage += "*Email and Password should not be empty";
            }

            if (!captchaResponse) {
                errorMessage += (errorMessage ? "<br>" : "") + "*Please complete the captcha.";
            }

            $("#client_error_message").html(errorMessage);
        }
    });

    // if ($("#login_btn").length > 0) {
    //     document.getElementById("login_btn").disabled = true;
    // }

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })
});

function checkStrength(pass) {
    let retMsg = [];
    if (pass.length == 0) { retMsg['count'] = -1; retMsg['message'] = ""; }
    if (pass.length < 6) {
        retMsg['count'] = 0; retMsg['message'] = "Very Weak";
    }
    else {
        let regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@.#$!%*?&])[A-Za-z\d@.#$!^%*?&]{6,25}$/;
        if (regex.test(pass)) {
            retMsg['count'] = 5
            retMsg['message'] = "Very Strong";
        }
        else {
            let count = 0;
            let regex1 = /[a-z]/;
            if (regex1.test(pass)) count++;
            let regex2 = /[A-Z]/;
            if (regex2.test(pass)) count++;
            let regex3 = /[\d]/;
            if (regex3.test(pass)) count++;
            let regex4 = /[!@#$%^&*.?]/;
            if (regex4.test(pass)) count++;
            retMsg['count'] = count;
            switch (count) {
                case 1: retMsg['message'] = "Weak"; break;
                case 2: retMsg['message'] = "Medium"; break;
                case 3: retMsg['message'] = "Strong"; break;
                case 4: retMsg['message'] = "Very Strong"; break;
            }
        }
        return retMsg;
    }
    return retMsg;
}

// Recaptcha Code
function enableLoginBtn() {
    document.getElementById("login_btn").disabled = false;
}
var recaptcha1;
var renderCaptchaFn = function () {
    recaptcha1 = grecaptcha.render('recaptcha1', {
        'sitekey': '6LdcqzIqAAAAAJeavZoe5BKefaCbFkM4x6v40XhL',
        'theme': 'light', // can also be dark
        'callback': 'enableLoginBtn' // function to call when successful verification for button 1
    });
};
function recaptchaExpired() {
    document.getElementById("login_btn").disabled = true;
}

function showSwalMessage(title, message, type) {
    swal.fire(title, message, type);
}

function showSwalMessageWithCallback(title, message, type, callbackFn) {
    // swal.fire(title, message, type).then(callbackFn());
    Swal.fire({
        title: message,
        icon: type,
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Ok"
    }).then((result) => {
        if (result.isConfirmed) {
            callbackFn()
        }
    });
}

function printDiv(titleDivId, bodyDivId) {
    var titlePrint = document.getElementById(titleDivId);
    var divToPrint = document.getElementById(bodyDivId);
    var newWin = window.open('', 'Print-Window');
    newWin.document.open();
    newWin.document.write('<html><body onload="window.print()">' + '<h3>' + titlePrint.innerHTML + '</h3>' + divToPrint.innerHTML + '</body></html>');
    newWin.document.close();
    // setTimeout(function(){newWin.close();},10);
}
