@extends('layouts.main')
@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('storia.store') }}">
    @csrf
	@include('storia.form', [])
		<button type="submit" class="btn btn-primary">Aggiungi</button>
</form>
@endsection