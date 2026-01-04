@include('front.top')
<h1>Registration Page:</h1>

@if(session('success'))
{{ session('success') }}
@endif

@if(session('error'))
{{ session('error') }}
@endif


<form action="{{ route('registration_submit') }}" method="post">
    @csrf
    <!-- Name -->
    <label for="name">Name:</label>
    <input type="text" name="name" placeholder="Enter Name">
    @error('name')
    <p style="color:red">{{ $message }}</p>
    @enderror


    <pre></pre>

    <!-- Email -->
    <label for="email">Email:</label>
    <input type="email" name="email" placeholder="Enter Email">
    @error('email')
    <p style="color:red">{{ $message }}</p>
    @enderror

    <pre></pre>
    <!-- Password -->
    <label for="password">Password:</label>
    <input type="password" name="password" placeholder="Enter Password">
    @error('password')
    <p style="color:red">{{ $message }}</p>
    @enderror

    <pre></pre>

    <label for="password_confirmation">Confirm Password:</label>
    <input type="password" name="password_confirmation"  placeholder="Confirm password">
    @error('password_confirmation')
    <p style="color:red">{{ $message }}</p>
    @enderror

    <pre></pre>
    <button type="submit">Registration</button>

</form>