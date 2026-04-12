@extends('layouts.main')
@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('albo.store_storia', $id_albo) }}">
    @csrf
    @include('rel_storia_albo.form', [])
    <button type="submit" class="btn btn-primary">Salva</button>
</form>
@endsection