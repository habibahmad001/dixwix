<section class="single-blog">
    <div class="container">
        <div class="blog-header text-center mt-5">
            <h1 class="blog-title">{{ $post->title }}</h1>
            <div class="blog-meta mt-3">
                <span class="blog-category badge bg-primary">{{ $post->category }}</span>
                <span class="blog-date text-muted ms-3">{{ $post->created_at->format('M d, Y') }}</span>
                <span class="blog-author text-muted ms-3">By <strong>{{ $post->user->name }}</strong></span>
            </div>
        </div>
        <div class="blog-image my-5 text-center">
            <img src="{{ asset($post->image) }}" alt="{{ $post->title }}" class="img-fluid rounded shadow">
        </div>
        <div class="blog-content">
            {!! $post->content !!}
        </div>
        <div class="blog-footer mt-5">
            <div class="row">
                <div class="col-md-8 d-flex align-items-center">
                    <img src="{{ isset($post->user->profile_pic) && !empty($post->user->profile_pic) ? asset('storage/' . $post->user->profile_pic) : url('assets/media/userimg.png') }}" alt="{{ $post->user->name }}" class="author-img rounded-circle me-3" width="50" height="50">
                    <div>
                        <h5 class="mb-0">{{ $post->user->name }}</h5>
                        <p class="text-muted small">Published on {{ $post->created_at->format('F j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="related-posts mt-5">
            <h3 class="mb-4">Related Posts</h3>
            <div class="row">
                @foreach ($relatedPosts as $relatedPost)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card">
                        <a href="{{ route('post-slug', $relatedPost->slug) }}">
                            <img src="{{ asset($relatedPost->image) }}" class="card-img-top" alt="{{ $relatedPost->title }}">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('post-slug', $relatedPost->slug) }}" class="text-dark text-decoration-none">
                                    {{ $relatedPost->title }}
                                </a>
                            </h5>
                            <p class="card-text text-muted small">
                                {{ Str::limit(strip_tags($relatedPost->content), 100, '...') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
