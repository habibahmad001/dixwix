@php
if (isset($retdata)) {
    extract($retdata);
}

$userItemLocations = [];

if ($mode == 'edit' && !empty($book['created_by'])) {
    $user = \App\Models\User::find($book['created_by']);
    if ($user) {
        $userItemLocations = json_decode($user->locations, true) ?? [];
    } else {
        $userItemLocations = [];
    }
} elseif (auth()->check() && !is_null(auth()->user()->locations)) {
    $userItemLocations = auth()->user()->locations;
    $userItemLocations = json_decode($userItemLocations);
} else {
    $userItemLocations = [];
}
@endphp

<div class="container">
    <div class="heading">
        <h2>{{ $data['title'] }}</h2>
    </div>
    @if (isset($err_message))
    <div class="error_msg">*{{ $err_message }}</div>
    @endif
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
    @error('csv_url')
    <div class="alert alert-danger">{{ $message }}</div>
    @endif
    @if(session()->has('csv_errors'))
    @php
    $csvErrors = session('csv_errors');
    @endphp
    <div class="alert alert-danger">
        <strong>Some rows failed to import. Please review the detailed errors:</strong>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Row</th>
                    <th>Field</th>
                    <th>Invalid Value</th>
                    <th>Error Message</th>
                </tr>
            </thead>
            <tbody>
                @foreach($csvErrors as $error)
                @foreach($error['field_errors'] as $fieldError)
                <tr>
                    <td>{{ $error['row'] }}</td>
                    <td>{{ $fieldError['field'] }}</td>
                    <td>{{ $fieldError['value'] ?? 'N/A' }}</td>
                    <td>{{ $fieldError['error'] }}</td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @if (isset($item_limit_reached))
    <div class="item search-result">
        <div class="text404">
            <img src="{{ url('assets/media/error 1.png') }}" alt="Max Item Added">
            <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">Item Creation Limit
                Reached Upgrade <a href="{{ route('my-account') }}">Membership</a> to create further Items</p>
        </div>
    </div>
    @else
    <div class="tab d-flex flex-column w-100 flex-md-row">
        <button id="manual_entry_btn" class="tablinks w-100" onclick="openEntryTab(this, 'manual_tab')">Manual entry</button>
        @if ($mode == "add")
        <button id="csv_entry_btn" class="tablinks w-100" onclick="openEntryTab(this, 'csv_tab')">CSV import</button>
        <button id="amazon_entry_btn" class="tablinks w-100" onclick="openEntryTab(this, 'amazon_tab')">Amazon import</button>
        @endif
    </div>

    <div id="manual_tab" class="tabcontent">
        <div class="main-box-amazon">
            <div class="box-form-tab1">
                <form name="add-book-form" id="add-book-form" enctype="multipart/form-data" method="post">
                    @csrf
                    <input type="hidden" name="mode" id="mode" value="{{ $mode }}">
                    <input type="hidden" name="book_id" id="book_id" value="{{ isset($book_id) ? $book_id : '' }}">
                    <div class="form-group">
                        <div class="select3">
                            <div>
                                <h3 class="lead main-heading">Rental ID <span class="text-danger">*</span></h3>
                                @php
                                $item_id = "";
                                if ($retdata['mode'] === "add") {
                                $rand_item_id = $retdata['rand_item_id'] ?? "";
                                $item_id = strtoupper($rand_item_id);
                                } else {
                                $item_id = isset($book) ? $book['item_id'] : "";
                                }
                                @endphp
                                <input type="text" class="form-control allinput" id="item_id" name="book[item_id]" value="{{ $item_id }}" class="form-control" placeholder="Enter the Rental ID here" />
                                <div id="error_item_id" class="error_msg"></div>
                                @if (isset($errs['item_id']))
                                <div class="error_msg">*{{ $errs['item_id'] }}</div>
                                @endif
                            </div>

                            <div>
                                <h3 class="lead main-heading">Select Item Category <span class="text-danger">*</span></h3>
                                <select class="form-control allinput" id="type_id" name="book[type_id]" required>
                                    @foreach ($data["types"] as $type)
                                    <option data-percent="{{ $type['percentage'] }}" value="{{ $type['id'] }}" {{ isset($book) ? ($book['type_id'] === $type['id'] ? 'selected' : '') : '' }}>{{ $type['name'] }}</option>
                                    @endforeach
                                </select>
                                <div id="error_type_id" class="error_msg"></div>
                                @if (isset($errs['type_id']))
                                <div class="error_msg">*{{ $errs['type_id'] }}</div>
                                @endif
                                <p>The category of item are you adding.</p>
                            </div>


                            <div>
                                <h3 class="lead main-heading">Select Group</h3>
                                <select class="form-control allinput" id="group_id" name="book[group_id]">
                                    @foreach($data["groups"] as $group)
                                    <option value="{{ $group['id'] }}" {{ isset($book) ? ($book['group_id'] === $group['id'] ? 'selected' : '') : '' }}>{{ $group['title'] }}</option>
                                    @endforeach
                                </select>
                                <div id="error_group_id" class="error_msg"></div>
                                @if (isset($errs['group_id']))
                                <div class="error_msg">*{{ $errs['group_id'] }}</div>
                                @endif
                                <p>Choose the group you're adding items to.</p>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <h3 class="lead main-heading">Available For</h3>
                                        <select class="form-control allinput" id="" name="book[sale_or_rent]" required>
                                            <option value="rent" {{ isset($book) ? ($book['sale_or_rent'] === 'rent' ? 'selected' : '') : '' }}>Rent</option>
                                        </select>
                                        <div id="error_sale_or_rent" class="error_msg"></div>
                                        @if (isset($errs['sale_or_rent']))
                                        <div class="error_msg">*{{ $errs['sale_or_rent'] }}</div>
                                        @endif
                                        <p>Item will be available for this purpose.</p>
                                    </div>
                                    <div class="col">
                                        <h3 class="lead main-heading">Purchase Price <span class="text-danger">*</span></h3>
                                        <input type="number" class="form-control allinput" id="price" name="book[price]" value="{{ isset($book) ? $book['price'] : '' }}" class="form-control" min=1 placeholder="Price" step=".01" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" />
                                        <div id="error_price" class="error_msg"></div>
                                        @if (isset($errs['price']))
                                        <div class="error_msg">*{{ $errs['price'] }}</div>
                                        @endif
                                    </div>
                                    <div class="col">
                                        <h3 class="lead main-heading">Rent Price <span class="text-danger">*</span></h3>
                                        <input type="number" class="form-control allinput" id="rent_price" name="book[rent_price]" value="{{ isset($book) ? $book['rent_price'] : '' }}" class="form-control" min=1 placeholder="Rent Price" step=".01" />
                                        <div id="error_rent_price" class="error_msg"></div>
                                        @if (isset($errs['rent_price']))
                                        <div class="error_msg">*{{ $errs['rent_price'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="divider">
                        <hr>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col">
                                <h3 class="lead main-heading">Title <span class="text-danger">*</span></h3>
                                <input type="text" class="form-control allinput" id="book_name" name="book[name]" value="{{ isset($book) ? $book['name'] : '' }}" class="form-control" placeholder="Enter the title here" />
                                <div id="error_name" class="error_msg"></div>
                                @if (isset($errs['name']))
                                <div class="error_msg">*{{ $errs['name'] }}</div>
                                @endif
                            </div>
                            <div class="col">
                                <h3 class="lead main-heading text-nowrap">Writer / Author</h3>
                                <input type="text" class="form-control allinput" id="writers" name="book[writers]" value="{{ isset($book) ? $book['writers'] : '' }}" class="form-control" placeholder="Author name" />
                                <div id="error_writers" class="error_msg"></div>
                                @if (isset($errs['writers']))
                                <div class="error_msg">*{{ $errs['writers'] }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="form-group textarea">
                        <h3 class="lead main-heading">Description <span class="text-danger">*</span></h3>
                        <textarea type="textarea" class="form-control" id="description" name="book[description]" placeholder="Write description here">{{ isset($book) ? $book['description'] : '' }}</textarea>
                        <div id="error_description" class="error_msg"></div>
                        @if (isset($errs['description']))
                        <div class="error_msg">*{{ $errs['description'] }}</div>
                        @endif
                    </div>

                    <div>
    <h3 class="lead main-heading">Location <span class="text-danger">*</span>
        <span class="text-danger">
            @if(false&&empty($userItemLocations))
                Please update your profile with location first <a href='/edit-profile'>Edit Profile</a>
            @endif
        </span>
    </h3>
    <select class="form-control selectpicker allinput" id="locations" name="book[locations][]" multiple>
        <!-- Options will be populated via JavaScript -->
    </select>
    <div id="error_locations" class="error_msg"></div>
    @if (isset($errs['locations']))
        <div class="error_msg">*{{ $errs['locations'] }}</div>
    @endif
</div>
<script>
    $(document).ready(function () {
        // Data passed from the controller to the view
        let userItemLocations = @json($userItemLocations);  // List of available locations
        let locationsArray = @json(isset($book) ? json_decode($book['locations'], true) : []);

        // Function to populate the dropdown
        const populateDropdown = () => {
            const locationsSelect = $('#locations');
            locationsSelect.empty();  // Clear any existing options

            let isGeneralSelected = false;  // Flag to track if "General" is selected

            // Loop through each location and add it to the dropdown
            userItemLocations.forEach((location) => {
                let isSelected = locationsArray.includes(location);  // Check if the location is selected

                // If "General" is not selected yet and it's available, set it as the default selection
                if (location === 'Garage' && !isGeneralSelected) {
                    isSelected = true;  // "General" should be selected by default
                    isGeneralSelected = true;  // Mark it as selected
                }

                // Create option element for each location
                $('<option></option>')
                    .val(location)
                    .text(location)
                    .prop('selected', isSelected)
                    .appendTo(locationsSelect);
            });

            // If "General" is not in the list, but should be selected by default, add it manually
            if (!isGeneralSelected) {
                $('<option></option>')
                    .val('Garage')
                    .text('Garage')
                    .prop('selected', true)  // Mark "General" as selected by default
                    .appendTo(locationsSelect);
            }

            // Refresh the Bootstrap Select plugin to ensure it renders properly
            locationsSelect.selectpicker('refresh');
        };

        // Call the function to populate the dropdown on page load
        populateDropdown();
    });
</script>


                    <br>
                    <h3 class="lead main-heading">No. of Copies <span class="text-danger">*</span></h3>
                    <input type="number" class="form-control allinput" id="copies" name="book[copies]" value="{{ isset($book) ? $book['copies'] : 1 }}" class="form-control" min=1 placeholder="No of Copies" />
                    <div id="error_copies" class="error_msg"></div>
                    @if (isset($errs['copies']))
                    <div class="error_msg">*{{ $errs['copies'] }}</div>
                    @endif

                    <h3 class="lead main-heading mt-3">Keywords</h3>
                    <input type="text" class="form-control allinput" id="keyword" name="book[keyword]" placeholder="Enter Keywords" value="{{ isset($book) ? $book['keyword'] : '' }}">
                    <div id="error_keyword" class="error_msg"></div>
                    @if (isset($errs['keyword']))
                    <div class="error_msg">*{{ $errs['keyword'] }}</div>
                    @endif
                    <br>
                    <h3 class="lead main-heading">Manufacturer/Publisher</h3>
                    <input type="text" class="form-control allinput" id="journal_name" name="book[journal_name]" placeholder="Manufacturer/Publisher name" value="{{ isset($book) ? $book['journal_name'] : '' }}">
                    <div id="error_journal_name" class="error_msg"></div>
                    @if (isset($errs['journal_name']))
                    <div class="error_msg">*{{ $errs['journal_name'] }}</div>
                    @endif
                    <br>
                    <h3 class="lead main-heading">EAN ISBN 10/13 / UPC ISBN No</h3>
                    <input type="text" class="form-control allinput" id="ean_isbn_no" name="book[ean_isbn_no]" placeholder="EAN ISBN Number / UPC ISBN Number" value="{{ isset($book) ? $book['ean_isbn_no'] : '' }}">
                    <div id="error_ean_isbn_no" class="error_msg"></div>
                    @if (isset($errs['ean_isbn_no']))
                    <div class="error_msg">*{{ $errs['ean_isbn_no'] }}</div>
                    @endif
                    <br>
                    <div id="error_year" class="error_msg"></div>
                    @if (isset($errs['year']))
                    <div class="error_msg">*{{ $errs['year'] }}</div>
                    @endif
            </div>

            <br>
            <h3 class="lead main-heading">Serial Number</h3>
            <input type="number" class="form-control allinput" id="serial_number" name="book[serial_number]" value="{{ isset($book) ? $book['serial_number'] : '' }}" class="form-control" min=1 placeholder="Serial Number" />
            <div id="error_serial_number" class="error_msg"></div>
            @if (isset($errs['serial_number']))
            <div class="error_msg">*{{ $errs['serial_number'] }}</div>
            @endif
            <br>
            <div>
                <h3 class="lead main-heading"><label for="status">Status</label></h3>

                <div class="form-check form-check-inline m->">
                    <input class="form-check-input form-control" type="radio" name="book[status_options]" id="checkedRadioSwitch" {{ isset($book['status_options']) && $book['status_options'] == 'disable'?'checked':'' }} autocomplete="off" style="width:20px" value="disable" />
                    <div id="error_status_options" class="error_msg"></div>
                    <label class="form-check-label ml-2" for="checkedRadioSwitch">
                        Disabled / Maintainance
                    </label>
                    @if (isset($errs['status']))
                    <div class="error_msg">*{{ $errs['status'] }}</div>
                    @endif
                </div>
                <br>
                <div class="form-check form-check-inline m->">
                    <input class="form-check-input form-control" type="radio" name="book[status_options]" id="defaultRadioSwitch" {{ !isset($book['status_options'])?'checked':'' }} {{ isset($book['status_options']) && $book['status_options'] == 'maintenance'?'checked':'' }} autocomplete="off" style="width:20px" value="maintenance" required />
                    <div id="error_status_options" class="error_msg"></div>
                    <label class="form-check-label ml-2" for="defaultRadioSwitch">
                        Enabled / Maintainance
                    </label>
                    @if (isset($errs['maintenance']))
                    <div class="error_msg">*{{ $errs['maintenance'] }}</div>
                    @endif
                </div>

            </div>

            <br />
            <div>
                <h3 class="lead main-heading"><label for="condition">Condition</label></h3>
                <select class="form-control allinput" name="book[condition]" id="condition">
                    <option value="">Please select option</option>
                    <option {{ isset($book['condition']) == 'new' ? 'selected' : '' }} value="new">New</option>
                    <option {{ isset($book['condition']) == 'good' ? 'selected' : '' }} value="good">Good</option>
                    <option {{ isset($book['condition']) == 'fair' ? 'selected' : '' }} value="fair">Fair</option>
                    <option {{ isset($book['condition']) == 'poor' ? 'selected' : '' }} value="poor">Poor</option>
                </select>
                <div id="error_condition" class="error_msg"></div>
            </div>
            <br />
            <div>
                <h3 class="lead main-heading"><label for="weight">Weight</label></h3>
                <div class="row">
                    <div class="col-6 col-md-3">
                        <input value="{{ isset($book['weight']) ? $book['weight'] : '' }}" type="text" class="form-control allinput" name="book[weight]" id="weight" />
                    </div>
                    <div class="col-6 col-md-2">
                        <select class="form-control allinput" name="book[weightKgLbs]" id="weightKgLbs">
                            <option {{ isset($book['weightKgLbs']) == 'kg' ? 'selected' : '' }} value="kg">Kg</option>
                            <option {{ isset($book['weightKgLbs']) == 'kg' ? 'selected' : '' }} value="lb">Lb</option>
                        </select>
                    </div>
                </div>
            </div>
            <div id="error_weight" class="error_msg"></div>
            <br />
            <h3 class="lead main-heading">Cover image <span class="text-danger">*</span></h3>
            <span id="cover_page_span">
                <div class="lasfrm-sec">
                    <div class="file-drop-area">
                        @php
                        if ($mode == "edit") {
                        $bookURL = isset($book['cover_page']) ? $book['cover_page'] : "";
                        } else {
                        $bookURL = "";
                        }
                        @endphp
                        <div class="holder">
                            <img id="imgPreview" src="{{ $bookURL? $bookURL : asset('img/upload-big-arrow.png') }}" alt="Select Cover Image" {{ !$bookURL ? 'style="width: 150px"' : ""; }} />
                            {!! !$bookURL ? "<span style='margin-left: 10px'>Select Cover Image</span>" : "" !!}
                        </div>
                        <br>
                        <br>
                        <input id="photo" class="file-input" type="file" accept=".jpeg,.jpg,.png" name="cover_page" value="{{ $bookURL }}" {{ $retdata['mode'] === "edit" ? "" : "" }} />
                    </div>
                </div>
                <div id="error_cover_page" class="error_msg"></div>
            </span>
            <div class="btn-se3 pt-4">
                <button type="submit" id="form-submit-btn" class="btn lastbtn submit_btn">Submit</button>
            </div>
            @if (isset($err_message))
            <div class="error_msg">*{{ $err_message }}</div>
            @endif
            </form>
        </div>
    </div>


    @if ($mode == "add")
    <div id="csv_tab" class="tabcontent">
        <div class="main-box d-flex flex-column flex-md-row">
            <div class="box-form">
                <form class="main-form" id="csv-import-form" name="import-csv-form" enctype="multipart/form-data" id="import-csv-form" method="post" action="{{ route('import-item-csv').'?csv_tab=true' }}">
                    @csrf

                    <div>
                        <h3 class="lead main-heading">Select Item Category <span class="text-danger">*</span></h3>
                        <select class="form-control allinput" id="import_type_id" name="type_id" required>
                            @foreach ($data["types"] as $type)
                            <option value="{{ $type['id'] }}" @selected(old('type_id')===$type['id'])>{{ $type['name'] }}</option>
                            @endforeach
                        </select>
                        @error('type_id')
                        <div class="error_msg">*{{ $message }}</div>
                        @endif
                        <p>The type of item are you adding.</p>
                    </div>

                    <div class="form-group mb-3">
                        <h3 class="lead main-heading">Location <span class="text-danger">*</span>
                            <span class="text-danger">
                                @if(empty($userItemLocations)) Please update your profile with location first <a href='/edit-profile'>Edit Profile</a>@endif
                            </span>
                        </h3>
                        <select class="form-control selectpicker allinput" id="locations" name="locations[]" multiple>
                            @foreach ($userItemLocations as $location)
                            <option value="{{ $location }}" @selected(in_array($location, old('locations', [])))>{{ $location }}</option>
                            @endforeach
                        </select>
                        <div id="error_locations" class="error_msg"></div>
                        @error('locations')
                        <div class="error_msg">*{{ $message }}</div>
                        @endif
                    </div>

                    <div>
                        <h3 class="lead main-heading">Select Group</h3>
                        <select class="form-control allinput" id="import_group_id" name="group_id" required>
                            @if(!empty($data['groups']))
                            @foreach ($data['groups'] as $group)
                            <option value="{{ $group['id'] }}" @selected(old('group_id')==$group['id'])>
                                {{ $group['title'] }}
                            </option>
                            @endforeach
                            @endif
                        </select>
                        @error('group_id')
                        <div class="error_msg">*{{ $message }}</div>
                        @enderror
                        <p>Choose the group you're adding items to.</p>
                    </div>

                    <div>
                        <h3 class="lead main-heading">Available For</h3>
                        <select class="form-control allinput" id="" name="sale_or_rent">
                            <option value="rent">Rent</option>
                        </select>
                        @error('sale_or_rent')
                        <div class="error_msg">*{{ $message }}</div>
                        @endif
                        <p>Item will be available for this purpose.</p>
                    </div>


                    <div class="form-group mb-3">
                        <h3 class="lead main-heading">Select Import Method</h3>
                        <label>
                            <input type="radio" name="import_method" value="file" checked> Upload File
                        </label>
                        <label>
                            <input type="radio" name="import_method" value="url"> Bulk Import
                        </label>
                    </div>
                    <div class="form-group mb-3" id="file-input-container">
                        <label for="csv_file">Upload CSV File</label>
                        <input class="form-control" id="csv_file" accept=".csv" type="file" name="csv_file" />
                        @error('csv_file')
                        <div class="error_msg">*{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-3" id="url-input-container" style="display: none;">
                        <label for="csv_url">Enter CSV/Excel File URL</label>
                        <input class="form-control" id="csv_url" type="url" name="csv_url" placeholder="https://docs.google.com/spreadsheets/d/{sheet_id}/edit?gid={sheet_tab_id} or direct file URL" />
                        <small class="text-muted">
                            Supported URLs:
                            <ul>
                                <li>Google Sheets URLs (e.g., <code>https://docs.google.com/spreadsheets/d/{sheet_id}/edit?gid={sheet_tab_id}</code>)</li>
                                <li>Direct CSV file URLs (e.g., <code>http://example.com/sample.csv</code>)</li>
                                <li>Google Sheets CSV Export URLs (e.g., <code>https://docs.google.com/spreadsheets/d/{sheet_id}/export?format=csv&gid={sheet_tab_id}</code>)</li>
                                <li>Public Google Sheets only (Ensure "Anyone with the link" access is enabled)</li>
                                <li>CSV URLs must point directly to the file download without requiring authentication</li>
                                <li>Ensure the Google Sheet is published to the web if necessary (<code>File → Share → Publish to the web</code>)</li>
                                <li>Test URLs in Incognito or Private mode to verify public accessibility</li>
                                <li>Data should be in plain text or proper numeric format without scientific notation</li>
                            </ul>
                            Ensure the file or sheet is publicly accessible.
                        </small>
                        @error('csv_url')
                        <div class="error_msg">*{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <button type="button" onclick="showSwalMessageWithCallback('Confirmation','Please be patient it can take few minutes?','question',function(){$('#csv-import-form').submit()})" id="csv_import_form_btn" class="btn lastbtn submit_btn">Upload</button>
                    </div>
                </form>
            </div>

            <div class="text-box">
                <img src="{{ url('assets/media/information-circle.png') }}">
                <h3>Please note:</h3>
                <p>Format of file should be like following sample file:</p>
                <a href="{{ url('book_import_sample_new.csv') }}" download="Sample File.csv">Sample File</a>
            </div>
        </div>
    </div>

    <div id="amazon_tab" class="tabcontent">
        <div class="main-box-amazon">
            <form class="main-form" id="amazon-import-form" method="POST" action="{{ route('search-items') }}">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <h3 class="lead main-heading">Select Item Category <span class="text-danger">*</span></h3>
                        <select class="form-control allinput" id="import_type_id" name="type_id" required>
                            <option value="all">All Categories</option>
                            @foreach ($data["types"] as $type)
                            <option value="{{ $type['id'] }}">{{ $type['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <h3 class="lead main-heading">Search By</h3>
                        <input type="text" name="query" class="form-control allinput" placeholder="Name, Keyword, SKU" required />
                    </div>
                </div>
                <div class="form-group mt-3">
                    <button type="submit" class="btn lastbtn submit_btn">Search items</button>
                </div>
            </form>
            <div id="amazon_searched_content"></div>
        </div>
    </div>
    @endif

    @endif

    @php $book_id = isset($book_id) ? $book_id : "" @endphp

</div>


<div class="modal fade" id="groupSelectionModal" tabindex="-1" aria-labelledby="groupSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="groupSelectionModalLabel">Select Group</h5>
            </div>
            <div class="modal-body">
                <form id="groupSelectionForm">
                    @csrf
                    <input type="hidden" name="item_id" id="selectedItemId" value="">
                    <div class="form-group">
                        <label for="groupSelect">Select Group</label>
                        <select id="groupSelect" name="group_id" class="form-control" required>
                            <option value="">Select Group</option>
                            @foreach($data["groups"] as $group)
                            <option value="{{ $group->id }}">{{ $group->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="groupSelect">Enter number of copies</label>
                        <input type="number" name="copies" class="form-control" placeholder="How many copies do you have?">
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success text-nowrap">Add to Group</button>
                        <button type="button" id="close-add-group-modal" class="btn btn-danger">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('input[name="import_method"]').change(function() {
            if ($(this).val() === 'file') {
                $('#file-input-container').show();
                $('#url-input-container').hide();
                $('#csv_url').val('');
            } else if ($(this).val() === 'url') {
                $('#file-input-container').hide();
                $('#url-input-container').show();
                $('#csv_file').val('');
            }
        });

        $('#type_id, #price').on('input', function() {

            const selectedOption = $("#type_id option:selected");
            const categoryPercent = parseFloat(selectedOption.data('percent')) || 0;
            const price = parseFloat($('#price').val()) || 0;

            if (!selectedOption.val()) {
                $('#rent_price').val('');
                alert('Please select a category');
                return;
            }

            if (categoryPercent > 0) {
                const rentPrice = Math.round(price * categoryPercent / 100);
                $('#rent_price').val(rentPrice);
            } else {
                $('#rent_price').val('');
                alert('Percentage for this category is missing or invalid.');
            }
        });

        const photoInp = $("#photo");
        photoInp.change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    $(".holder span").hide();
                    $("#imgPreview").attr("src", event.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        $('#add-book-form').on('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const mode = $("#mode").val();
            const bookId = $("#book_id").val();
            const url = mode === "edit" ? `/update-item-api/${bookId}` : "/store-item-api";

            $("#form-submit-btn").attr("disabled", true).text("Submitting...");

            $.ajax({
                type: 'POST'
                , url: url
                , data: formData
                , contentType: false
                , processData: false
                , success: function(response) {
                    $("#form-submit-btn").attr("disabled", false).text("Submit");
                    if (response.status) {
                        Swal.fire({
                            icon: "success"
                            , title: "Success"
                            , text: response.message
                            , confirmButtonText: "OK"
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                }
                , error: function(xhr) {
                    $("#form-submit-btn").attr("disabled", false).text("Submit");
                    const errors = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : {};
                    $('.error_msg').empty();

                    $.each(errors, function(key, message) {
                        const field = key.split('.').pop();
                        $(`#error_${field}`).text(message[0]);
                    });

                    Swal.fire({
                        icon: "error"
                        , title: "Oops..."
                        , text: "Please fill all mandatory fields!"
                    });
                }
            });
        });

        $('#amazon-import-form').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action')
                , method: 'POST'
                , data: formData
                , success: function(response) {
                    if (response.status) {
                        $('#amazon_searched_content').html(response.view);
                    } else {
                        $('#amazon_searched_content').html(`<p>${response.message}</p>`);
                    }
                }
                , error: function() {
                    alert('An error occurred while processing your request.');
                }
            , });
        });


        $(document).on('click', '.add-to-group', function() {
            const itemId = $(this).data('item-id');
            $('#selectedItemId').val(itemId);
            $('#groupSelectionModal').modal('show');
        });

        $('#close-add-group-modal').click(function() {
            $('#groupSelectionModal').modal('hide');
        })

        $('#groupSelectionForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();

            Swal.fire({
                title: 'Are you sure?'
                , text: "Do you want to add this item to the group?"
                , icon: 'warning'
                , showCancelButton: true
                , confirmButtonColor: '#3085d6'
                , cancelButtonColor: '#d33'
                , confirmButtonText: 'Yes, proceed!'
                , cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...'
                        , text: 'Please wait while we add the item to the group.'
                        , icon: 'info'
                        , allowOutsideClick: false
                        , showConfirmButton: false
                        , didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '{{ route("add-to-group") }}'
                        , method: 'POST'
                        , data: formData
                        , success: function(response) {
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success'
                                    , title: 'Success'
                                    , text: response.message
                                    , confirmButtonText: 'OK'
                                , }).then(() => {
                                    window.location.href = response.url;
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error'
                                    , title: 'Error'
                                    , text: response.message
                                    , confirmButtonText: 'OK'
                                });
                            }
                        }
                        , error: function() {
                            Swal.fire({
                                icon: 'error'
                                , title: 'Error'
                                , text: 'An error occurred while adding the item to the group. Please try again later.'
                                , confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

    });
  
  document.getElementById('price').addEventListener('input', function(e) {
        let value = e.target.value;
        if (value.includes('.')) {
            const parts = value.split('.');
            if (parts[1].length > 2) {
                value = parts[0] + '.' + parts[1].slice(0, 2);
                e.target.value = value;
            }
        }
    });

</script>
