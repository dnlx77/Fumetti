@extends('layouts.main')
@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('albo.elimina_execute', $id_albo) }}">
    @csrf
    <button type="submit" class="btn btn-primary">Elimina</button>
@endsection
</form>