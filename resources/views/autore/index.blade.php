@extends('layouts.main')
@section('content')

<div class="table-container">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>Cognome</th>
                <th>Nome</th>
                <th>Pseudonimo</th>
                <th>Modifica</th>
                <th>Elimina</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($autore AS $autori)
                <tr>
                    <td>{{ $autori->cognome }}</td>
                    <td>{{ $autori->nome }}</td>
                    <td>{{ $autori->pseudonimo }}</td>
                    <td><a href="{{ route('autore.edit', $autori->id) }}">modifica</a></td>
                    <td><a href="{{ route('autore.elimina_form', $autori->id) }}">elimina</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $autore->appends(['cerca_in' => $cerca_in, 'cerca_per' => $cerca_per, 'ricerca' => $search, 'tipo_ricerca' => $tipo_ricerca])->links() }}

</div>
@endsection