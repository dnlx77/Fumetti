@extends('layouts.main')
@section('content')

<h3>Elenco storie di {{ $autore->nome }} {{ $autore->cognome }}</h3>

<table class="table table-hover table-bordered">
    <thead>
        <tr>
            <th>Ruoli</th>
            <th>Storie</th>
            <th>Numero storie</th>
        </tr>
    </thead>
    
    <tbody>
        @foreach ($info_ruoli AS $id_ruolo => $info_ruoli)
            <tr>
                <td>{{ $info_ruoli['ruolo'] }}</td>
                <td>
                    <ul class="multi-row">
                        @foreach ($info_ruoli['titoli'] as $id_storia => $storia)
                           <li><a href="{{ route('storia.details', $id_storia) }}">{{ $storia }}</a></li>
                        @endforeach
                    </ul>
                </td>
                <td>{{ count($info_ruoli['titoli']) }} </td>
            </tr>
        @endforeach
        
    </tbody>
</table>
@endsection