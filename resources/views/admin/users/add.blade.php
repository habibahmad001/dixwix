<div class="container">
    <div class="heading">
        <h2>Add User</h2>
    </div>
    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
    @endif
    <div class="divider">
        <hr>
    </div>
    <form class="main-form" name="add-user-form" enctype="multipart/form-data" id="add-user-form" method="post" action="{{ route('admin-add-user') }}">
        @csrf
        <div class="form-group">
            <input type="text" required class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="User Name">
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="User Phone">
            @error('phone')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="email" required class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="User Email">
            @error('email')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        @if(auth()->user()->id == 1)
        <div class="form-group">
            <label for="roles">Assign Roles</label>
            <select name="roles[]" id="roles" class="form-control" multiple>
                @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ collect(old('roles'))->contains($role->name) ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
                @endforeach
            </select>
            @error('roles')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        @endif
        <div class="form-group textarea">
            <textarea class="form-control" id="biodata" name="biodata" placeholder="User bio data">{{ old('biodata') }}</textarea>
            @error('biodata')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" placeholder="User Address">
            @error('address')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}" placeholder="User State">
            @error('state')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="zipcode" name="zipcode" value="{{ old('zipcode') }}" placeholder="User Zip Code">
            @error('zipcode')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="password" required class="form-control" id="password" name="password" placeholder="User Password">
            @error('password')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="profile_pic">Profile Picture</label>
            <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
            <small class="form-text text-muted">Accepted formats: jpg, jpeg, png.</small>
            @error('profile_pic')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <button type="submit" class="btn lastbtn submit_btn">Submit</button>
        </div>
    </form>
</div>
