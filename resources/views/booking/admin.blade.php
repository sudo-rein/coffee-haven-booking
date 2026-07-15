@extends('layouts.app')
@section('content')

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:30px;">
    <div class="card" style="text-align:center;">
        <div style="font-size:48px; font-weight:bold; color:#6f4e37;">{{ $totalBookings }}</div>
        <div style="color:#999; margin-top:5px;">Total Bookings</div>
    </div>
    <div class="card" style="text-align:center;">
        <div style="font-size:48px; font-weight:bold; color:#6f4e37;">{{ $totalUsers }}</div>
        <div style="color:#999; margin-top:5px;">Registered Users</div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<h3 style="color:#6f4e37; margin-bottom:5px;">Bookings Calendar</h3>
<p style="color:#999; font-size:13px; margin-bottom:15px;">Click an available date to create a booking</p>
<div id="admin-calendar" style="margin-bottom:30px;"></div>

<!-- Quick Book Modal -->
<div id="quick-book-modal" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    z-index:9999;
    justify-content:center;
    align-items:center;
">
    <div style="
        background:white;
        padding:30px;
        border-radius:15px;
        width:450px;
        box-shadow:0 10px 30px rgba(0,0,0,0.2);
    ">
        <h3 style="color:#6f4e37; margin-bottom:20px;">📅 New Booking</h3>

        <form method="POST" action="/admin/bookings" id="quick-book-form">
            @csrf

            <label style="color:#4a2c2a; font-weight:600; font-size:14px;">Selected Date</label>
            <input type="text" id="modal-date-display" readonly style="background:#fffaf5; color:#6f4e37; font-weight:600;">
            <input type="hidden" name="booking_date" id="modal-booking-date">

            <label style="color:#4a2c2a; font-weight:600; font-size:14px;">Customer Name</label>
            <input type="text" name="customer_name" id="modal-customer-name" placeholder="Enter customer name">

            <label style="color:#4a2c2a; font-weight:600; font-size:14px;">Choose a Room</label>
            <select name="room_id" id="modal-room-id">
                <option value="">-- Select a Room --</option>
                @foreach(\App\Models\Room::all() as $room)
                    <option value="{{ $room->id }}">
                        {{ $room->name }} — {{ $room->description }} (Max: {{ $room->capacity }})
                    </option>
                @endforeach
            </select>

            <label style="color:#4a2c2a; font-weight:600; font-size:14px;">Number of Persons</label>
            <input type="number" name="persons" id="modal-persons" min="1" value="1">

            <br><br>

            <div style="display:flex; gap:10px;">
                <button type="submit" style="flex:1;">Create Booking</button>
                <button type="button" onclick="closeModal()" style="flex:1; background:#999;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div style="display:flex; justify-content:space-between; align-items:center;">
    <h2>All Bookings</h2>
    <div style="display:flex; gap:10px;">
        <a href="/admin/bookings/create">
            <button style="background:#4a7c4e;">+ New Booking</button>
        </a>
        <form method="POST" action="/logout" style="display:inline;">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</div>

@if($bookings->isEmpty())
    <p style="margin-top:20px;">No bookings yet.</p>
@else
<table>
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>Customer</th>
            <th>Room</th>
            <th>Date</th>
            <th>Persons</th>
            <th>File</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($bookings as $booking)
        <tr>
            <td><strong>{{ $booking->booking_id }}</strong></td>
            <td>{{ $booking->customer_name }}</td>
            <td>{{ $booking->room->name ?? $booking->event_name }}</td>
            <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('F d, Y') }}</td>
            <td>{{ $booking->persons }}</td>
            <td>
                @if($booking->confirmation_file)
                    <a href="{{ asset('storage/'.$booking->confirmation_file) }}" target="_blank">View</a>
                @else
                    <span style="color:#999;">None</span>
                @endif
            </td>
            <td>
                <a href="/admin/bookings/{{ $booking->id }}/edit">
                    <button style="background:#c68e5b; padding:6px 12px;">Edit</button>
                </a>
                <form method="POST" action="/admin/bookings/{{ $booking->id }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button style="background:#a33; padding:6px 12px;" onclick="return confirm('Delete this booking?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<script>
function closeModal() {
    document.getElementById('quick-book-modal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('quick-book-modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/booked-dates')
        .then(res => res.json())
        .then(data => {
            const events = data.map(b => ({
                title: '🚫 ' + b.room,
                date:  b.date,
                color: '#a33',
            }));

            const calendar = new FullCalendar.Calendar(document.getElementById('admin-calendar'), {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left:   'prev,next today',
                    center: 'title',
                    right:  'dayGridMonth,listMonth'
                },
                events: events,
                dayCellClassNames: function(info) {
                    const dateStr = info.date.toISOString().split('T')[0];
                    return data.some(b => b.date === dateStr) ? ['booked-day'] : ['available-day'];
                },
                dateClick: function(info) {
                    const clickedDate = info.dateStr;
                    const isBooked = data.some(b => b.date === clickedDate);

                    if (isBooked) {
                        alert('This date is already booked!');
                        return;
                    }

                    // Format date for display
                    const formatted = new Date(clickedDate + 'T00:00:00')
                        .toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });

                    // Fill modal
                    document.getElementById('modal-booking-date').value  = clickedDate;
                    document.getElementById('modal-date-display').value  = formatted;
                    document.getElementById('modal-customer-name').value = '';
                    document.getElementById('modal-room-id').value       = '';
                    document.getElementById('modal-persons').value       = '1';

                    // Show modal
                    document.getElementById('quick-book-modal').style.display = 'flex';
                },
                eventClick: function(info) {
                    alert('Booked: ' + info.event.title.replace('🚫 ', '') + '\nDate: ' + info.event.startStr);
                }
            });

            calendar.render();
        });
});
</script>

<style>
.booked-day   { background:#ffe5e5 !important; cursor:not-allowed !important; }
.available-day:hover { background:#f5eee6 !important; cursor:pointer !important; }
.fc-toolbar-title { color:#6f4e37 !important; }
.fc-button-primary { background:#6f4e37 !important; border-color:#6f4e37 !important; }
.fc-button-primary:hover { background:#4a2c2a !important; border-color:#4a2c2a !important; }
</style>

@endsection