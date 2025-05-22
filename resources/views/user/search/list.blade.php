<style>
    a:hover, a:focus{
        text-decoration: none;
    }
    h2 .item_title a:hover, h2 .item_title a:focus{
        text-decoration: none;
        text-underline: none;
        color: var(--green-dark-01);
    }

    .item_title a {
        color: var(--green-dark-01);
    }
</style>

<div class="inner_content">
    <h3>{{ $data['title'] }}</h3>
    @if (isset($data['users']) && count($data['users'])>0)

    @foreach ($data["users"] as $ky => $users)
    <div class="items_list">
        <div class="post_item d-flex flex-column flex-md-row">
            <div class="post_image">
                <img src="{{ !is_null($users->profile_pic) ? asset('storage/' . $users->profile_pic) : url("assets/media/userimg.png") }}" alt="Current Profile Picture">
            </div>
            <div class="item_details">
                <div class="item_summary_wrapper d-flex flex-column flex-md-row">
                    <div class="item_summary">
{{--                        <h2 class="item_title"><a href="{{ route("show-item",["id"=>$users["id"]]) }}"> {{ $users["name"] }}</a></h2>--}}
                        <h2 class="item_title">{{ $users["name"] }}</h2>
                        <div class="item_meta mt-2">
                            <span class="item_meta_tag">Email:</span>
                            <span class="item_meta_details">{{ $users->email ? $users->email : 'Email not set yet' }}</span>
                        </div>
                        <div class="item_meta">
                            <span class="item_meta_tag">Phone Number:</span>
                            <span class="item_meta_details">
                              {{ $users->phone ? $users->phone : 'Number not set yet' }}
                            </span>
                      </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    @endforeach
    @else
    <div class="items_list">
        <div class="item search-result">
            <div class="text404">
                <p style="font-family: 'poppins'; font-weight: 600; font-size: 20px; color: #606060;">No User Exist</p>
            </div>
        </div>
    </div>
    @endif
</div>
@include('book.modal')

