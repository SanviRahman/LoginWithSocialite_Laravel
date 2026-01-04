@include('front.top')
<h1>Admin Page:</h1>

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


<form action="{{ route('admin_login_submit') }}" method="post">
    @csrf

    <!-- Email -->
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" placeholder="Enter Email">
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

    <br><br>

    <div>
        <a href="{{ route('admin_forget_password') }}">Forget Password?</a>
    </div>

</form>