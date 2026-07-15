@extends('layouts.app')

@section('content')

<h2>Edit Booking</h2>

<form method="POST" action="/admin/bookings/{{ $booking->id }}">
    @csrf
    @method('PUT')

    <label>Customer Name</label><br>
    <input type="text" value="{{ $booking->customer_name }}" readonly><br>

    <label>Booking ID</label><br>
    <input type="text" value="{{ $booking->booking_id }}" readonly><br>

    <label>Choose a Room</label><br>
    <select name="room_id">
        @foreach($rooms as $room)
            <option value="{{ $room->id }}" {{ $booking->room_id == $room->id ? 'selected' : '' }}>
                {{ $room->name }} (Max: {{ $room->capacity }} persons)
            </option>
        @endforeach
    </select>
    @error('room_id')
        <div class="error">{{ $message }}</div>
    @enderror

    <label>Booking Date</label><br>
    <input type="date" name="booking_date" value="{{ old('booking_date', $booking->booking_date) }}">
    @error('booking_date')
        <div class="error">{{ $message }}</div>
    @enderror

    <label>Number of Persons</label><br>
    <input type="number" name="persons" value="{{ old('persons', $booking->persons) }}" min="1">
    @error('persons')
        <div class="error">{{ $message }}</div>
    @enderror

    <br>
    <button type="submit">Save Changes</button>
    <a href="/admin/bookings">
        <button type="button" style="background:#999;">Cancel</button>
    </a>

</form>

@endsection