@include('front.top')
<h1>Admin Dashboard.</h1>
<p>
    Welcome {{ Auth::guard('admin')->user()->name }} to your Dashboard.
</p>