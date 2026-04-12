@extends('layouts.main')
@section('content')
<div class="table-container">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>Ruolo</th>
                <th>Modifica</th>
                <th>Elimina</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ruoli AS $ruolo)
                <tr>
                    <td>{{ $ruolo->descrizione }}</td>
                    <td><a href="{{ route('ruolo.edit', $ruolo->id) }}">modifica</a></td>
                    <td><a href="{{ route('ruolo.elimina_form', $ruolo->id) }}">elimina</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection