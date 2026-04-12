@extends('layouts.main')
@section('content')
    
    <div class="table-container">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th>Copertina</th>
                <th>Numero albo</th>
                <th>Titolo</th>
                <th>Editore</th>
                <th>Data di pubblicazione</th>
                <th>Data di lettura</th>
                <th>Collana</th>
                <th>Titoli</th>
                <th>Modale link</th>
                <th>Modale button</th>
                <th>Modifica</th>
                <th>Elimina</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($albi AS $albo)
                <tr>
                    <td><a href="{{ route('albo.details', $albo->id) }}">
                        <div class="immagine-tabella-wrapper">
                          <img src="{{ url('storage/'.$albo->filename) }}" class="card-img-top" alt="{{ $albo->filename }}">
                        </div></a>
                    </td>
                    <td>{{ $albo->numero }}</td>
                    <td>{{ $albo->titolo }}</td>
                    <td>{{ $albo->editore->nome }}</td>
                    <td>{{ date('d/m/Y', strtotime($albo->data_pubblicazione)) }}</td>
                    <td><ul class="multi-row">
                        @if ($albo->dateLettura->isEmpty())
                            {{ 'Da leggere '}}
                        @else
                            @foreach ($albo->dateLettura AS $data)
                                <li> {{ date('d/m/Y', strtotime($data->data_lettura)) }}</li>
                            @endforeach
                        @endif
                    </ul></td>
                    <td>{{ $albo->collana ? $albo->collana->nome : '' }}</td>
                    <td><a href="{{ route('albo.storia', $albo->id) }}">storie</a></td>
                    <td><a data-target="#storieModal" class="modale-storie" data-toggle="modal" data-id-albo="{{ $albo->id }}" href="#storieModal">storie</a></td>
                    <td><button id="modal-button" type="button" class="btn btn-primary modale-storie" data-toggle="modal" data-target="#storieModal" data-id-albo="{{ $albo->id }}">
                        Storie
                        </button>
                    <td><a href="{{ route('albo.edit', $albo->id) }}">modifica</a></td>
                    <td><a href="{{ route('albo.elimina_form', $albo->id) }}">elimina</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>

            {{ $albi->appends(['cerca_in' => $cerca_in, 'cerca_per' => $cerca_per, 'ricerca' => $search, 'tipo_ricerca' => $ricerca_esatta, 'data_pub_iniziale' => $data_pub_iniziale, 'data_pub_finale' => $data_pub_finale, 'stato_lettura' => $stato_lettura])->links() }}

    </div>

    <!-- Modal -->
    <div class="modal fade" id="storieModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('.modale-storie').on('click', (function(){
                $.ajax({
                    url:"/albo/" + $(this).attr('data-id-albo') + "/services/get-storie",
                    method:"GET",
                    data:{},
                    dataType: 'json',
                    success:function(result){
                        $('.modal-title').html(result.titolo);
                        $('.modal-body').html(result.storie);
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