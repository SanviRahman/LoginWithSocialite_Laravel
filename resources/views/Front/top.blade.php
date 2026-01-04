<div>
    <?php
        $isAdmin = auth()->guard('admin')->check();
        $isUser = auth()->guard('web')->check();
    ?>

    <a href="{{ route('home') }}">Home</a> |
    <a href="{{ route('about') }}">About</a> |

    @if($isAdmin)
    <a href="{{ route('admin_dashboard') }}">Admin_Dashboard</a> |
    <a href="{{ route('admin_profile') }}">Admin Profile</a> |
    <a href="{{ route('admin_logout') }}">Logout</a>


    @elseif($isUser)
    <a href="{{ route('dashboard') }}">User Dashboard</a> |
    <a href="{{ route('profile') }}">User Profile</a> |
    <a href="{{ route('logout') }}">Logout</a>

    @else
    <a href="{{ route('admin_login') }}">Admin Login</a>|
    <a href="{{ route('registration') }}">User Registration</a> |
    <a href="{{ route('user_login') }}">User Login</a>
    @endif