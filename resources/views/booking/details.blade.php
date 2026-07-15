@extends('layouts.app')

@section('content')

<h2>Booking Details</h2>

<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<form method="POST" action="/booking/details" id="bookingForm">
    @csrf

    <label>Customer Name</label><br>
    <input type="text" value="{{ auth()->user()->name }}" readonly>

    <br>

    <label>Choose a Room</label><br>
    <select name="room_id" id="room_id">
        <option value="">-- Select a Room --</option>
        @foreach($rooms as $room)
            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                {{ $room->name }} — {{ $room->description }} (Max: {{ $room->capacity }} persons)
            </option>
        @endforeach
    </select>
    @error('room_id')
        <div class="error">{{ $message }}</div>
    @enderror

    <br>

    <label>Select Booking Date</label>
    @error('booking_date')
        <div class="error">{{ $message }}</div>
    @enderror

    <!-- Hidden input that stores selected date -->
    <input type="hidden" name="booking_date" id="booking_date" value="{{ old('booking_date') }}">

    <!-- Selected date display -->
    <div id="selected-date-display" style="
        padding:12px;
        background:#fffaf5;
        border:1px solid #ddd;
        border-radius:8px;
        margin-bottom:15px;
        color:#6f4e37;
        font-weight:600;
    ">
        📅 No date selected — click a date on the calendar below
    </div>

    <!-- FullCalendar -->
    <div id="calendar" style="margin-bottom:20px;"></div>

    <br>

    <label>Number of Persons</label><br>
    <input type="number" name="persons" value="{{ old('persons') }}" min="1">
    @error('persons')
        <div class="error">{{ $message }}</div>
    @enderror

    <br><br>

    <button type="submit">Next</button>

</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/booked-dates')
        .then(res => res.json())
        .then(data => {

            const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'dayGridMonth',
                headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
                validRange: { start: new Date().toISOString().split('T')[0] },
                dayCellClassNames: function(info) {
                    const dateStr  = info.date.toISOString().split('T')[0];
                    const selected = document.getElementById('room_id').value;

                    // Only mark red if selected room is booked on this date
                    const isBlocked = selected
                        ? data.some(b => b.date === dateStr && b.room_id == selected)
                        : false;

                    return isBlocked ? ['booked-day'] : [];
                },
                events: function(fetchInfo, successCallback) {
                    const selected = document.getElementById('room_id').value;
                    const events = data
                        .filter(b => !selected || b.room_id == selected)
                        .map(b => ({
                            title: '🚫 ' + b.room,
                            date:  b.date,
                            color: '#a33',
                        }));
                    successCallback(events);
                },
                dateClick: function(info) {
                    const clickedDate = info.dateStr;
                    const selected    = document.getElementById('room_id').value;

                    if (!selected) {
                        alert('Please select a room first!');
                        return;
                    }

                    const isBooked = data.some(b => b.date === clickedDate && b.room_id == selected);

                    if (isBooked) {
                        alert('This room is already booked on this date! Please choose another date or room.');
                        return;
                    }

                    document.getElementById('booking_date').value = clickedDate;
                    const formatted = new Date(clickedDate + 'T00:00:00')
                        .toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
                    document.getElementById('selected-date-display').innerHTML =
                        '📅 Selected: <strong>' + formatted + '</strong>';

                    document.querySelectorAll('.selected-day').forEach(el => el.classList.remove('selected-day'));
                    info.dayEl.classList.add('selected-day');
                },
                eventClick: function(info) {
                    alert('Already booked: ' + info.event.title.replace('🚫 ', ''));
                }
            });

            calendar.render();

            // Re-render calendar when room changes
            document.getElementById('room_id').addEventListener('change', function() {
                calendar.refetchEvents();
                calendar.render();

                // Clear selected date when room changes
                document.getElementById('booking_date').value = '';
                document.getElementById('selected-date-display').innerHTML =
                    '📅 No date selected — click a date on the calendar below';
                document.querySelectorAll('.selected-day').forEach(el => el.classList.remove('selected-day'));
            });
        });
});
</script>

<style>
.booked-day {
    background:#ffe5e5 !important;  
    cursor: not-allowed !important;
}
.selected-day {
    background:#fffaf5 !important;
    border: 2px solid #6f4e37 !important;
}
.fc-daygrid-day:not(.booked-day):hover {
    background:#f5eee6 !important;
    cursor: pointer;
}
.fc-toolbar-title {
    color:#6f4e37 !important;
    font-size:18px !important;
}
.fc-button-primary {
    background:#6f4e37 !important;
    border-color:#6f4e37 !important;
}
.fc-button-primary:hover {
    background:#4a2c2a !important;
    border-color:#4a2c2a !important;
}
</style>

@endsection