<h2>{{ $subject }}</h2>
<p>{{ $body }}</p>

@if(isset($link) && $link)
<div>
    @php
    $isResetPassword = str_contains($subject,'Reset Password');
    $isRegistration = str_contains($subject,'Registration');
    @endphp

    @if($isResetPassword)
    <a href="{{ $link }}">Reset Password.</a>
    @elseif($isRegistration)
    <a href="{{ $link }}">Verify Email.</a>
    @endif
</div>
<p><small>This link will expire in 24 hours for security reasons.</small></p>
@endif

<hr>

<footer>
    <p>If you didn't request this, please ignore this email.</p>
    <p>Thank you,<br>
        <strong>{{ config('app.name') }}</strong>
    </p>
</footer>