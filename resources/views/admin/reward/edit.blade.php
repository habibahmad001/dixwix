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
    <form class="main-form" name="edit-plan-form" enctype="multipart/form-data" id="edit-plan-form" method="post" action="{{ route('update-reward', $package->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Name</label>
            <input type="text" required class="form-control" id="name" name="name" value="{{ old('name', $package->name) }}" placeholder="Name">
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label>Coins</label>
            <input type="number" class="form-control" id="coins" name="coins" value="{{ old('coins', $package->coins) }}" placeholder="Coins">
            @error('coins')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="text" class="form-control" id="price" name="price" value="{{ old('price', $package->price) }}" placeholder="Price">
            @error('price')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn lastbtn submit_btn">Update</button>
        </div>
    </form>
</div>
