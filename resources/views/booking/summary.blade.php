@extends('layouts.app')

@section('content')

<div class="card">
    <h2>☕ Reservation Summary</h2>

    <p>Guest: {{ session('customer_name') }}</p>
    <p>Reservation ID: <strong>{{ session('booking_id') }}</strong></p>
    <p>Room: {{ session('room_name') }}</p>
    <p>Date: {{ \Carbon\Carbon::parse(session('booking_date'))->format('F d, Y') }}</p>
    <p>Guests: {{ session('persons') }}</p>
</div>

<div style="margin-top:20px;">
    @if(session('confirmation_file'))
        <a href="{{ asset('storage/'.session('confirmation_file')) }}" target="_blank">
            <button>View File</button>
        </a>
    @endif
</div>

<br>

<div style="display:flex; gap:10px;">
    <a href="/my-bookings">
        <button>View My Bookings</button>
    </a>
    <a href="/booking/reset">
        <button style="background:#4a2c2a;">☕ Make Another Reservation</button>
    </a>
</div>

@endsection