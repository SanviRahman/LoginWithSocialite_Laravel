@include('front.top')
<h1>Reset Password</h1>

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


<form action="{{ route('admin_reset_password_submit', ['token' => $token, 'email' => $email]) }}" method="post">
    @csrf

    <!-- New Password -->
    <div>
        <label for="password">New Password:</label>
        <input type="password" name="password" id="password" placeholder="Enter new password">
        @error('password')
        <p style="color:red">{{ $message }}</p>
        @enderror
    </div>

    <pre></pre>

    <!-- Confirm Password  -->
    <div>
        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" name="password_confirmation" placeholder="Confirm password">
        @error('password_confirmation')
        <p style="color:red">{{ $message }}</p>
        @enderror
    </div>

    <pre></pre>

    <button type="submit">Reset Password</button>
</form>