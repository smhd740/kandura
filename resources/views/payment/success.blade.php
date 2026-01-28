@extends('layouts.app')

@section('content')
<div style="
    max-width: 600px;
    margin: 60px auto;
    background: #ffffff;
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    font-family: Arial, sans-serif;
">
    <h1 style="color: #28a745; font-size: 32px; margin-bottom: 20px;">
        Payment Successful 🎉
    </h1>

    <p style="font-size: 18px; color: #555;">
        Thank you! Your payment has been processed successfully.
    </p>

    @if($sessionId)
        <p style="margin-top: 20px; font-size: 14px; color: #888;">
            Session ID: <strong>{{ $sessionId }}</strong>
        </p>
    @endif

    <a href="/" style="
        display: inline-block;
        margin-top: 30px;
        padding: 12px 24px;
        background: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 8px;
        font-size: 16px;
    ">
        Back to Home
    </a>
</div>
@endsection
