@extends('layouts.main')
@section('content')

<div class="inserisci-menu-wrapper">
    <ul class="inserisci-menu">
        @foreach ($storie AS $storia)
            <li><a href="{{ route('storia.details', $storia->id) }}">{{ $storia->nome }}</a></li>
        @endforeach
    </ul>
</div>
@endsection