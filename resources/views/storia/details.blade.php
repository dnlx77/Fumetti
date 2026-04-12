@extends('layouts.main')
@section('content')

<div class="container albo-details">
    <!-- Riga del titolo -->
    <div class="row justify-content-center">
        <h1> {{ $storia->nome }} </h1>

    </div>
    <div class="row">
        <div class="col-4">
            <h5>Autori</h5>
            @foreach ($lista_ruoli AS $id_ruolo => $lista_autore)
                <span>{{ $lista_autore['ruolo'] }}</span>
                <ul>
                    @foreach ($lista_autore['nome'] AS $id_autore => $autore)
                        <li><a href="{{ route('autore.storia', $id_autore) }}"><span>{{ $autore }}</span></a></li>
                    @endforeach
                </ul>
            @endforeach
            <h5>Data Lettura</h5>
            <span>
                <ul class="multi-row">
                    @if ($storia->dateLettura->isEmpty())
                        {{ 'Da leggere '}}
                    @else
                        @foreach ($storia->dateLettura AS $data)
                        <li> {{ date('d/m/Y', strtotime($data->data_lettura)) }}<a href="{{ route('storia.remove_read_date', array($storia, $data->data_lettura))}}"><i class="bi bi-eraser-fill"></i></a></li>
                        
                        @endforeach
                    @endif
                </ul>
            </span>
            <button id="modal-button" type="button" class="btn btn-primary modale-data" data-toggle="modal" data-target="#storiaModal">
                Data lettura
            </button>
        </div>
        <div class="col-4">
            <h5>Trama</h5>
            <span>{{ $storia->trama }}</span>
        </div>    
        <div class="col-4">        
            <h5>Albi</h5>
            @foreach ($albi AS $albo)   
                <a href="{{ route('albo.details', $albo->id) }}"><img class="img-thumbnail" src="{{ url('storage/'.$albo->filename) }}" alt="{{ $albo->filename }}"></a>
            @endforeach
           
           {{ $albi->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="storiaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Inserisci la data di lettura della storia</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form enctype="multipart/form-data" method="post" action="{{ route('storia.set_read_date', $storia->id) }}">
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
        $('[name=data_lettura]').datepicker({
        format: 'dd-mm-yyyy',
        todayHighlight: true,
        weekStart: 1
        });
    });
</script>

@endsection