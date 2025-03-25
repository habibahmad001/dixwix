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
            <div class="card-body pad15" style="height:200px">
                <div id="payment-form">
                    <div class="post_image d-flex align-items-center flex-row">
                        <h3 class="lead mb-0 main-heading text-nowrap ms-2">You're purchasing {{ $points }} points for ${{ $price }}</h3>
                    </div>
                    <div id="card-element" class="mt-4"></div>
<!--                    <button id="pay-button" class="btn rewards-buttons lastbtn submit_btn w-100 mt-5">Pay Now</button>-->
                    <button id="pay-button" class="btn rewards-buttons lastbtn submit_btn w-100 mt-5">
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
                                <img src="assets/media/star.png" alt="View Group" class="icon">
                                <h3 class="lead mb-0 main-heading text-nowrap ms-2">Total Earned Points</h3>
                            </div>
                            <div class="post_image">
                                <img src="assets/media/amazon.png" alt="View Group" class="icon">
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
                                <img src="assets/media/star.png" alt="View Group" class="icon">
                                <h3 class="lead mb-0 main-heading text-nowrap ms-2">Send Gift Points</h3>
                            </div>
                            <div class="post_image">
                                <img src="assets/media/amazon.png" alt="View Group" class="icon">
                            </div>
                        </div>
                        <div class="center-content d-flex align-items-center pt-4" style="height:130px">
                            <img src="assets/media/gift-points.png" style="height:116px" alt="Img Missing" class="icon">
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
                                <img src="assets/media/star.png" alt="View Group" class="icon">
                                <h3 class="lead mb-0 main-heading text-nowrap ms-2">Purchase points</h3>
                            </div>
                            <div class="post_image">
                                <img src="assets/media/amazon.png" alt="View Group" class="icon">
                            </div>
                        </div>

                        <div class="imagesection purchase-points d-flex flex-column justify-content-between" style="gap:10px">
                            @foreach($rewards_prices as $reward)
                            <form method="POST" action="{{ route('purchase-points') }}" class="w-100">
                                @csrf
                                <input type="hidden" name="points" value="{{ $reward->coins }}">
                                <input type="hidden" name="price" value="{{ $reward->price }}">
                                <input type="hidden" name="package_id" value="{{ $reward->id }}">
                                <button type="submit" class="btn rewards-buttons d-flex justify-content-between align-items-center lastbtn submit_btn w-100">
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
                    <div class="form-group mt-3">
                        <label for="user_points">Enter Points</label>
                        <input type="number" id="user_points" class="form-control" min="0" placeholder="Enter points to assign" disabled />
                    </div>
                    <div class="form-group mt-3">
                        <button class="btn btn-success" id="assign_button" disabled>Assign Points</button>
                    </div>
                </div>
                <button id="close-modal" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function openmodal() {
        jQuery('#dixwix_modal1').modal('show');
    }
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
            let points = $('#user_points').val();

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
                , method: 'POST'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , user_id: selectedUser
                    , points: points
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
    });

</script>

@if(!empty($client_secret))
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ env('STRIPE_KEY') }}");
    const elements = stripe.elements();
    // const cardElement = elements.create("card");
    const cardElement = elements.create("card", {
        hidePostalCode: true
    });
    cardElement.mount("#card-element");

    document.getElementById("pay-button").addEventListener("click", function() {
        $("#pay-button").attr("disabled", true);
        $("#button-text").text('Processing...');
        $("#loading-spinner").removeClass("d-none");

        stripe.confirmCardPayment("{{ $client_secret }}", {
            payment_method: {
                card: cardElement
            }
        }).then(function(result) {
            if (result.error) {

                $("#pay-button").attr("disabled", false);
                $("#button-text").text('Pay Now');
                $("#loading-spinner").addClass("d-none");

                alert(result.error.message);
            } else if (result.paymentIntent.status === "succeeded") {
                const paymentIntentId = result.paymentIntent.id;
                window.location.href = "{{ route('payment-success') }}?payment_intent=" + paymentIntentId;
            }
        }).catch((error) => {
            $("#pay-button").attr("disabled", false).text('Pay Now');
            alert('Something went wrong')
        })
    });

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
