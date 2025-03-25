<style>
    .large-radio {
        transform: scale(1.2);
        margin-left: 10px;
    }

</style>
<?php if (isset($retdata)) {
    extract($retdata);
    $userGroupLocations = [];
} ?>
<div class="container">
    <div class="heading">
        <h2>{{ $data['title'] }}</h2>
    </div>
    <div class="divider">
        <hr>
    </div>

    @if(isset($success))
    <div class="alert alert-success">
        {{ $success }}
    </div>
    @endif

    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
    @endif

    @if(isset($group) && isset($group['to_be_deleted_at']))
    <div class="alert alert-warning">
        This group is marked for deletion and will be deleted on {{ $group->to_be_deleted_at ?? 'N/A' }}.
    </div>
    @endif

    <?php if(isset($group_limit_reached)) { ?>
    <div class="item search-result">
        <div class="text404">
            <img src="{{ url('assets/media/error 1.png') }}" alt="No Group Joined">
            <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">Group Creation Limit
                Reached Upgrade <a href="{{ route('my-account') }}">Membership</a> to create further groups</p>
        </div>
    </div>
    <?php } else { ?>
    <form class="main-form" name="add-group-form" enctype="multipart/form-data" id="add-group-form" method="post" action="{{ route('store-group') }}">
        @csrf
        <input type="hidden" name="mode" value="{{ $mode }}">
        <input type="hidden" name="group_id" value="{{ isset($group_id) ? $group_id : '' }}">
        <div class="form-group">
            <h3 class="lead main-heading">Title <span class="text-danger">*</span></h3>
            <input required type="text" class="form-control" id="title" name="group[title]" value="{{ isset($group) ? $group['title'] : '' }}" placeholder="Group Title">
            @error('group.title')
                <div class="error_msg text-danger">*{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group textarea">
            <h3 class="lead main-heading">Description <!-- <span class="text-danger">*</span> --></h3>
            <textarea type="textarea" class="form-control" id="description" name="group[description]" placeholder="Describe your group here">{{ isset($group) ? $group['description'] : '' }}</textarea>
            @error('group.description')
                <div class="error_msg text-danger">*{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="lead main-heading">State <span class="text-danger">*</span></h3>
                    <input type="text" class="form-control" id="state" required name="group[state]" value="{{ isset($group) ? $group['state'] : '' }}" placeholder="Enter State">
                    @if (isset($errs['state']))
                    <div class="error_msg">*{{ $errs['state'] }}</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <h3 class="lead main-heading">Zip Code</h3>
                    <input type="text" class="form-control" id="zip_code" name="group[zip_code]" value="{{ isset($group) ? $group['zip_code'] : '' }}" placeholder="Enter Zipcode">
                    @if (isset($errs['zip_code']))
                    <div class="error_msg">*{{ $errs['zip_code'] }}</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="lead main-heading">Country <span class="text-danger">*</span></h3>
                    <select class="form-control" name="group[country]" required>
                        <option value="">Please Select Country</option>
                        @foreach ($data['countries'] as $country)
                        <option {{ isset($group['country']) && $group['country'] == $country->country_name?'selected':'' }} value="{{ $country->country_name }}">
                            {{ $country->country_name }}
                        </option>
                        @endforeach
                    </select>
                    @if (isset($errs['country']))
                    <div class="error_msg">*{{ $errs['country'] }}</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div>
                        @php

                            if ($mode === 'edit' && !empty($group['created_by'])) {
                                $user = \App\Models\User::find($group['created_by']);
                                if ($user && !empty($user->group_locations)) {
                                    $userGroupLocations = json_decode($user->group_locations, true) ?? [];
                                }
                            } elseif (auth()->check() && !is_null(auth()->user()->group_locations)) {
                                $userGroupLocations = json_decode(auth()->user()->group_locations, true) ?? [];
                            }
                        @endphp
                        <h3 class="lead main-heading">Group Location <!-- <span class="text-danger">*</span> -->
                            <span class="text-danger">
                                @if (empty($userGroupLocations))
                                    <!-- Please update your group location first. -->
                                @endif
                            </span>
                        </h3>
                        <select class="form-control selectpicker allinput" id="locations" name="group[locations][]" multiple>
                            @php
                            $isFirstOption = true;
                            foreach ($userGroupLocations as $location) :
                                $selected = "";
                                if ($mode == "edit" && !empty($group)) {
                                    $locationsArray = is_string($group['locations'])
                                        ? json_decode($group['locations'], true)
                                        : (is_array($group['locations']) ? $group['locations'] : []);
                                    $selected = in_array($location, $locationsArray) ? 'selected' : '';
                                } elseif ($isFirstOption) {
                                    $selected = 'selected';
                                }
                                $isFirstOption = false;
                                @endphp
                            <option value="{{ $location }}" {{ $selected }}>{{ $location }}</option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group mt-3">
                            <input type="text" class="form-control" id="new_location" placeholder="Add a new group location" />
                            <button type="button" id="add_location_btn" class="btn btn-primary">Add Group Location</button>
                        </div>
                        <div id="error_locations" class="error_msg"></div>
                        @error('group.locations')
                            <div class="error_msg text-danger">*{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <h3 class="lead main-heading">Status <span class="text-danger">*</span></h3>
            <input type="radio" name="group[status]" value="1" {{ !isset($group['status']) ? 'checked' : '' }} {{ isset($group['status']) && $group['status']==1?'checked':'' }} class="large-radio" required> Public
            <input type="radio" name="group[status]" value="0" {{ isset($group['status']) && $group['status']==0?'checked':'' }} class="large-radio" required> Private
            <div class="form-group mt-3">
                <h3 class="lead main-heading">Group Picture</h3>
                <span id="cover_page_span">
                    <div class="lasfrm-sec">
                        <div class="file-drop-area">
                            <?php
                        if($mode == "edit") {
                            $groupImageURL = isset($group['group_picture']) ? asset('storage/' . $group['group_picture']) : "";
                        }else{
                            $groupImageURL = "";
                        }
                        ?>
                            <div class="holder">
                                <img id="imgPreview" src="{{ $groupImageURL? $groupImageURL : asset('img/upload-big-arrow.png') }}" alt="Select Cover Image" {{ !$groupImageURL ? 'style="width: 25px"' : "" }} />
                                {!! !$groupImageURL ? "<span style='margin-left: 10px'>Select Cover Image</span>": "" !!}
                            </div>
                            <br>
                            <br>
                            <input {{ $mode == "edit" ? '' : '' }} id="photo" class="file-input" type="file" accept=".jpeg,.jpg,.png" name="group_picture" value="{{ $groupImageURL }}" />
                        </div>
                    </div>
                </span>
                 @error('group_picture')
                    <div class="error_msg text-danger">*{{ $message }}</div>
                @enderror
            </div>
            <div class="d-flex flex-column flex-md-row" style="gap:5px">
                <div class="form-group">
                    <button id="save-form-button" type="submit" class="btn lastbtn submit_btn">Submit</button>
                </div>
                @if(isset($group) && !isset($group['to_be_deleted_at']) && isset($group['id']))
                <div class="form-group">
                    <a href="javascript:void(0)" onclick="showSwalMessageWithCallback('Confirmation','Are you sure you want to Delete Group?','question',function(){deleteGroup('{{ $group['id']  }}', '{{ route('delete-group')  }}')})" class="btn lastbtn submit_btn" style="background:#dc3545 !important; border-color:#dc3545 !important;">
                        <i class="fa fa-trash" aria-hidden="true"></i> Delete Group
                    </a>
                </div>
                @endif
            </div>
            @if (isset($err_message))
            <div class="error_msg">*{{ $err_message  }}</div>
            @endif
    </form>
    <?php } ?>
</div>
<script>
    $(document).ready(() => {
        const photoInp = $("#photo");
        let file;

        photoInp.change(function(e) {
            file = this.files[0];
            if (file) {
                let reader = new FileReader();
                reader.onload = function(event) {
                    $(".holder span").hide();
                    $("#imgPreview")
                        .attr("src", event.target.result);
                    $("#imgPreview").removeAttr('style');
                };
                reader.readAsDataURL(file);
            }
        });

        $(document).on("submit", "#add-group-form", function(e) {
            const saveButton = $("#save-form-button");
            if (!saveButton.prop("disabled")) {
                saveButton.attr("disabled", true).text("Processing...");
            }
        });

    });

   $(document).ready(function () {
    let userGroupLocations = @json($userGroupLocations);
    let locationsArray = @json(isset($locationsArray) ? $locationsArray : []);

    // Function to populate the dropdown
    const populateDropdown = () => {
        const locationsSelect = $('#locations');
        locationsSelect.empty();

        // Add "Community" at the beginning of the locations if it's not already there
        if (!userGroupLocations.includes("Community")) {
            userGroupLocations.unshift("Community"); // Add "Community" at the beginning
        }

        let isCommunitySelected = false;

        userGroupLocations.forEach(location => {
            // Check if "Community" is selected, otherwise set it as selected by default
            let isSelected = locationsArray.includes(location) || (!isCommunitySelected && location === "Community");
            
            if (location === "Community" && !isCommunitySelected) {
                isCommunitySelected = true; // Mark that "Community" has been selected
            }

            $('<option></option>')
                .val(location)
                .text(location)
                .prop('selected', isSelected) // Set "selected" property
                .appendTo(locationsSelect);
        });

        // Refresh the Bootstrap Select plugin to reflect changes
        locationsSelect.selectpicker('refresh');
    };

    // Populate the dropdown on page load
    populateDropdown();

    // Add location button click event
    $('#add_location_btn').on('click', function () {
        const newLocation = $('#new_location').val().trim();

        if (newLocation === "") {
            alert('Location cannot be empty!');
            return;
        }

        if (userGroupLocations.includes(newLocation)) {
            alert('This location already exists!');
            return;
        }

        // Add the new location to the backend and update the dropdown
        $.ajax({
            url: '/add-group-location',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                location: newLocation,
            },
            success: function (response) {
                if (response.success) {
                    userGroupLocations.push(newLocation); // Add to the locations list
                    locationsArray.push(newLocation); // Add to the selected group list
                    populateDropdown(); // Refresh the dropdown
                    $('#new_location').val(''); // Clear the input field
                    alert('Location added successfully!');
                } else {
                    alert('Failed to add location. Please try again.');
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            },
        });
    });
});



</script>
