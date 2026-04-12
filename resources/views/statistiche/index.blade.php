@extends('layouts.main')
@section('content')

<div class="inserisci-menu-wrapper">
    <ul class="inserisci-menu">
        <li><a href="{{ route('statistiche.generali') }}">Statistiche generali</a></li>
        <li><a href="{{ route('statistiche.albi_mese', date("Y")) }}">Albi pubblicati per anno</a></li>
        <li><a href="{{ route('statistiche.albi_anno') }}">Distribuzione degli albi negli anni</a></li>
    </ul>
</div>
@endsection