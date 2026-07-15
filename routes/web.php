<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// Redirect dashboard to correct page based on role
Route::get('/dashboard', function () {
    if (auth()->user()->is_admin) {
        return redirect('/admin/bookings');
    }
    return redirect('/my-bookings');
})->middleware(['auth', 'verified'])->name('dashboard');




// Booking routes — auth required
Route::middleware('auth')->group(function () {
    Route::get('/booking/start/{customerName}', [BookingController::class, 'start']);
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);
    Route::get('/booking/details', [BookingController::class, 'details']);
    Route::post('/booking/details', [BookingController::class, 'storeDetails']);

    Route::get('/booking/confirmation', [BookingController::class, 'confirmation']);
    Route::post('/booking/confirmation', [BookingController::class, 'uploadConfirmation']);

    Route::get('/booking/summary', [BookingController::class, 'summary']);

  Route::get('/booking/reset', function () {
    // Only clear booking data, not auth session
    session()->forget([
        'booking_id',
        'room_id',
        'room_name',
        'persons',
        'booking_date',
        'confirmation_file',
        'customer_name',
    ]);
    return redirect('/booking/details');
});
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin only routes
    Route::get('/admin/bookings',           [BookingController::class, 'adminBookings']);
    Route::get('/admin/bookings/create',    [BookingController::class, 'createBooking']);
    Route::post('/admin/bookings',          [BookingController::class, 'storeBooking']);
    Route::get('/admin/bookings/{id}/edit', [BookingController::class, 'editBooking']);
    Route::put('/admin/bookings/{id}',      [BookingController::class, 'updateBooking']);
    Route::delete('/admin/bookings/{id}',   [BookingController::class, 'destroyBooking']);
});

require __DIR__.'/auth.php';