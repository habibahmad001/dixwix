@php $english_alp = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"]; @endphp

@include("dashboard.filters")

<div class="inner_content">
    <div class="toolbar_nav">
        <?php /*<h3>Pick of the day for you</h3>*/ ?>

        <div class="d-flex flex-column flex-md-row">
          <b class="disabled_header_menu_icon text-md-nowrap">&nbsp;</b>
          	<?php /*<a id="toolbar_nav_dev" class="disabled_header_menu_icon text-md-nowrap">
                Developing the Leaders Around You <i class="fas fa-angle-right"></i>
            </a>*/ ?>
            <div class="bottom-links d-flex flex-column flex-md-row" style="gap:5px">
                <a href="{{ route("dashboard", ["selected_alphabet" => $data['selected_alphabet'], "view_type" => "title"]) }}" id="toolbar_nav_title" class="{{ !isset($data["view_type"]) || $data["view_type"] == "title" ? "dark-btn" : "" }} btn link_with_img">
                    <img src="assets/media/summary.png"> Title
                </a>
                <a href="{{ route("dashboard", ["selected_alphabet" => $data['selected_alphabet'], "view_type" => "summary"]) }}" id="toolbar_nav_summary" class="{{ isset($data["view_type"]) && $data["view_type"] == "summary" ? "dark-btn" : "" }} btn link_with_img">
                    <img src="assets/media/title.png"> Summary
                </a>
                <a href="javascript:void(0)" id="toolbar_nav_filter" class="dark-btn btn link_with_img" onclick="openNav()">
                    <img src="assets/media/filter.png"> Filter
                </a>
            </div>
        </div>
    </div>

    <div class="filter_by_alpha">
        <?php /*<h3>Pick of the day for you</h3>*/ ?>
        <div class="d-flex flex-wrap">
            <a href="{{ route("dashboard", ["selected_alphabet" => "All", "view_type" => $data['view_type']]) }}" class="btn btn-link p-0">All</a>
            @foreach ($english_alp as $alphabet)
            <a href="{{ route("dashboard", ["selected_alphabet" => $alphabet, "view_type" => $data['view_type']]) }}" class="btn btn-link p-0">{{ $alphabet }}</a>
            @endforeach
        </div>
        <div class="selected_alpha_filter">{{ ucfirst($data["selected_alphabet"]) }}</div>
    </div>

    @if (count($data["types"]) == 0 && count($data["groups"]) == 0)
    <div class="item search-result">
        <div class="text404">
            <img src="{{ url('assets/media/error 1.png') }}">
            <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">Item category and Group not found</p>
        </div>
    </div>
    @else
    @include("dashboard.".$data['view_type'])
    @endif
</div>
@include('book.modal')
@include("scripts.dashboard")
