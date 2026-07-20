@extends('layouts.app')
@section('content')

<!-- Stats -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:30px;">
    <div style="
        background:linear-gradient(135deg, #6f4e37, #c68e5b);
        padding:25px;
        border-radius:15px;
        text-align:center;
        color:white;
        box-shadow:0 5px 15px rgba(111,78,55,0.3);
    ">
        <div style="font-size:50px; font-weight:800;">{{ $totalBookings }}</div>
        <div style="font-size:14px; opacity:0.9; margin-top:5px;">📅 Total Bookings</div>
    </div>
    <div style="
        background:linear-gradient(135deg, #4a7c4e, #6dbf6d);
        padding:25px;
        border-radius:15px;
        text-align:center;
        color:white;
        box-shadow:0 5px 15px rgba(74,124,78,0.3);
    ">
        <div style="font-size:50px; font-weight:800;">{{ $totalUsers }}</div>
        <div style="font-size:14px; opacity:0.9; margin-top:5px;">👤 Registered Users</div>
    </div>
</div>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<div style="background:#fffaf5; border-radius:15px; padding:20px; margin-bottom:30px; border:1px solid #f0e0d0;">
    <h3 style="color:#6f4e37; margin-bottom:5px;">📅 Bookings Calendar</h3>
    <p style="color:#999; font-size:13px; margin-bottom:15px;">Click an available date to create a booking</p>
    <div id="admin-calendar"></div>
</div>

<!-- Quick Book Modal -->
<div id="quick-book-modal" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.6);
    z-index:9999;
    justify-content:center;
    align-items:center;
">
    <div style="
        background:white;
        padding:35px;
        border-radius:20px;
        width:450px;
        box-shadow:0 20px 60px rgba(0,0,0,0.3);
    ">
        <h3 style="color:#6f4e37; margin-bottom:5px;">📅 Quick Booking</h3>
        <p style="color:#999; font-size:13px; margin-bottom:20px;">Fill in the details below</p>

        <form method="POST" action="/admin/bookings">
            @csrf
            <label style="color:#4a2c2a; font-weight:600; font-size:13px;">Selected Date</label>
            <input type="text" id="modal-date-display" readonly style="background:#fffaf5; color:#6f4e37; font-weight:600;">
            <input type="hidden" name="booking_date" id="modal-booking-date">

            <label style="color:#4a2c2a; font-weight:600; font-size:13px;">Customer Name</label>
            <input type="text" name="customer_name" id="modal-customer-name" placeholder="Enter customer name">

            <label style="color:#4a2c2a; font-weight:600; font-size:13px;">Choose a Room</label>
            <select name="room_id" id="modal-room-id">
                <option value="">-- Select a Room --</option>
                @foreach(\App\Models\Room::all() as $room)
                    <option value="{{ $room->id }}">
                        {{ $room->name }} (Max: {{ $room->capacity }})
                    </option>
                @endforeach
            </select>

            <label style="color:#4a2c2a; font-weight:600; font-size:13px;">Number of Persons</label>
            <input type="number" name="persons" id="modal-persons" min="1" value="1">

            <br><br>
            <div style="display:flex; gap:10px;">
                <button type="submit" style="flex:1; background:linear-gradient(135deg,#6f4e37,#c68e5b);">
                    ✅ Create Booking
                </button>
                <button type="button" onclick="closeModal()" style="flex:1; background:#999;">
                    ✖ Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
    <h2 style="color:#6f4e37;">All Bookings</h2>
    <div style="display:flex; gap:10px;">
        <a href="/admin/bookings/create">
            <button style="background:linear-gradient(135deg,#4a7c4e,#6dbf6d); padding:10px 20px;">
                + New Booking
            </button>
        </a>
        <form method="POST" action="/logout" style="display:inline;">
            @csrf
            <button type="submit" style="background:#4a2c2a; padding:10px 20px;">Logout</button>
        </form>
    </div>
</div>

@if($bookings->isEmpty())
    <div style="text-align:center; padding:50px; color:#999;">
        <div style="font-size:50px; margin-bottom:15px;">📭</div>
        <p>No bookings yet.</p>
    </div>
@else
<div style="overflow-x:auto;">
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
            <td><strong style="color:#6f4e37;">{{ $booking->booking_id }}</strong></td>
            <td>{{ $booking->customer_name }}</td>
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
                @if($booking->confirmation_file)
                    <a href="{{ asset('storage/'.$booking->confirmation_file) }}" target="_blank"
                       style="color:#6f4e37; font-weight:600; text-decoration:none;">
                        📄 View
                    </a>
                @else
                    <span style="color:#ccc;">None</span>
                @endif
            </td>
            <td>
                <a href="/admin/bookings/{{ $booking->id }}/edit">
                    <button style="background:#c68e5b; padding:6px 14px; font-size:13px;">✏️ Edit</button>
                </a>
                <form method="POST" action="/admin/bookings/{{ $booking->id }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button
                        style="background:#c0392b; padding:6px 14px; font-size:13px;"
                        onclick="return confirm('Delete this booking?')"
                    >🗑️ Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>
@endif

<script>
function closeModal() {
    document.getElementById('quick-book-modal').style.display = 'none';
}

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
                color: '#c0392b',
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
                    const isBooked    = data.some(b => b.date === clickedDate);

                    if (isBooked) {
                        alert('This date is already booked!');
                        return;
                    }

                    const formatted = new Date(clickedDate + 'T00:00:00')
                        .toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });

                    document.getElementById('modal-booking-date').value  = clickedDate;
                    document.getElementById('modal-date-display').value  = formatted;
                    document.getElementById('modal-customer-name').value = '';
                    document.getElementById('modal-room-id').value       = '';
                    document.getElementById('modal-persons').value       = '1';
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
.booked-day    { background:#ffe5e5 !important; cursor:not-allowed !important; }
.available-day:hover { background:#f5eee6 !important; cursor:pointer !important; }
.fc-toolbar-title { color:#6f4e37 !important; font-weight:700 !important; }
.fc-button-primary { background:linear-gradient(135deg,#6f4e37,#c68e5b) !important; border:none !important; }
.fc-button-primary:hover { opacity:0.9 !important; }
.fc-event { border-radius:5px !important; font-size:12px !important; }
</style>

@endsection