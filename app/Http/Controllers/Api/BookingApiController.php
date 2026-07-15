<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Str;

class BookingApiController extends Controller
{
    // GET /api/bookings — list own bookings
    public function index(Request $request)
    {
        $bookings = Booking::with('room')
                           ->where('user_id', $request->user()->id)
                           ->orderBy('booking_date', 'asc')
                           ->get();

        return response()->json($bookings);
    }

    // GET /api/bookings/{id} — view one booking
    public function show(Request $request, $id)
    {
        $booking = Booking::with('room')->findOrFail($id);

        $this->authorize('view', $booking);

        return response()->json($booking);
    }

    // POST /api/bookings — create booking
    public function store(Request $request)
    {
        $request->validate([
            'room_id'      => 'required|exists:rooms,id',
            'persons'      => 'required|numeric|min:1',
            'booking_date' => 'required|date|after_or_equal:today',
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($request->persons > $room->capacity) {
            return response()->json([
                'message' => "This room fits a maximum of {$room->capacity} persons."
            ], 422);
        }

        $alreadyBooked = Booking::where('room_id', $request->room_id)
                                ->where('booking_date', $request->booking_date)
                                ->exists();

        if ($alreadyBooked) {
            return response()->json([
                'message' => 'This room is already booked on this date.'
            ], 422);
        }

        do {
            $bookingId = strtoupper(Str::random(8));
        } while (Booking::where('booking_id', $bookingId)->exists());

        $booking = Booking::create([
            'booking_id'    => $bookingId,
            'customer_name' => $request->user()->name,
            'room_id'       => $request->room_id,
            'event_name'    => $room->name,
            'booking_date'  => $request->booking_date,
            'persons'       => $request->persons,
            'user_id'       => $request->user()->id,
        ]);

        return response()->json($booking->load('room'), 201);
    }

    // PUT /api/bookings/{id} — update booking
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $this->authorize('update', $booking);

        $request->validate([
            'room_id'      => 'required|exists:rooms,id',
            'persons'      => 'required|numeric|min:1',
            'booking_date' => 'required|date|after_or_equal:today',
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($request->persons > $room->capacity) {
            return response()->json([
                'message' => "This room fits a maximum of {$room->capacity} persons."
            ], 422);
        }

        $alreadyBooked = Booking::where('room_id', $request->room_id)
                                ->where('booking_date', $request->booking_date)
                                ->where('id', '!=', $id)
                                ->exists();

        if ($alreadyBooked) {
            return response()->json([
                'message' => 'This room is already booked on this date.'
            ], 422);
        }

        $booking->update([
            'room_id'      => $request->room_id,
            'event_name'   => $room->name,
            'persons'      => $request->persons,
            'booking_date' => $request->booking_date,
        ]);

        return response()->json($booking->load('room'));
    }

    // DELETE /api/bookings/{id} — delete booking
    public function destroy(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $this->authorize('delete', $booking);

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully.']);
    }
}