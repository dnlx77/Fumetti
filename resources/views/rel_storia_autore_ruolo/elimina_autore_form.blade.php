@extends('layouts.main')
@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('storia.elimina_autore_execute', array($id_storia, $id_autore)) }}">
    @csrf
    <button type="submit" class="btn btn-primary">Elimina</button>
@endsection
</form>