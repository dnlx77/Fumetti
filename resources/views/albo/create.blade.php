@extends('layouts.main')
@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('albo.store') }}">
    @csrf
	@include('albo.form', [])
		<button type="submit" class="btn btn-primary">Aggiungi</button>
</form>
@endsection