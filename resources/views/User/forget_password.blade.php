@include('front.top')
<h1>Forget Password</h1>

@if(session('success'))
{{ session('success') }}
@endif

@if(session('error'))
{{ session('error') }}
@endif

<form action="{{ route('forget_password_submit') }}" method="post">
    @csrf
    <!-- Email -->
    <label for="email">Email</label>
    <input type="email" name="email" placeholder="Enter Email">
    @error('email')
    <p style="color:red">{{ message }}</p>
    @enderror

    <pre></pre>
    <button type="submit">Submit</button>
    <div>
        <a href="{{ route('user_login') }}">Back to Login Page.</a>
    </div>
</form>