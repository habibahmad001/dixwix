<section class="bloglist">
    <div class="container">
        <div class="head d-flex justify-content-between">
            <h2>Latest <span>Blog</span></h2>
            <div class="search">
                <form method="get" action="{{ route('blog') }}">
                    <input type="text" name="search" placeholder="search the blog..." />
                </form>
            </div>
        </div>
        <div class="row">
            @foreach ($posts as $post)

            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="content">
                    <div class="thumb">
                        <a href="{{ route('post-slug', $post->slug) }}">
                            <img src="{{ $post->image }}" />
                        </a>
                    </div>
                    <div class="detail">
                        <div class="meta">
                            <span class="tag">{{ $post->category }}</span>
                            <span class="postdate">{{ $post->created_at->format('F j, Y') }}</span>
                        </div>
                        <h3>
                            <a href="{{ route('post-slug', $post->slug) }}">{{ $post->title }}</a>
                        </h3>
                        <div class="author d-flex align-items-center">
                            <img src="<?= (isset(Auth::user()->profile_pic) && !empty(Auth::user()->profile_pic)) ? asset('storage/'.Auth::user()->profile_pic): url('assets/media/userimg.png') ?>" }}" class="ml-2"> <strong>{{ $post->user->name }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            @endforeach


        </div>
    </div>
</section>
