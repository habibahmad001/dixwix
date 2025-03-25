<?php if(isset($retdata)){extract($retdata);} ?>
<div class="container">
    <div class="heading">
        <h2>{{ $data["title"] }}</h2>
    </div>
    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
    @endif
    <div class="divider">
        <hr>
    </div>

    <form class="main-form" name="add-type-form" enctype="multipart/form-data" id="add-type-form" method="post" action="{{route('store-category')}}">
        @csrf
        <input type="hidden" name="mode" value="{{ $mode }}">
        <input type="hidden" name="type_id" value="{{ isset($type_id) ? $type_id : "" }}">

        <!-- <div class="form-group">
            <h3 class="lead main-heading">Select Group Type</h3>
            <select class="form-control" id="group_type_id" name="type[group_type_id]">
                <?php /* foreach ($data["group_types"] as $group_type) { ?>
                    <option value="{{ $group_type["id"] }}" {{ (isset($type) ? ($type["group_type_id"] === $group_type["id"] ? "selected" : "") : "") }}>{{ $group_type["name"] }}</option>
                <?php } */ ?>
            </select>
            @if (isset($errs['group_type_id'])) <div class="error_msg">*{{ $errs['group_type_id'];  }}</div>@endif
            <p>Choose the group type you're adding groups to.</p>
        </div> -->
        <div class="form-group">
            <input type="text" required class="form-control" id="title" name="type[name]" value="{{ (isset($type) ? $type["name"] : "") }}" placeholder="Category Name">
            @if (isset($errs['name']))
            <div class="error_msg">*{{ $errs['name'] }}</div>
            @endif
        </div>
        <div class="form-group">
            <input type="number" min="0" required class="form-control" id="title" name="type[percentage]" value="{{ (isset($type) ? $type["percentage"] : "") }}" placeholder="Category Percentage(%)">
            @if (isset($errs['name']))
            <div class="error_msg">*{{ $errs['name'] }}</div>
            @endif
        </div>
        <div class="form-group textarea">
            <textarea required  class="form-control" id="description" name="type[description]" placeholder="Describe your category here">{{ (isset($type) ? $type["description"] : "") }}</textarea>
            @if (isset($errs['description']))
            <div class="error_msg">*{{ $errs['description'] }}</div>
            @endif
        </div>
        <div class="form-group">
            <button type="submit" class="btn lastbtn submit_btn">Submit</button>
        </div>
        @if (isset($err_message))
        <div class="error_msg">*{{ $err_message }}</div>
        @endif
    </form>
</div>
