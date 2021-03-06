@extends('layouts.main')
@section('content')
<br><br><a href="{{ route('collana.create') }}">Definisci una nuova collana</a><br><br>

<table>
    <thead>
        <tr>
            <th>Collana</th>
            <th>Numero di albi</th>
            <th>Stato collana</th>
            <th>Modifica</th>
            <th>Elimina</th>
            <th>Data inserimento</th>
            <th>Data aggiornamento</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($collane AS $collana)
            <tr>
                <td>{{ $collana->nome }}</td>
                <td>{{ $collana->num_albi }}</td>
                <td>{{ \App\Enums\TipoCollanaEnum::getDescription($collana->stato) }}</td>
                <td><a href="{{ route('collana.edit', $collana->id) }}">modifica</a></td>
                <td><a href="{{ route('collana.elimina_form', $collana->id) }}">elimina</a></td>
                <td>{{ $collana->created_at }}</td>
                <td>{{ $collana->updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection