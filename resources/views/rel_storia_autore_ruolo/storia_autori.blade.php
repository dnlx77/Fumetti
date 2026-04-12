@extends('layouts.main')
@section('content')
<a href="{{ route('storia.aggiungi_autore', $storia->id) }}">Aggiungi autori</a> di {{ $storia->nome }}<br/>
<div class="table-container">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>Autori</th>
                <th>Ruoli</th>
                <th>Modifica</th>
                <th>Elimina</th>
            </tr>
        </thead>
        
        <tbody>
            @foreach ($info_autori AS $id_autore => $info_autore)
                <tr>
                    <td>{{ $info_autore['nome'] }}</td>
                    <td>
                        <ul class="multi-row">
                            @foreach ($info_autore['ruoli'] as $ruolo)
                                <li>{{ $ruolo }}</li>
                            @endforeach
                        </ul>
                    </td>
                    <td><a href="{{ route('storia.aggiungi_autore', [$storia->id, 'autore_id' => $id_autore]) }}">Modifica</a></td>
                    <td><a href="{{ route('storia.elimina_autore_form', [$storia->id, $id_autore]) }}">Elimina</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection