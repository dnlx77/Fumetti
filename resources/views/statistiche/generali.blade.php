@extends('layouts.main')
@section('content')

<div class="table-container">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>Campo</th>
                <th>Valore</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($statistiche AS $key => $statistica)
                <tr>
                    <td>Numero di {{ $key }}</td>
                    <td> {{ $statistica }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection