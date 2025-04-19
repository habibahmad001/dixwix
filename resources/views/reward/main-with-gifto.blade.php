<style>
    .a-bal {
        margin: 25% 0 0 0;
    }

    .a-bal sub {
        color: #963c45;
        font-size: 11px;
    }

    .red-msg {
        color: #963c45;
        font-size: 11px;
    }
</style>

<div class="content">
    <div class="container">
        <div class="heading mb-4">
            <h2>My Rewards</h2>
        </div>
        <div class="divider mb-4">
            <hr>
        </div>

        @if(session()->has('success'))
        <div class="alert alert-success">
            {{ session()->get('success') }}
        </div>
        @endif
        @if(session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
        @endif

        @if(!empty($client_secret))
            <div class="item">
                <div class="card-body">
                    <div id="payment-form">
                        <div class="post_image d-flex align-items-center flex-row">
                            <h3 class="lead mb-0 main-heading text-nowrap ms-2">You're purchasing {{ $points }} points for ${{ $price }}</h3>
                        </div>
                        <br>
                        @if(count($payment_methods)>0)
                            <label>Saved payment methods</label>
                            <div class="payment-methods-list">
                                @foreach($payment_methods as $method)
                                    <label class="card payment-method-card mb-3 w-100">
                                        <div class="card-body d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('assets/media/' . strtolower($method->type) . '.png') }}"
                                                     alt="{{ ucfirst($method->type) }} Logo" class="card-icon mr-3">
                                                <div>
                                                    <h5 class="mb-1 text-nowrap">**** **** **** {{ $method->last4 }}
                                                        @if($method->default)
                                                            <span class="badge badge-success mr-2">Primary</span>
                                                        @endif
                                                    </h5>
                                                    <p class="mb-0 text-muted">Expiration {{ $method->expiry_month }}/{{ $method->expiry_year }}</p>
                                                </div>
                                            </div>
                                            <input type="radio" name="selected_payment_method" value="{{ $method->id }}"
                                                   @if($method->default) checked @endif class="ms-2 payment-radio">
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div class="form-check mt-3 mb-3">
                                <input class="form-check-input" type="checkbox" id="toggle-new-card">
                                <label class="form-check-label" for="toggle-new-card">
                                    Pay with new card
                                </label>
                            </div>
                        @endif

                        <div id="new-card-section" class="@if(count($payment_methods)>0)) d-none @endif">
                            <div class="mb-3">
                                <div id="card-element" class="form-control"></div>
                            </div>
                            <div id="card-errors" class="text-danger"></div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" id="save-card">
                                <label class="form-check-label" for="save-card">
                                    Save card for future payments
                                </label>
                            </div>
                        </div>

                        <button id="pay-button" class="btn rewards-buttons lastbtn submit_btn w-100 mt-3">
                            <span id="button-text">Pay Now</span>
                            <span id="loading-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-6 col-xl-4 col-md-6">
                <div class="item">
                    <div class="pad15" style="height:280px">
                        <div class="innerheader">
                            <div class="post_image d-flex align-items-center flex-row">
                                <img src="{{ asset('assets/media/star.png') }}" alt="View Group" class="icon">
                                <h3 class="lead mb-0 main-heading text-nowrap ms-2">Total Earned Points</h3>
                            </div>
                            <div class="post_image">
                                <img src="{{ asset('assets/media/amazon.png') }}" alt="View Group" class="icon">
                            </div>
                        </div>
                        <div class="center-content d-flex justify-content-center align-items-center" style="height: 130px;">
                            <h2 class="mb-0 points-display">{{ $reward_balance }}</h2>
                        </div>
                        <div class="imagesection d-flex justify-content-center">
                            <form method="POST" action="{{ route('redeem-rewards') }}" class="w-100">
                                @csrf
                                <input type="hidden" name="redeem_coins" value="{{ $reward_balance }}">
                                <button type="submit" class="btn rewards-buttons lastbtn submit_btn w-100">Redeem Points</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-xl-4 col-md-6">
                <div class="item">
                    <div class="pad15" style="height:280px">
                        <div class="innerheader">
                            <div class="post_image d-flex align-items-center flex-row">
                                <img src="{{ asset('assets/media/star.png') }}" alt="View Group" class="icon">
                                <h3 class="lead mb-0 main-heading text-nowrap ms-2">Send Gift Points</h3>
                            </div>
                            <div class="post_image">
                                <img src="{{ asset('assets/media/amazon.png') }}" alt="View Group" class="icon">
                            </div>
                        </div>
                        <div class="center-content d-flex align-items-center pt-4" style="height:130px">
                            <img src="{{ asset('assets/media/gift-points.png') }}" style="height:116px" alt="Img Missing" class="icon">
                        </div>
                        <div class="imagesection d-flex justify-content-center">
                            <button onclick="openmodal()" class="btn rewards-buttons lastbtn submit_btn w-100">Search Dix Member</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-lg-12">
                <div class="item">
                    <div class="pad15" style="height:280px">
                        <div class="innerheader">
                            <div class="post_image d-flex align-items-center flex-row">
                                <img src="{{ asset('assets/media/star.png') }}" alt="View Group" class="icon">
                                <h3 class="lead mb-0 main-heading text-nowrap ms-2">Purchase points</h3>
                            </div>
                            <div class="post_image">
                                <img src="{{ asset('assets/media/amazon.png') }}" alt="View Group" class="icon">
                            </div>
                        </div>

                        <div class="imagesection purchase-points d-flex flex-column justify-content-between" style="gap:10px">
                            @foreach($rewards_prices as $reward)
                                <form method="POST" action="{{ route('purchase-points') }}" class="w-100">
                                    @csrf
                                    <input type="hidden" name="points" value="{{ $reward->coins }}">
                                    <input type="hidden" name="price" value="{{ $reward->price }}">
                                    <input type="hidden" name="package_id" id="package_id" value="{{ $reward->id }}">
                                    <button type="submit" class="btn justify-content-between align-items-center submit_btn">
                                        {{ $reward->name }} <span class="price">${{ $reward->price }}</span>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="dixwix_modal1" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body" id="modal_body">
                <div class="container mt-5">
                    <div class="form-group">
                        <label for="search_user">Search User</label>
                        <div class="input-group">
                            <input type="text" id="search_user" class="form-control" placeholder="Enter name or email to search" />
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <label for="user_select">Select User</label>
                        <select id="user_select" class="form-control" disabled>
                            <option value="">No users found</option>
                        </select>
                    </div>
{{--                    <div class="form-group mt-3">--}}
{{--                        <label for="user_points">Enter Points</label>--}}
{{--                        <input type="number" id="user_points" class="form-control" min="0" placeholder="Enter points to assign" disabled />--}}
{{--                    </div>--}}
{{--                    <div class="form-group mt-3">--}}
{{--                        <input type="checkbox" name="gifto-checkbox" id="gifto_checkbox" onclick="javascript:$('.gifto_data_div').toggle('slow')" />--}}
{{--                        <label for="gifto_checkbox">Also Send Gifto</label>--}}
{{--                    </div>--}}

                    <div class="row gifto_data_div">
                        <div class="form-group col-12 mt-3">
                            @if(count($campaigns["data"]["data"])> 0)
                                @foreach($campaigns["data"]["data"] as $campaign)
                                    <p>
                                        <input type="radio" name="comp" id="comp-{!! $campaign["id"] !!}" value="{!! $campaign["id"] !!}" checked />
                                        {!! $campaign["name"] !!}
                                    </p>
                                @endforeach
                            @endif
                        </div>

                        <div class="form-group col-12 mt-3">
                            <label for="gifto_msg">Gifto Message</label>
                            <input type="text" id="gifto_msg" name="gifto_msg" class="form-control" placeholder="Thanks from the team for an awesome year!" />
                        </div>
                        <div class="form-group col-7 mt-3">
                            <label for="gifto_amount">Points</label>
{{--                            <input type="number" id="gifto_amount" min="5" max="{!! $reward_balance/100 !!}" onchange="javascript:$('.peice').text({!! round($reward_balance/100, 2) !!} - ($(this).val()))" step="5" name="gifto_amount" value="5" class="form-control" placeholder="Max limit {!! $reward_balance/100 !!}" />--}}
                            <input type="number" id="gifto_amount" min="500" max="{!! $reward_balance !!}" step="500" onchange="javascript:validateInput(this);" name="gifto_amount" value="500" class="form-control" placeholder="Max limit {!! $reward_balance !!}" />
                            <sub class="red-msg">Points must be multiple of 500.</sub>
                        </div>
                        <div class="form-group col-4">
                            <div class="a-bal"> $ <span class="peice">5</span> <sub>Amount accept by gifto</sub></div>
{{--                            <div class="a-bal"><sub>Must have more then 500 points to send card</sub></div>--}}
                        </div>
                    </div>
                </div>
                <button class="btn btn-success" id="assign_button" disabled>Assign Points</button>
                <button id="close-modal" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openmodal() {
        let userSelect = $('#user_select');
        userSelect.empty();
        userSelect.append(
            `<option value="">Select User</option>`
        );
        if (!{!! $reward_balance !!} || {!! $reward_balance !!} <= 500) {
            Swal.fire({
                title: "Insufficient Points",
                text: "You do not have enough points to send a gift. Please purchase additional points to proceed.",
                icon: "error",
                confirmButtonText: "OK"
            });
            return;
        } else {
            jQuery('#dixwix_modal1').modal('show');
        }
    }

    /******* Logic for Multiple of 500 *******/
    function validateInput(input) {
        const value = parseInt(input.value, 10);
        const step = 500;
        const min = parseInt(input.min, 10);
        const max = parseInt(input.max, 10);

        // Ensure max is a multiple of 500
        const adjustedMax = Math.floor(max / step) * step;

        // Check if the value is a multiple of 500
        if (value % step !== 0) {
            // If not, round it to the nearest multiple of 500
            const roundedValue = Math.round(value / step) * step;

            // Ensure the rounded value is within the min and adjusted max limits
            if (roundedValue < min) {
                input.value = min;
            } else if (roundedValue > adjustedMax) {
                input.value = adjustedMax;
                showAlert();
            } else {
                input.value = roundedValue;
            }
        }

        // Ensure the value does not exceed the adjusted max limit
        if (value > adjustedMax) {
            input.value = adjustedMax;
            showAlert();
        }

        // Update the displayed value
        $('.peice').text(input.value / 100);
    }

    function showAlert() {
        Swal.fire({
            title: "Insufficient Points",
            text: "You do not have enough points for this conversion. Please purchase additional points to proceed.",
            icon: "warning",
            confirmButtonText: "OK"
        });
    }
    /******* Logic for Multiple of 500 *******/

    $(document).ready(function() {
        $('#search_user').on('input', function() {
            let searchQuery = $(this).val().trim();

            if (!searchQuery) {
                alert("Please enter a search query.");
                return;
            }

            $.ajax({
                url: "{{ url('find-users') }}"
                , method: 'GET'
                , data: {
                    search_user: searchQuery
                , }
                , success: function(response) {
                    if (response.success && response.data.length > 0) {
                        let userSelect = $('#user_select');
                        userSelect.empty();
                        userSelect.append(
                            `<option value="">Select User</option>`
                        );
                        $.each(response.data, function(index, user) {
                            userSelect.append(
                                // `<option value="${user.id}">${user.name} (${user.email})</option>`
                                `<option value="${user.id}">${user.email}</option>`
                            );
                        });
                        userSelect.prop('disabled', false);
                        $('#user_points').prop('disabled', false);
                        $('#assign_button').prop('disabled', false);
                    } else {
                        $('#user_select').html('<option value="">No users found</option>').prop('disabled', true);
                        $('#user_points').val('').prop('disabled', true);
                        $('#assign_button').prop('disabled', true);
                    }
                }
                , error: function() {
                    console.error("An error occurred while fetching users.");
                }
            , });
        });

        $('#assign_button').on('click', function() {
            let selectedUser = $('#user_select').val();
            // let points = $('#user_points').val();
            let points = $('#gifto_amount').val();
            // let is_gifto = $('#gifto_checkbox').prop('checked') ? 1 : 0; // Ensure it sends 1 or 0
            let is_gifto = 1; // Ensure it sends 1 or 0
            let gifto_msg = $('#gifto_msg').val();
            let gifto_price = $('#gifto_amount').val();
            let comp = $('input[name="comp"]').val();

            if (!selectedUser) {
                Swal.fire({
                    title: "Error"
                    , text: "Please select a user."
                    , icon: "error"
                , });
                return;
            }
            if (!points || points <= 0) {
                Swal.fire({
                    title: "Error"
                    , text: "Please enter a valid number of points."
                    , icon: "error"
                , });
                return;
            }

            $.ajax({
                url: "{{ url('assign-points') }}"
                , method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: selectedUser,
                    points: points,
                    is_gifto: is_gifto,
                    gifto_msg: gifto_msg,
                    gifto_price: gifto_price,
                    comp: comp
                , }
                , success: function(response) {
                    if (response.success === true) {
                        Swal.fire({
                            title: "Success"
                            , text: response.message
                            , icon: "success"
                        , }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error"
                            , text: response.message
                            , icon: "error"
                        , });
                    }
                }
                , error: function() {
                    Swal.fire({
                        title: "Error"
                        , text: "Something went wrong. Please try again."
                        , icon: "error"
                    , });
                    console.error("An error occurred while assigning points.");
                }
            , });
        });

        // let $pointsInput = $("#user_points");
        // // Restrict input value to the max limit
        // $pointsInput.on("input", function () {
        //     let maxLimit = parseInt($pointsInput.attr("max")); // Get dynamic max value
        //     let enteredValue = parseInt($pointsInput.val());
        //
        //     if (enteredValue > maxLimit) {
        //         $pointsInput.val(maxLimit); // Reset value to max limit if exceeded
        //     }
        //
        //     // Enable the button only if a valid number is entered
        //     $assignButton.prop("disabled", !enteredValue || enteredValue <= 0);
        // });
    });
