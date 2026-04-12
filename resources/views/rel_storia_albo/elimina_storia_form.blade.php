@extends('layouts.main')
@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('albo.elimina_storia_execute', [$id_albo, $id_storia]) }}">
    @csrf
    <button type="submit" class="btn btn-primary">Elimina</button>
@endsection
</form>