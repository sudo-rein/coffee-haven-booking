<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookingApiController;

// Auth endpoints
Route::post('/login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (!\Illuminate\Support\Facades\Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Invalid credentials.'], 401);
    }

    $user = \App\Models\User::where('email', $request->email)->first();
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json(['token' => $token]);
});

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out.']);
})->middleware('auth:sanctum');

// Booking endpoints — all protected by Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/bookings',         [BookingApiController::class, 'index']);
    Route::post('/bookings',        [BookingApiController::class, 'store']);
    Route::get('/bookings/{id}',    [BookingApiController::class, 'show']);
    Route::put('/bookings/{id}',    [BookingApiController::class, 'update']);
    Route::delete('/bookings/{id}', [BookingApiController::class, 'destroy']);
});

// Public route — no auth needed so calendar can load dates
Route::get('/booked-dates', function () {
    $booked = \App\Models\Booking::select('booking_date', 'room_id')
                                  ->with('room')
                                  ->get()
                                  ->map(function ($b) {
                                      return [
                                          'date'    => $b->booking_date,
                                          'room'    => $b->room->name ?? 'Room',
                                          'room_id' => $b->room_id,
                                      ];
                                  });
    return response()->json($booked);
});