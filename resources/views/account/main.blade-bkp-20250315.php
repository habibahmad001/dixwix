<div class="content">
    <div class="container">
        <div class="heading mb-4">
            <h2>{{ $data['title'] }}</h2>
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

        <div class="plans-container row">
    <!-- Current Plan -->
    <div class="col-md-4">
        @if($current_plan)
        <div class="card current-plan-card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-success">{{ $current_plan->plan->name }}</h5>
                <p class="card-text">
                    <strong>Start Date:</strong> {{ $current_plan->start_date }}<br>
                    <strong>End Date (Next Recurring):</strong> {{ $current_plan->end_date ?? 'N/A' }}<br>
                    <strong>Status:</strong>
                    <span class="badge badge-success">Active</span><br>
                    <strong>Price:</strong> {{ $current_plan->plan->name == 'Basic' ? 'Free' : '$' . $current_plan->plan->price . '/Month' }}<br>
                    <strong>Allowed Groups:</strong> {{ $current_plan->plan->allowed_groups }}<br>
                    <strong>Allowed Items:</strong> {{ $current_plan->plan->allowed_items }}
                </p>
                <button class="btn btn-secondary w-100" disabled>Current Plan</button>
            </div>
        </div>
        @else
        <div class="card current-plan-card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title text-muted">No Current Plan</h5>
                <p class="card-text">
                    You don't have an active plan. Please select a plan to get started.
                </p>
            </div>
        </div>
        @endif
    </div>

    <!-- Other Plans -->
    @foreach ($plans as $plan)
    <div class="col-md-4">
        <div class="card plan-card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">{{ $plan->name }}</h5>
                <p class="card-text">
                    <strong>Price:</strong> {{ $plan->name == 'Basic' ? 'Free' : '$' . $plan->price . '/Month' }}<br>
                    <strong>Allowed Groups:</strong> {{ $plan->allowed_groups }}<br>
                    <strong>Allowed Items:</strong> {{ $plan->allowed_items }}
                </p>
                @if ($plan->id !== ($current_plan->plan->id ?? null))
                <form id="switch-plan-{{ $plan->id }}" method="POST" action="{{ route('switch-plan') }}">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                    <button type="button" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to switch to {{ $plan->name }} plan?','question',function(){$('#switch-plan-{{ $plan->id }}').submit()})" class="btn btn-primary w-100">
                        Switch to {{ $plan->name }}
                    </button>
                </form>
                @else
                <button class="btn btn-secondary w-100" disabled>Current Plan</button>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

        <div class="content">
            <div class="container">
                <div class="heading mb-4">
                    <h2>Payment Details</h2>
                </div>
                <p class="text-muted">Your saved payment methods.</p>

                <!-- Payment Methods List -->
                <div class="payment-methods-list">
                    @if(!empty($payment_methods))
                    @foreach($payment_methods as $method)
                    <div class="card payment-method-card mb-3">
                        <div class="card-body d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <!-- Card Type Icon -->
                                <img src="{{ asset('assets/media/' . strtolower($method->type) . '.png') }}" alt="{{ ucfirst($method->type) }} Logo" class="card-icon mr-3">

                                <div>
                                    <h5 class="mb-1 text-nowrap">**** **** **** {{ $method->last4 }} @if($method->default)
                                        <span class="badge badge-success mr-2">Primary</span>
                                        @endif</h5>
                                    <p class="mb-0 text-muted">Expiration {{ $method->expiry_month }}/{{ $method->expiry_year }}</p>
                                </div>
                            </div>

                            <div class="action-buttons">
                                @if(!$method->default)
                                <form method="POST" id="make-default-payment-method-{{ $method->id }}" action="{{ route('make-default-payment-method') }}" class="d-inline-block">
                                    @csrf
                                    <input type="hidden" name="payment_method_id" value="{{ $method->id }}">
                                    <button type="button" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to make this as the default payment method?','question',function(){$('#make-default-payment-method-{{ $method->id }}').submit()})" class="btn btn-sm badge bg-warning text-white btn-link">Make Primary</button>
                                </form>
                                @endif

                                <form method="POST" id="remove-payment-method-{{ $method->id }}" action="{{ route('remove-payment-method') }}" class="d-inline-block">
                                    @csrf
                                    <input type="hidden" name="payment_method_id" value="{{ $method->id }}">
                                    <button type="button" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to delete this payment method?','question',function(){$('#remove-payment-method-{{ $method->id }}').submit()})" class="btn d-flex flex-row border-danger border-1 py-2 btn-sm btn-link text-danger"> <img src="{{ asset('assets/media/delete.png') }}"> Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-center text-muted">No payment methods added.</p>
                    @endif
                </div>

                <button type="button" class="btn btn-primary mt-4" data-toggle="modal" data-target="#addPaymentMethodModal">
                    + Add new payment method
                </button>
            </div>
        </div>
    </div>
</div>
</div>

<div class="modal fade" id="addPaymentMethodModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentMethodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content p-4">
            <div class="modal-header d-flex justify-content-center border-0">
                <h5 class="modal-title" id="editPaymentMethodModalLabel">Card Details</h5>
            </div>

            <div class="modal-body">
                <p class="text-muted text-center mb-4">
                    Your payment details are securely processed using <strong>Stripe</strong>, one of the most trusted and widely used payment gateways worldwide.
                    Stripe ensures your sensitive information is encrypted and never stored on our servers.
                </p>

                <form method="POST" action="{{ route('save-payment-method') }}" id="payment-method-form">
                    @csrf
                    <div id="payment-card-element" class="my-5 shadow py-3 px-2"></div>
                    <div class="form-row justify-content-center" style="gap:5px">
                        <button type="button" class="btn btn-light" style="background:#D94E291A" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submit-button">
                            <span id="button-text">Save Changes</span>
                            <span id="loading-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    $(document).ready(function() {
        const stripe = Stripe("{{ env('STRIPE_KEY') }}");
        const elements = stripe.elements();
        // const cardElement = elements.create("card");
        const cardElement = elements.create("card", {
            hidePostalCode: true
        });
        cardElement.mount("#payment-card-element");

        $("#payment-method-form").on("submit", function(e) {
            e.preventDefault();

            $("#submit-button").prop("disabled", true);
            $("#button-text").text("Processing...");
            $("#loading-spinner").removeClass("d-none");

            stripe.createPaymentMethod({
                type: "card"
                , card: cardElement
            , }).then(function(result) {
                if (result.error) {
                    alert(result.error.message);

                    // Hide spinner and re-enable button
                    $("#submit-button").prop("disabled", false);
                    $("#button-text").text("Save Changes");
                    $("#loading-spinner").addClass("d-none");

                } else {
                    $("<input>").attr({
                        type: "hidden"
                        , name: "payment_method_id"
                        , value: result.paymentMethod.id
                    , }).appendTo("#payment-method-form");
                    $("#payment-method-form")[0].submit();
                }
            });
        });
    });

</script>

<style>
.payment-methods-list {
    margin-top: 20px;
}

.payment-method-card {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.payment-method-card .card-icon {
    width: 40px;
    height: auto;
    margin-right: 15px;
}

.action-buttons {
    display:flex;
    align-items:center;
}

.payment-method-card .action-buttons button {
    margin-right: 10px;
    font-size: 14px;
}

.payment-method-card .badge-success {
    background-color: #28a745;
    color: #fff;
    padding: 5px 10px;
    font-size: 12px;
    border-radius: 8px;
}

.text-danger {
    color: #d9534f !important;
}

.text-danger:hover {
    text-decoration: underline;
}


.plans-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.card {
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.current-plan-card {
    border: 2px solid #28a745;
}

.plan-card {
    border: 1px solid #ddd;
}

.card-title {
    font-size: 1.25rem;
    font-weight: 600;
}

.card-text {
    font-size: 0.9rem;
    color: #555;
    min-height:130px;
}

</style>
