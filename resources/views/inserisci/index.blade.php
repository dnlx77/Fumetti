@extends('layouts.main')
@section('content')

<div class="inserisci-menu-wrapper">
    <ul class="inserisci-menu">
        <li><a href="{{ route('autore.create') }}">Inserisci un autore</a></li>
        <li><a href="{{ route('albo.create') }}">Inserisci un albo</a></li>
        <li><a href="{{ route('storia.create') }}">Inserisci una storia</a></li>
        <li><a href="{{ route('ruolo.create') }}">Inserisci un ruolo</a></li>
        <li><a href="{{ route('editore.create') }}">Inserisci un editore</a></li>
        <li><a href="{{ route('collana.create') }}">Inserisci una collana</a></li>
    </ul>
</div>
@endsection