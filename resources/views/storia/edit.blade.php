@extends('layouts.main')
@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('storia.update', $storia->id) }}">
    @csrf
	@include('storia.form', [])
		<button type="submit" class="btn btn-primary">Aggiorna</button>
</form>
@endsection