</script>

@if(!empty($client_secret))
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ env('STRIPE_KEY') }}");
    const elements = stripe.elements();
    const payment_intent = '{!! $payment_intent_id !!}';
    const cardElement = elements.create("card", {
        hidePostalCode: true
    });
    cardElement.mount("#card-element");

    cardElement.addEventListener("change", function(event) {
        var displayError = document.getElementById("card-errors");
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = "";
        }
    });

    document.querySelectorAll('.payment-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('new-card-section').classList.add('d-none');
            document.getElementById('toggle-new-card').checked = false;
            document.getElementById('save-card').checked = false;
        });
    });

    document.getElementById('toggle-new-card')?.addEventListener('change', function() {
        if (this.checked) {
            document.getElementById('new-card-section').classList.remove('d-none');
            document.getElementById('save-card').checked = false;
        } else {
            document.getElementById('new-card-section').classList.add('d-none');
        }
    });

    document.getElementById("pay-button").addEventListener("click", function() {
        const selectedMethod = document.querySelector("input[name='selected_payment_method']:checked");
        const useNewCardElement = document.getElementById("toggle-new-card");
        const useNewCard = useNewCardElement ? useNewCardElement.checked : false;
        const saveCard = document.getElementById("save-card").checked;

        // Disable button and show spinner
        document.getElementById("pay-button").disabled = true;
        document.getElementById("button-text").textContent = 'Processing...';
        document.getElementById("loading-spinner").classList.remove("d-none");

        if (selectedMethod && !useNewCard) {
            // Pay using saved card
            $.ajax({
                url: "{{ route('pay-with-saved-card') }}",
                type: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                data: JSON.stringify({ payment_method_id: selectedMethod.value, payment_intent:payment_intent }),
                contentType: "application/json",
                dataType: "json",
                success: function (data) {
                    if (data.success) {
                        Swal.fire({
                            title: "Payment Successful!",
                            text: "Your payment with the saved card has been successfully completed",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = "{{ route('payment-success') }}?payment_intent=" + data.payment_intent_id;
                        });
                    } else {
                        handlePaymentError(data.message || "Payment failed.");
                    }
                },
                error: function (xhr, status, error) {
                    handlePaymentError(xhr.responseJSON?.message || "Payment request failed.");
                }
            });

        } else {
            // Pay using new card
            stripe.confirmCardPayment("{{ $client_secret }}", {
                payment_method: { card: cardElement },
                setup_future_usage: "off_session"
            }).then(result => {
                if (result.error) {
                    throw new Error(result.error.message);
                } else if (result.paymentIntent.status === "succeeded") {
                    let paymentIntentId = result.paymentIntent.id;
                    let paymentMethodId = result.paymentIntent.payment_method;
                    if (saveCard) {
                        $.ajax({
                            url: "{{ route('save-payment-method-only') }}",
                            type: "POST",
                            headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            contentType: "application/json",
                            data: JSON.stringify({ payment_method_id: paymentMethodId }),
                            success: function () {
                                Swal.fire({
                                    title: "Payment Successful!",
                                    text: "Your card has been added successfully for future payments.",
                                    icon: "success",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    window.location.href = "{{ route('payment-success') }}?payment_intent=" + paymentIntentId;
                                });
                            },
                            error: function (xhr, status, error) {
                                console.log("Error:", error);
                                handlePaymentError(error)
                            }
                        });
                    } else {
                        window.location.href = "{{ route('payment-success') }}?payment_intent=" + paymentIntentId;

                        Swal.fire({
                            title: "Payment Successful!",
                            text: "Your payment was completed successfully.",
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = "{{ route('payment-success') }}?payment_intent=" + paymentIntentId;
                        });
                    }
                }
            }).catch(error => handlePaymentError(error));
        }
    });

    {{--document.getElementById("pay-button").addEventListener("click", function() {--}}

    {{--    $("#pay-button").attr("disabled", true);--}}
    {{--    $("#button-text").text('Processing...');--}}
    {{--    $("#loading-spinner").removeClass("d-none");--}}

    {{--    stripe.confirmCardPayment("{{ $client_secret }}", {--}}
    {{--        payment_method: {--}}
    {{--            card: cardElement--}}
    {{--        }--}}
    {{--    }).then(function(result) {--}}
    {{--        if (result.error) {--}}

    {{--            $("#pay-button").attr("disabled", false);--}}
    {{--            $("#button-text").text('Pay Now');--}}
    {{--            $("#loading-spinner").addClass("d-none");--}}
    {{--            document.getElementById("card-errors").textContent = result.error.message;--}}

    {{--            // alert(result.error.message);--}}
    {{--        } else if (result.paymentIntent.status === "succeeded") {--}}
    {{--            const paymentIntentId = result.paymentIntent.id;--}}
    {{--            window.location.href = "{{ route('payment-success') }}?payment_intent=" + paymentIntentId;--}}
    {{--        }--}}
    {{--    }).catch((error) => {--}}
    {{--        $("#pay-button").attr("disabled", false).text('Pay Now');--}}
    {{--        // alert('Something went wrong')--}}
    {{--        document.getElementById("card-errors").textContent = error.message;--}}
    {{--        handlePaymentError(error)--}}
    {{--    })--}}
    {{--});--}}

    function handlePaymentError(error) {
        document.getElementById("card-errors").textContent = error.message;
        document.getElementById("pay-button").disabled = false;
        document.getElementById("button-text").textContent = 'Pay Now';
        document.getElementById("loading-spinner").classList.add("d-none");
    }
</script>
@endif

<style>
    .item>div {
        padding: 20px 20px;
        margin: 10px;
        background: var(--grey-02);
        color: #666;
        border-radius: 10px;
    }

    .post_image {
        text-align: right;
        width: 88px;
    }

    .post_image img {
        width: 24px;
        margin: 0px 5px;
    }

    .points-display {
        font-family: Poppins, sans-serif;
        font-size: 64px;
        font-weight: 600;
        color: #094042;
    }

    .purchase-points .price {
        background: #EEF2F2;
        font-family: Poppins;
        font-size: 18px;
        font-weight: 600;
        line-height: 27px;
        text-align: center;
        text-decoration-skip-ink: none;
        color: red;
        color: #094042;
        padding: 4px 15px;
        border-radius: 4px;
    }

    .rewards-buttons {
        width: 300px !important;
    }


    @media (max-width: 768px) {
        .rewards-buttons {
            width: 100% !important;
        }
    }

</style>
