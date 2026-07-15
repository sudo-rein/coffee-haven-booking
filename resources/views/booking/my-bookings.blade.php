@extends('layouts.app')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center;">
    <h2>My Bookings</h2>
    <div style="display:flex; gap:10px;">
        <a href="/booking/details">
            <button>+ New Booking</button>
        </a>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" style="background:#4a2c2a;">Logout</button>
        </form>
    </div>
</div>

<br>

<!-- Stats Card -->
<div class="card" style="display:flex; gap:30px; align-items:center;">
    <div style="text-align:center;">
        <div style="font-size:36px; font-weight:bold; color:#6f4e37;">{{ $bookings->count() }}</div>
        <div style="color:#999; font-size:14px;">My Bookings</div>
    </div>
    <div style="text-align:center;">
        <div style="font-size:36px; font-weight:bold; color:#6f4e37;">
            {{ $bookings->where('booking_date', '>=', \Carbon\Carbon::today()->toDateString())->count() }}
        </div>
        <div style="color:#999; font-size:14px;">Upcoming</div>
    </div>
    <div style="text-align:center;">
        <div style="font-size:36px; font-weight:bold; color:#6f4e37;">
            {{ $bookings->where('booking_date', '<', \Carbon\Carbon::today()->toDateString())->count() }}
        </div>
        <div style="color:#999; font-size:14px;">Past</div>
    </div>
</div>

<br>

<!-- FullCalendar -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<h3 style="color:#6f4e37; margin-bottom:15px;">My Booking Calendar</h3>
<div id="my-calendar" style="margin-bottom:30px;"></div>

<br>

<!-- Bookings Table -->
<h3 style="color:#6f4e37; margin-bottom:15px;">Booking Details</h3>

@if($bookings->isEmpty())
    <div class="card" style="text-align:center;">
        <p>You have no bookings yet.</p>
        <br>
        <a href="/booking/details">
            <button>Make a Reservation</button>
        </a>
    </div>
@else
<table>
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Room</th>
            <th>Date</th>
            <th>Persons</th>
            <th>Status</th>
            <th>File</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bookings as $booking)
        <tr>
            <td><strong>{{ $booking->booking_id }}</strong></td>
            <td>{{ $booking->room->name ?? $booking->event_name }}</td>
            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</td>
            <td>{{ $booking->persons }}</td>
            <td>
                @if($booking->booking_date >= \Carbon\Carbon::today()->toDateString())
                    <span style="
                        background:#e6f4ea;
                        color:#2e7d32;
                        padding:4px 10px;
                        border-radius:20px;
                        font-size:13px;
                        font-weight:600;
                    ">Upcoming</span>
                @else
                    <span style="
                        background:#f5f5f5;
                        color:#999;
                        padding:4px 10px;
                        border-radius:20px;
                        font-size:13px;
                        font-weight:600;
                    ">Past</span>
                @endif
            </td>
            <td>
                @if($booking->confirmation_file)
                    <a href="{{ asset('storage/'.$booking->confirmation_file) }}" target="_blank">View</a>
                @else
                    <span style="color:#999;">None</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {

    const myBookings = @json($calendarEvents);

    const calendar = new FullCalendar.Calendar(document.getElementById('my-calendar'), {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,listMonth'
        },
        events: myBookings,
        eventClick: function(info) {
            alert('Booking: ' + info.event.title.replace('☕ ', '') + '\nDate: ' + info.event.startStr);
        }
    });

    calendar.render();
});
</script>

<style>
.fc-toolbar-title {
    color:#6f4e37 !important;
}
.fc-button-primary {
    background:#6f4e37 !important;
    border-color:#6f4e37 !important;
}
.fc-button-primary:hover {
    background:#4a2c2a !important;
    border-color:#4a2c2a !important;
}
.fc-event {
    cursor: pointer;
}
</style>

@endsection