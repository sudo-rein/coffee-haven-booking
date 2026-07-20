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
            <option
                value="{{ $room->id }}"
                data-name="{{ $room->name }}"
                data-description="{{ $room->description }}"
                data-capacity="{{ $room->capacity }}"
                {{ old('room_id') == $room->id ? 'selected' : '' }}
            >
                {{ $room->name }} (Max: {{ $room->capacity }} persons)
            </option>
        @endforeach
    </select>
    @error('room_id')
        <div class="error">{{ $message }}</div>
    @enderror

    <!-- Room Details Card -->
    <div id="room-details" style="
        display:none;
        background:#fffaf5;
        border:1px solid #f0e0d0;
        border-left:5px solid #c68e5b;
        border-radius:12px;
        padding:20px;
        margin-bottom:15px;
        transition:all 0.3s;
    ">
        <div style="display:flex; align-items:center; gap:15px;">
            <div style="font-size:40px;">🏠</div>
            <div>
                <h3 id="room-name" style="color:#6f4e37; margin-bottom:5px;"></h3>
                <p id="room-description" style="color:#888; font-size:14px; margin-bottom:8px;"></p>
                <div style="display:flex; gap:10px;">
                    <span id="room-capacity" style="
                        background:#e8f4fd;
                        color:#1565c0;
                        padding:4px 12px;
                        border-radius:20px;
                        font-size:13px;
                        font-weight:600;
                    "></span>
                    <span style="
                        background:#e6f4ea;
                        color:#2e7d32;
                        padding:4px 12px;
                        border-radius:20px;
                        font-size:13px;
                        font-weight:600;
                    ">✅ Available</span>
                </div>
            </div>
        </div>
    </div>

    <br>

    <label>Select Booking Date</label>
    @error('booking_date')
        <div class="error">{{ $message }}</div>
    @enderror

    <input type="hidden" name="booking_date" id="booking_date" value="{{ old('booking_date') }}">

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

    <div id="calendar" style="margin-bottom:20px;"></div>

    <br>

    <label>Number of Persons</label><br>
    <input type="number" name="persons" id="persons" value="{{ old('persons') }}" min="1">
    @error('persons')
        <div class="error">{{ $message }}</div>
    @enderror

    <!-- Persons warning -->
    <div id="persons-warning" style="
        display:none;
        color:#c0392b;
        font-size:13px;
        margin-top:-10px;
        margin-bottom:10px;
    "></div>

    <br><br>
    <button type="submit">Next →</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const roomSelect = document.getElementById('room_id');

    // Show room details when room is selected
    roomSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const card     = document.getElementById('room-details');

        if (this.value) {
            document.getElementById('room-name').textContent        = selected.dataset.name;
            document.getElementById('room-description').textContent = selected.dataset.description;
            document.getElementById('room-capacity').textContent    = '👥 Max ' + selected.dataset.capacity + ' persons';
            document.getElementById('persons').max                  = selected.dataset.capacity;
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }

        // Clear date when room changes
        document.getElementById('booking_date').value = '';
        document.getElementById('selected-date-display').innerHTML =
            '📅 No date selected — click a date on the calendar below';
        document.querySelectorAll('.selected-day').forEach(el => el.classList.remove('selected-day'));
    });

    // Persons validation
    document.getElementById('persons').addEventListener('input', function() {
        const selected = roomSelect.options[roomSelect.selectedIndex];
        const warning  = document.getElementById('persons-warning');

        if (roomSelect.value && parseInt(this.value) > parseInt(selected.dataset.capacity)) {
            warning.style.display = 'block';
            warning.textContent   = '⚠️ Exceeds room capacity of ' + selected.dataset.capacity + ' persons!';
        } else {
            warning.style.display = 'none';
        }
    });

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
                            color: '#c0392b',
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
            roomSelect.addEventListener('change', function() {
                calendar.refetchEvents();
                calendar.render();
            });
        });
});
</script>

<style>
.booked-day { background:#ffe5e5 !important; cursor:not-allowed !important; }
.selected-day { background:#fffaf5 !important; border:2px solid #6f4e37 !important; }
.fc-daygrid-day:not(.booked-day):hover { background:#f5eee6 !important; cursor:pointer; }
.fc-toolbar-title { color:#6f4e37 !important; font-weight:700 !important; }
.fc-button-primary { background:linear-gradient(135deg,#6f4e37,#c68e5b) !important; border:none !important; }
.fc-button-primary:hover { opacity:0.9 !important; }
</style>

@endsection