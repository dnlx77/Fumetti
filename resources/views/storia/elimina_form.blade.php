@extends('layouts.main')
@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('storia.elimina_execute', $id_storia) }}">
    @csrf
    <button type="submit" class="btn btn-primary">Elimina</button>
@endsection
</form>