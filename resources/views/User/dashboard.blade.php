@include('front.top')

@if(session('success'))
<div style="color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin-bottom: 15px; border: 1px solid #c3e6cb;">
    {{ session('success') }}
</div>
@endif

<h1>User Dashboard</h1>
<p>Welcome, {{ Auth::user()->name }}!</p>
<p>Email: {{ Auth::user()->email }}</p>

@if(Auth::user()->google_id)
<p>Connected with: Google Account</p>
@endif

@if(Auth::user()->facebook_id)
<p>Connected with: Facebook Account</p>
@endif
