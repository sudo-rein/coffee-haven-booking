@extends('layouts.app')

@section('content')

<div style="display:flex; justify-content:space-between; align-items:center;">
    <h2>Create New Booking</h2>
    <a href="/admin/bookings">
        <button type="button" style="background:#999;">← Back</button>
    </a>
</div>

<br>

<form method="POST" action="/admin/bookings">
    @csrf

    <label>Customer Name</label><br>
    <input type="text" name="customer_name" value="{{ old('customer_name') }}" placeholder="Enter customer name">
    @error('customer_name')
        <div class="error">{{ $message }}</div>
    @enderror

    <label>Choose a Room</label><br>
    <select name="room_id">
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

    <label>Booking Date</label><br>
    <input type="date" name="booking_date" value="{{ old('booking_date') }}" min="{{ date('Y-m-d') }}">
    @error('booking_date')
        <div class="error">{{ $message }}</div>
    @enderror

    <label>Number of Persons</label><br>
    <input type="number" name="persons" value="{{ old('persons') }}" min="1">
    @error('persons')
        <div class="error">{{ $message }}</div>
    @enderror

    <br>
    <button type="submit">Create Booking</button>

</form>

@endsection