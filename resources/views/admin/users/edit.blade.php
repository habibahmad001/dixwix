<div class="container">
    <div class="heading">
        <h2>Edit User</h2>
    </div>
    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session()->get('success') }}
    </div>
    @endif
    <div class="divider">
        <hr>
    </div>
    <form class="main-form" name="edit-user-form" enctype="multipart/form-data" id="edit-user-form" method="post" action="{{ route('update-user', $user->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <input type="text" required class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="User Name">
            @error('name')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="User Phone">
            @error('phone')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="email" required class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" placeholder="User Email">
            @error('email')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group textarea">
            <textarea class="form-control" id="biodata" name="biodata" placeholder="User bio data">{{ old('biodata', $user->biodata) }}</textarea>
            @error('biodata')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $user->address) }}" placeholder="User Address">
            @error('address')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $user->state) }}" placeholder="User State">
            @error('state')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="zipcode" name="zipcode" value="{{ old('zipcode', $user->zipcode) }}" placeholder="User Zip Code">
            @error('zipcode')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
            @error('password')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="profile_pic">Profile Picture (Leave blank to keep current)</label>
            <input type="file" class="form-control" id="profile_pic" name="profile_pic">
            <small class="form-text text-muted">Accepted formats: jpg, jpeg, png.</small>
            @if($user->profile_pic)
            @endif
            @error('profile_pic')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group d-flex justify-content-start">
            <img src="{{ asset('storage/' . $user->profile_pic) }}" alt="Current Profile Picture" style="max-height: 200px; max-width:200px; margin-top: 10px;">
        </div>
        @if($user->id != 1)
        <div class="form-group">
            <label for="roles">Assign Roles</label>
            <select name="roles[]" id="roles" class="form-control" multiple>
                @foreach($roles as $role)
                <option value="{{ $role->name }}" {{ $user->roles->contains($role->id) ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
                @endforeach
            </select>
            @error('roles')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        @endif
        <div class="form-group">
            <button type="submit" class="btn lastbtn submit_btn">Update</button>
        </div>
    </form>
</div>
