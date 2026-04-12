@extends('layouts.main')
@section('content')
<div class="table-container">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>Collana</th>
                <th>Numero albi</th>
                <th>Modifica</th>
                <th>Elimina</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($collane AS $collana)
                <tr>
                    <td>{{ $collana->nome }}</td>
                    <td>{{ $num_albi_in_collana[$collana->id] }}</td>
                    <td><a href="{{ route('collana.edit', $collana->id) }}">modifica</a></td>
                    <td><a href="{{ route('collana.elimina_form', $collana->id) }}">elimina</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection