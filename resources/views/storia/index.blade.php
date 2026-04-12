@extends('layouts.main')
@section('content')

<div class="table-container">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>Titolo storia</th>
                <th>Trama</th>
                <th>Data lettura</th>
                <th>Stato storia</th>
                <th>Modifica</th>
                <th>Autori</th>
                <th>Elimina</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($storie AS $storia)
                <tr>
                    <td><a href="{{ route('storia.details', $storia->id) }}">{{ $storia->nome }}</a></td>
                    <td>{{ $storia->trama }}</td>
                    <td><ul class="multi-row">
                        @if ($storia->dateLettura->isEmpty())
                            {{ 'Da leggere '}}
                        @else
                            @foreach ($storia->dateLettura AS $data)
                                <li> {{ date('d/m/Y', strtotime($data->data_lettura)) }}</li>
                            @endforeach
                        @endif
                    </ul></td>                   
                    <td>{{ \App\Enums\TipoStoriaEnum::getDescription($storia->stato) }}</td>
                    <td><a href="{{ route('storia.edit', $storia->id) }}">modifica</a></td>
                    <td><a href="{{ route('storia.autore', $storia->id) }}">autori</a></td>
                    <td><a href="{{ route('storia.elimina_form', $storia->id) }}">elimina</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $storie->appends(['cerca_in' => $cerca_in, 'cerca_per' => $cerca_per, 'ruoli' => $ruoli, 'ricerca' => $search, 'tipo_ricerca' => $ricerca_esatta, 'data_pub_iniziale' => $data_pub_iniziale, 'data_pub_finale' => $data_pub_finale, 'stato_lettura' => $stato_lettura])->links() }}
</div>
@endsection