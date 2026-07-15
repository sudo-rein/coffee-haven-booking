<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;

class BookingController extends Controller
{
    public function start($customerName = null)
{
    $customerName = auth()->user()->name;
    session(['customer_name' => $customerName]);
    return view('booking.start', compact('customerName'));
}

    public function details()
    {
        $rooms = Room::all();
        return view('booking.details', compact('rooms'));
    }

    public function storeDetails(Request $request)
    {
        $request->validate([
            'room_id'      => 'required|exists:rooms,id',
            'persons'      => 'required|numeric|min:1',
            'booking_date' => 'required|date|after_or_equal:today',
        ]);

        $room = Room::findOrFail($request->room_id);

        // Check persons doesn't exceed room capacity
        if ($request->persons > $room->capacity) {
            return back()->withInput()->withErrors([
                'persons' => "This room fits a maximum of {$room->capacity} persons.",
            ]);
        }

        // Check if this room is already booked on this date
        $alreadyBooked = Booking::where('room_id', $request->room_id)
                                ->where('booking_date', $request->booking_date)
                                ->exists();

        if ($alreadyBooked) {
            return back()->withInput()->withErrors([
                'booking_date' => 'This room is already booked on this date. Please choose a different date or room.',
            ]);
        }

        // Auto-generate unique booking ID
        do {
            $bookingId = strtoupper(\Str::random(8));
        } while (Booking::where('booking_id', $bookingId)->exists());

        session([
            'booking_id'   => $bookingId,
            'room_id'      => $request->room_id,
            'room_name'    => $room->name,
            'persons'      => $request->persons,
            'booking_date' => $request->booking_date,
        ]);

        return redirect('/booking/confirmation');
    }

public function myBookings()
{
    $bookings = Booking::with('room')
                       ->where('user_id', auth()->id())
                       ->orderBy('booking_date', 'asc')
                       ->get();

    $calendarEvents = $bookings->map(function($b) {
        $isUpcoming = $b->booking_date >= \Carbon\Carbon::today()->toDateString();
        return [
            'title' => '☕ ' . ($b->room ? $b->room->name : $b->event_name),
            'date'  => $b->booking_date,
            'color' => $isUpcoming ? '#6f4e37' : '#bbb',
        ];
    });

    return view('booking.my-bookings', compact('bookings', 'calendarEvents'));
}

    public function confirmation()
    {
        if (!session()->has('booking_id')) {
            return redirect('/booking/details');
        }
        return view('booking.confirmation');
    }

   public function uploadConfirmation(Request $request)
{
    $request->validate([
        'confirmation_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);

    $path = $request->file('confirmation_file')->store('confirmations', 'public');

    session(['confirmation_file' => $path]);

    Booking::create([
        'booking_id'        => session('booking_id'),
        'customer_name'     => auth()->user()->name,
        'room_id'           => session('room_id'),
        'event_name'        => session('room_name'),
        'booking_date'      => session('booking_date'),
        'persons'           => session('persons'),
        'confirmation_file' => $path,
        'user_id'           => auth()->id(),
    ]);

    return redirect('/booking/summary');
}

    public function adminDashboard()
{
    $totalBookings = Booking::count();
    $totalUsers    = \App\Models\User::where('is_admin', false)->count();
    $bookings      = Booking::with('room')->orderBy('booking_date', 'asc')->get();

    return view('booking.admin-dashboard', compact('totalBookings', 'totalUsers', 'bookings'));
}






    public function summary()
    {
        if (!session()->has('confirmation_file')) {
            return redirect('/booking/confirmation');
        }
        return view('booking.summary');
    }

   public function adminBookings()
{
    $totalBookings = Booking::count();
    $totalUsers    = \App\Models\User::where('is_admin', false)->count();
    $bookings      = Booking::with('room')->orderBy('booking_date', 'asc')->get();
    return view('booking.admin', compact('bookings', 'totalBookings', 'totalUsers'));
}
    public function createBooking()
    {
        $rooms = Room::all();
        return view('booking.admin-create', compact('rooms'));
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|min:2',
            'room_id'       => 'required|exists:rooms,id',
            'persons'       => 'required|numeric|min:1',
            'booking_date'  => 'required|date|after_or_equal:today',
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($request->persons > $room->capacity) {
            return back()->withInput()->withErrors([
                'persons' => "This room fits a maximum of {$room->capacity} persons.",
            ]);
        }

        $alreadyBooked = Booking::where('room_id', $request->room_id)
                                ->where('booking_date', $request->booking_date)
                                ->exists();

        if ($alreadyBooked) {
            return back()->withInput()->withErrors([
                'booking_date' => 'This room is already booked on this date.',
            ]);
        }

        // Auto-generate unique booking ID
        do {
            $bookingId = strtoupper(\Str::random(8));
        } while (Booking::where('booking_id', $bookingId)->exists());

        Booking::create([
            'booking_id'        => $bookingId,
            'customer_name'     => $request->customer_name,
            'room_id'           => $request->room_id,
            'event_name'        => $room->name,
            'booking_date'      => $request->booking_date,
            'persons'           => $request->persons,
            'confirmation_file' => null,
        ]);

        return redirect('/admin/bookings')->with('success', 'Booking created successfully!');
    }

    public function editBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $rooms = Room::all();
        return view('booking.edit', compact('booking', 'rooms'));
    }

    public function updateBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $request->validate([
            'room_id'      => 'required|exists:rooms,id',
            'persons'      => 'required|numeric|min:1',
            'booking_date' => 'required|date',
        ]);

        $room = Room::findOrFail($request->room_id);

        if ($request->persons > $room->capacity) {
            return back()->withInput()->withErrors([
                'persons' => "This room fits a maximum of {$room->capacity} persons.",
            ]);
        }

        // Check if room+date is taken by another booking
        $alreadyBooked = Booking::where('room_id', $request->room_id)
                                ->where('booking_date', $request->booking_date)
                                ->where('id', '!=', $id)
                                ->exists();

        if ($alreadyBooked) {
            return back()->withErrors([
                'booking_date' => 'This room is already booked on this date.',
            ]);
        }

        $booking->update([
            'room_id'      => $request->room_id,
            'event_name'   => $room->name,
            'persons'      => $request->persons,
            'booking_date' => $request->booking_date,
        ]);

        return redirect('/admin/bookings')->with('success', 'Booking updated successfully!');
    }

    public function destroyBooking($id)
    {
        Booking::findOrFail($id)->delete();
        return redirect('/admin/bookings')->with('success', 'Booking deleted successfully!');
    }
}