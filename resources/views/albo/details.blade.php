@extends('layouts.main')
@section('content')

    <div class="container albo-details">
        <!-- Riga del titolo -->
        <div class="row justify-content-center">
            <h1> {{ $albo->titolo }} </h1>
        </div>
        <!-- Seconda riga divisa in 2 colonne -->
        <div class="row">
            <!-- Colonna di sinistra contiene l'imamgine della copertina -->
            <div class="col-6"><img src="{{ url('storage/'.$albo->filename) }}" class="card-img-top img-albo-details" alt="{{ $albo->filename }}"></div>
            <!-- Colonna di destra tutti i dati dell'albo -->
            <div class="col-6">
                <div class="row">
                    <div class="col-6"><h5>Numero albo</h5><span>{{ $albo->numero }}</span></div>
                    @if ($albo->prezzo)
                        <div class="col-6"><h5>Prezzo</h5><span>{{ $albo->prezzo }} &euro;</span></div>
                    @elseif ($albo->prezzo_lire)
                        <div class="col-6"><h5>Prezzo</h5><span>{{ $albo->prezzo_lire }} Lire ({{ round(($albo->prezzo_lire / 1936.27), 2) }} &euro;)</span></div>
                    @else
                        <div class="col-6"><h5>Prezzo</h5></div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-6"><h5>Data di pubblicazione</h5><span>{{ date('d/m/Y', strtotime($albo->data_pubblicazione)) }}</span></div>
                    <div class="col-6"><h5>Data di lettura</h5>
                        <span>
                            <ul class="multi-row">
                                @if ($albo->dateLettura->isEmpty())
                                    {{ 'Da leggere '}}
                                @else
                                    @foreach ($albo->dateLettura AS $data)
                                    <li> {{ date('d/m/Y', strtotime($data->data_lettura)) }}<a href="{{ route('albo.remove_read_date', array($albo, $data->data_lettura))}}"><i class="bi bi-eraser-fill"></i></a></li>
                                    
                                    @endforeach
                                @endif
                            </ul>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6"><h5>Editore</h5><span>{{ $albo->editore->nome }}</span></div>
                    <div class="col-6"><h5>Collana</h5><span>{{ $albo->collana ? $albo->collana->nome : 'Nessuna collana' }}</span></div>
                </div>
                <div class="row">
                    <div class="col-6"><h5>Codice a barre</h5>{{ $albo->barcode }}</div>
                    <div class="col-6"><h5>Numero di pagine</h5>{{ $albo->num_pagine }}</div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <h5>Autori copertina</h5>
                        <ul class="multi-row">
                            @foreach ($albo->autoriCopertina AS $autoreCopertina)
                                @if ($autoreCopertina->pseudonimo)
                                    <li><span>{{ $autoreCopertina->nome }} '{{ $autoreCopertina->pseudonimo }}' {{ $autoreCopertina->cognome }}</span></li>
                                @else
                                    <li><span>{{ $autoreCopertina->nome }} {{ $autoreCopertina->cognome }}</span></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-6">
                        <h5>Storie albo</h5>
                        <ul class="multi-row">
                            @foreach ($albo->storie AS $storia)
                                <li><a href="{{ route('storia.details', $storia) }}"> {{ $storia->nome }} </a>
                                    <a data-target="#tramaModal" class="modale-trama" data-toggle="modal" data-id-storia="{{ $storia->id }}" href="#tramaModal"><i class="bi bi-info-circle-fill"></i></a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <button id="modal-button" type="button" class="btn btn-primary modale-data" data-toggle="modal" data-target="#dataModal">
                            Data lettura
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="tramaModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Titolo della storia</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Trama della storia</div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dataModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Inserisci la data di lettura dell'albo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form enctype="multipart/form-data" method="post" action="{{ route('albo.set_read_date', $albo->id) }}">
                    <div class="modal-body">
                    
                        @csrf
                        <label for="data_lettura">Data lettura:</label>
                        <input type="text" class="form-control" name="data_lettura" value=""/>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>  
                </form>
            </div>
        </div>
    </div>

    <!-- Script gestione modal -->
    <script>
        $(document).ready(function(){
            $('.modale-trama').on('click', (function(){
                $.ajax({
                    url:"/storia/" + $(this).attr('data-id-storia') + "/services/get-trame",
                    method:"GET",
                    data:{},
                    dataType: 'json',
                    success:function(result){
                        $('.modal-title').html(result.titolo);
                        $('.modal-body').html(result.trama);
                        console.log(result);
                    },
                    error:function() {
                        console.log();
                    }
                });
            }));

            $('[name=data_lettura]').datepicker({
            format: 'dd-mm-yyyy',
            todayHighlight: true,
            weekStart: 1
            });
        });
    </script>
@endsection