<div class="container">
    <div class="heading">
        <h2>{{ $data['title'] }}</h2>
    </div>
    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
    @endif
    <div class="divider">
        <hr>
    </div>
    <form class="main-form" name="edit-plan-form" enctype="multipart/form-data" id="edit-plan-form" method="post" action="{{ route('update-plan', $plan->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Name</label>
            <input type="text" required class="form-control" id="name" name="name" value="{{ old('name', $plan->name) }}" placeholder="Plan Name">
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label>Allowed Groups</label>
            <input type="number" class="form-control" id="allowed_groups" name="allowed_groups" value="{{ old('allowed_groups', $plan->allowed_groups) }}" placeholder="plan Allowed Groups">
            @error('allowed_groups')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label>Allowed Items</label>
            <input type="number" required class="form-control" id="allowed_items" name="allowed_items" value="{{ old('allowed_items', $plan->allowed_items) }}" placeholder="plan Allowed Items">
            @error('allowed_items')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="text" class="form-control" id="price" name="price" value="{{ old('price', $plan->price) }}" placeholder="plan Price">
            @error('price')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label>Stripe Price Id</label>
            <input type="text" class="form-control" id="stripe_price_id" name="stripe_price_id" value="{{ old('stripe_price_id', $plan->stripe_price_id) }}" placeholder="plan Stripe Price Id">
            @error('stripe_price_id')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn lastbtn submit_btn">Update</button>
        </div>
    </form>
</div>
