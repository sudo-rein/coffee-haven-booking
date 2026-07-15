@extends('layouts.app')

@section('content')

<div class="card">
    <h2>☕ Welcome {{ $customerName }}</h2>

    <p>
        Reserve your favorite Room and enjoy premium coffee,
        pastries, and a cozy atmosphere.
    </p>
</div>

<h3>Reservation Process</h3>

<ul>
    <li>Choose your Room details</li>
    <li>Upload reservation confirmation</li>
    <li>View reservation summary</li>
</ul>

<br>

<a href="/booking/details">
    <button>Reserve Now</button>
</a>


@endsection

