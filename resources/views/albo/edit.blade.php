@extends('layouts.main')
@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('albo.update', $albo->id) }}">
    @csrf
	@include('albo.form', [])
		<button type="submit" class="btn btn-primary">Aggiorna</button>
</form>
@endsection