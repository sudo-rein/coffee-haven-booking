@extends('layouts.app')
@section('content')

<!-- Header -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;">
    <div>
        <h2 style="margin-bottom:3px;">My Bookings</h2>
        <p style="color:#999; font-size:14px;">Welcome back, {{ auth()->user()->name }}! ☕</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/booking/details">
            <button style="background:linear-gradient(135deg,#6f4e37,#c68e5b); padding:10px 20px;">
                + New Booking
            </button>
        </a>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" style="background:#4a2c2a; padding:10px 20px;">Logout</button>
        </form>
    </div>
</div>

<!-- Stats -->
<div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:15px; margin-bottom:30px;">
    <div style="
        background:linear-gradient(135deg,#6f4e37,#c68e5b);
        padding:20px;
        border-radius:15px;
        text-align:center;
        color:white;
        box-shadow:0 5px 15px rgba(111,78,55,0.3);
    ">
        <div style="font-size:40px; font-weight:800;">{{ $bookings->count() }}</div>
        <div style="font-size:13px; opacity:0.9; margin-top:5px;">📅 Total Bookings</div>
    </div>
    <div style="
        background:linear-gradient(135deg,#4a7c4e,#6dbf6d);
        padding:20px;
        border-radius:15px;
        text-align:center;
        color:white;
        box-shadow:0 5px 15px rgba(74,124,78,0.3);
    ">
        <div style="font-size:40px; font-weight:800;">
            {{ $bookings->where('booking_date', '>=', \Carbon\Carbon::today()->toDateString())->count() }}
        </div>
        <div style="font-size:13px; opacity:0.9; margin-top:5px;">✅ Upcoming</div>
    </div>
    <div style="
        background:linear-gradient(135deg,#555,#888);
        padding:20px;
        border-radius:15px;
        text-align:center;
        color:white;
        box-shadow:0 5px 15px rgba(0,0,0,0.2);
    ">
        <div style="font-size:40px; font-weight:800;">
            {{ $bookings->where('booking_date', '<', \Carbon\Carbon::today()->toDateString())->count() }}
        </div>
        <div style="font-size:13px; opacity:0.9; margin-top:5px;">🕐 Past</div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<!-- Calendar -->
<div style="background:#fffaf5; border-radius:15px; padding:20px; margin-bottom:30px; border:1px solid #f0e0d0;">
    <h3 style="color:#6f4e37; margin-bottom:15px;">📅 My Booking Calendar</h3>
    <div id="my-calendar"></div>
</div>

<!-- Table -->
<div style="background:white; border-radius:15px; padding:20px; border:1px solid #f0e0d0;">
    <h3 style="color:#6f4e37; margin-bottom:15px;">📋 Booking Details</h3>

    @if($bookings->isEmpty())
        <div style="text-align:center; padding:40px; color:#999;">
            <div style="font-size:50px; margin-bottom:15px;">☕</div>
            <p style="margin-bottom:15px;">No bookings yet. Start your coffee experience!</p>
            <a href="/booking/details">
                <button style="background:linear-gradient(135deg,#6f4e37,#c68e5b);">Make a Reservation</button>
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
                <td><strong style="color:#6f4e37;">{{ $booking->booking_id }}</strong></td>
                <td>
                    <span style="background:#fffaf5; padding:4px 10px; border-radius:20px; font-size:13px; border:1px solid #f0e0d0;">
                        {{ $booking->room->name ?? $booking->event_name }}
                    </span>
                </td>
                <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</td>
                <td>
                    <span style="background:#e8f4fd; color:#1565c0; padding:4px 10px; border-radius:20px; font-size:13px;">
                        👥 {{ $booking->persons }}
                    </span>
                </td>
                <td>
                    @if($booking->booking_date >= \Carbon\Carbon::today()->toDateString())
                        <span style="background:#e6f4ea; color:#2e7d32; padding:4px 12px; border-radius:20px; font-size:13px; font-weight:600;">
                            ✅ Upcoming
                        </span>
                    @else
                        <span style="background:#f5f5f5; color:#999; padding:4px 12px; border-radius:20px; font-size:13px; font-weight:600;">
                            🕐 Past
                        </span>
                    @endif
                </td>
                <td>
                    @if($booking->confirmation_file)
                        <a href="{{ asset('storage/'.$booking->confirmation_file) }}" target="_blank"
                           style="color:#6f4e37; font-weight:600; text-decoration:none;">
                            📄 View
                        </a>
                    @else
                        <span style="color:#ccc;">None</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

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
.fc-toolbar-title { color:#6f4e37 !important; font-weight:700 !important; }
.fc-button-primary { background:linear-gradient(135deg,#6f4e37,#c68e5b) !important; border:none !important; }
.fc-button-primary:hover { opacity:0.9 !important; }
.fc-event { border-radius:5px !important; font-size:12px !important; }
</style>

@endsection