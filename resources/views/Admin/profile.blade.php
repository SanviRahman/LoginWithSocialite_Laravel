@include('front.top')
<h1>Admin Profile Page</h1>

@if(session('success'))
<div>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div>
    {{ session('error') }}
</div>
@endif


<form action="{{ route('admin_profile_submit') }}" method="post" enctype="multipart/form-data">
    @csrf

    <!-- Existing Photo -->
    <div>
        <label>Existing Photo:</label><br>
        @if(Auth::guard('admin')->user()->photo == null)
        <p>No Photo Found</p>
        @else
        <img src="{{ asset('uploads/'.Auth::guard('admin')->user()->photo) }}" alt="Profile Photo"
            style="width: 100px;height:auto; border: 1px solid #ddd; border-radius: 5px;">
        @endif
    </div>
    <pre></pre>
    <!-- Change Photo -->
    <div>
        <label for="photo">Change Photo:</label>
        <input type="file" name="photo" id="photo" accept="image/*">
        @error('photo')
        <p style="color:red">{{ $message }}</p>
        @enderror
    </div>
    <pre></pre>
    <!-- Name -->
    <div>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="{{  Auth::guard('admin')->user()->name }}">
        @error('name')
        <p style="color:red">{{ $message }}</p>
        @enderror
    </div>
    <pre></pre>
    <!-- Email -->
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="{{ Auth::guard('admin')->user()->email }}">
        @error('email')
        <p style="color:red">{{ $message }}</p>
        @enderror
 
    <pre></pre>

    <!-- Password Change (Optional) -->

    <div>
        <label for="password">New Password:</label>
        <input type="password" name="password" id="password">
        @error('password')
        <p style="color:red">{{ $message }}</p>
        @enderror
    </div>
    
    <pre></pre>
    <div>
        <label for="password_confirmation">Confirm New Password:</label>
        <input type="password" name="password_confirmation" id="password_confirmation">
        @error('password_confirmation')
        <p style="color:red">{{ $message }}</p>
        @enderror
    </div>
    
    <pre></pre>
    <button type="submit">Update Profile</button>
</form>