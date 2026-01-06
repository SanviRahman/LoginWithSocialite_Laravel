@include('front.top')
<h1>Login Page:</h1>

@if(session('success'))
<div >
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div>
    {{ session('error') }}
</div>
@endif

@if(session('warning'))
<div>
    {{ session('warning') }}
</div>
@endif

<form action="{{ route('login_submit') }}" method="post">
    @csrf

    <!-- Email -->
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" placeholder="Enter Email" value="{{ old('email') }}">
        @error('email')
        <p style="color:red">{{ $message }}</p>
        @enderror
    </div>

    <pre></pre>

    <!-- Password -->
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" placeholder="Enter Password">
        @error('password')
        <p style="color:red">{{ $message }}</p>
        @enderror
    </div>

    <pre></pre>

    <button type="submit">Login</button>
    <div style="margin-top: 15px;">
        <a href="{{ url('user/authorized/google') }}">
            Sign In with Google
        </a>
    </div>

    <pre></pre>

    <div>
        <a href="{{ route('forget_password') }}">Forget Password?</a>
    </div>

</form>