@extends('layouts.main')
@section('content')
    <br><br><a href="{{ route('albo.create') }}">Inserisci un nuovo albo</a><br><br>
    <div class="table-container">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>Copertina</th>
                <th>Numero di pagine</th>
                <th>Prezzo</th>
                <th>Codice a barre</th>
                <th>Numero albo</th>
                <th>Titolo</th>
                <th>Editore</th>
                <th>Data di pubblicazione</th>
                <th>Collana</th>
                <th>Titoli</th>
                <th>Modale</th>
                <th>Modifica</th>
                <th>Elimina</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($albi AS $albo)
                <tr>
                    <td>
                        <div class="immagine-tabella-wrapper">
                          <img src="{{ url('storage/'.$albo->filename) }}" class="card-img-top" alt="{{ $albo->filename }}">
                        </div>
                    </td>
                    <td>{{ $albo->num_pagine }}</td>
                    <td>{{ $albo->prezzo }}</td>
                    <td>{{ $albo->barcode }}</td>
                    <td>{{ $albo->numero }}</td>
                    <td>{{ $albo->titolo }}</td>
                    <td>{{ $albo->editore->nome }} </td>
                    <td>{{ $albo->data_pubblicazione }}</td>
                    <td>{{ $albo->collana['nome'] }}</td>
                    <td><a href="{{ route('albo.storia', $albo->id) }}">storie</a></td>
                    <td><button id="modal-button" type="button" class="btn btn-primary" data-toggle="modal" data-target="#storieModal">
                          Storie
                        </button>
                    <td><a href="{{ route('albo.edit', $albo->id) }}">modifica</a></td>
                    <td><a href="{{ route('albo.elimina_form', $albo->id) }}">elimina</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="storieModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $("#modal-button").on('click', (function(){
                $.ajax({
                    url:"/albo/services/get-storie",
                    method:"GET",
                    data:{},
                    dataType: 'json',
                    success:function(result){
                        console.log(result);
                    },
                    error:function() {
                        console.log();
                    }
                });
            }));
        });
    </script>
@endsection