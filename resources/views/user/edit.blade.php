<div class="container">
    <div class="heading">
        <h2>Edit Profile</h2>
    </div>
    <div class="divider">
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


    <form class="main-form" name="edit-user-form" enctype="multipart/form-data" id="edit-user-form" method="post" action="{{ route('store-profile') }}">
        @csrf
        <div class="form-group">
            <input required type="text" class="form-control" id="user_name" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="User Name">
            @error('name')
            <div class="error_msg text-danger">*{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <input type="email" class="form-control" id="user_email" value="{{ $user->email ?? '' }}" placeholder="Email Address" readonly>
        </div>

        <div class="form-group">
            <input type="text" class="form-control" name="phone" maxlength="20" id="phone" value="{{ old('phone', $user->phone ?? '') }}">
            @error('phone')
            <div id="phone-errors" class="error_msg phone-errors text-danger">*{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group textarea">
            <textarea class="form-control" id="user_biodata" name="biodata" placeholder="Describe Yourself Here">{{ old('biodata', $user->biodata ?? '') }}</textarea>
            @error('biodata')
            <div class="error_msg text-danger">*{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group textarea">
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->address ?? '') }}" placeholder="Enter Address">
            @error('address')
            <div class="error_msg text-danger">*{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col">
                    <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $user->state ?? '') }}" placeholder="Enter State">
                    @error('state')
                    <div class="error_msg text-danger">*{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <input type="text" class="form-control" id="zipcode" name="zipcode" value="{{ old('zipcode', $user->zipcode ?? '') }}" placeholder="Enter Zipcode">
                    @error('zipcode')
                    <div class="error_msg text-danger">*{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">

                @if(!$user->external_id)
                <div class="form-group">
                    <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Current Password">
                    @error('current_password')
                    <div class="error_msg text-danger">*{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="new_password" name="password" placeholder="New Password">
                    @error('password')
                    <div class="error_msg text-danger">*{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" id="confirm_password" name="password_confirmation" placeholder="Confirm New Password">
                    @error('password_confirmation')
                    <div class="error_msg text-danger">*{{ $message }}</div>
                    @enderror
                </div>

                @endif

                <div class="form-group">
                    <h3 class="lead main-heading">Profile Picture</h3>
                    <div class="file-drop-area">
                        @php
                        $userProfilePic = $user->profile_pic ?? null;
                        @endphp
                        <div class="holder">
                            <img id="imgPreview" src="{{ $userProfilePic ? asset("storage/{$userProfilePic}") : asset('img/upload-big-arrow.png') }}" alt="Select Cover Image" style="{{ $userProfilePic ? '' : 'width: 25px' }}">
                            @if (!$userProfilePic)
                            <span style="margin-left: 10px">Select Cover Image</span>
                            @endif
                        </div>
                        <br><br>
                        <input id="photo" class="file-input" type="file" name="profile_pic">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <div class="row">
                        <div id="location-fields" class="col">
                            @if (!empty($user->locations))
                            @foreach (json_decode($user->locations, true) as $index => $location)
                            <div class="location-group">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="locations[]" value="{{ old("locations.$index", $location) }}" placeholder="Enter Location">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-location pr-4 pl-4">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                                @error("locations.$index")
                                <div class="error_msg text-danger">*{{ $message }}</div>
                                @enderror
                            </div>
                            @endforeach
                            @else
                            <div class="location-group">
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="locations[]" placeholder="Enter Location" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger remove-location pr-4 pl-4">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    @error('locations.0')
                                    <div class="error_msg text-danger">*{{ $message }}</div>
                                    @enderror
                                    @error('locations.0')
                                    <div class="error_msg text-danger">*{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col"></div>
                        <div class="col text-right">
                            <button type="button" class="btn btn-secondary pr-3 pl-3" id="add-location-btn">Add More</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex mt-2">
            <div class="form-group">
                <button type="submit" class="btn lastbtn submit_btn">Submit</button>
            </div>
            <div class="form-group ml-3">
                <a href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to delete your account?','question',function(){deleteAccount()})" class="btn lastbtn text-nowrap submit_btn" style="background:#dc3545 !important; border-color:#dc3545 !important;">
                    <i class="fa fa-trash" aria-hidden="true"></i> Delete Account
                </a>
            </div>
        </div>

        @if (isset($err_message))
        <div class="error_msg text-danger">*{{ $err_message }}</div>
        @endif
    </form>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this location?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>

<script>
    $(document).ready(function() {
        const phoneInputField = document.querySelector("#phone");
        const iti = window.intlTelInput(phoneInputField, {
            initialCountry: "auto"
            , geoIpLookup: function(callback) {
                $.get("https://ipinfo.io/json?token=c6edd5b7da2c96", function(response) {
                    callback(response.country);
                }).fail(function() {
                    callback("us");
                });
            }
            , utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
        });

        phoneInputField.addEventListener("countrychange", function() {
            const countryCode = iti.getSelectedCountryData().dialCode;
            phoneInputField.value = `+${countryCode}`;
        });

        $("form").on("submit", function(e) {
            const isPhoneValid = iti.isValidNumber();
            const fullPhoneNumber = iti.getNumber();

            if (!isPhoneValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error'
                    , title: 'Invalid Phone Number'
                    , text: 'Please enter a valid phone number in the correct format.'
                    , confirmButtonText: 'OK'
                });
            } else {
                $("#phone").val(fullPhoneNumber);
            }
        });
    });

    function deleteAccount() {
        $.ajax({
            url: "{{ route('delete-account') }}"
            , method: 'POST'
            , data: {
                _token: "{{ csrf_token() }}"
            }
            , success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Deleted!'
                        , text: `Your account has been deleted.`
                        , icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!'
                        , text: response.message || 'Could not delete the user.'
                        , icon: 'error'
                    });
                }
            }
            , error: function() {
                Swal.fire({
                    title: 'Error!'
                    , text: 'An unexpected error occurred.'
                    , icon: 'error'
                });
            }
        });
    }

    $(document).ready(() => {
        const photoInp = $("#photo");
        let file;

        photoInp.change(function(e) {
            file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    $(".holder span").hide();
                    $("#imgPreview").attr("src", event.target.result);
                    $("#imgPreview").removeAttr('style');
                };
                reader.readAsDataURL(file);
            }
        });

        function addLocationField() {
            const locationFields = document.getElementById('location-fields');
            const newLocationGroup = document.createElement('div');
            newLocationGroup.className = 'location-group';
            newLocationGroup.innerHTML = `
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="locations[]" placeholder="Enter Location" required>
                    <div class="input-group-append">
                        <button type="button" class="btn btn-danger remove-location pr-4 pl-4"><i class="fa fa-trash" aria-hidden="true"></i></button>
                    </div>
                </div>
            `;
            newLocationGroup.querySelector('.remove-location').addEventListener('click', function() {
                confirmRemoveLocation(this);
            });
            locationFields.appendChild(newLocationGroup);
        }

        function confirmRemoveLocation(button) {
            $('#confirmModal').modal('show');
            $('#confirmDeleteBtn').off('click').on('click', function() {
                removeLocationField(button);
                $('#confirmModal').modal('hide');
            });
        }

        function removeLocationField(button) {
            button.closest('.location-group').remove();
        }

        document.getElementById('add-location-btn').addEventListener('click', function() {
            addLocationField();
        });

        document.querySelectorAll('.remove-location').forEach(button => {
            button.addEventListener('click', function() {
                confirmRemoveLocation(this);
            });
        });
    });

</script>


<style>
    .iti--allow-dropdown {
        width: 100% !important;
    }
</style>
