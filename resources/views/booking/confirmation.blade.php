@extends('layouts.app')

@section('content')

<h2>Upload Confirmation ☕</h2>

<form method="POST" enctype="multipart/form-data" action="/booking/confirmation">
@csrf

<input type="file" name="confirmation_file">

@error('confirmation_file')
<p class="error">{{ $message }}</p>
@enderror

<button type="submit">Upload</button>

</form>

@endsection