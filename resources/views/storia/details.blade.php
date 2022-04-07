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
                    @foreach ($lista_autore['nome'] as $autore)
                        <li><span>{{ $autore }}</span></li>
                    @endforeach
                </ul>
            @endforeach
        </div>
        <div class="col-4">
            <h5>Trama</h5>
            <span>{{ $storia->trama }}</span>
        </div>    
        <div class="col-4">        
            <h5>Albi</h5>
            @foreach ($albi AS $albo)            
                <img class="img-thumbnail" src="{{ url('storage/'.$albo->filename) }}" alt="{{ $albo->filename }}">
           @endforeach
           
           {{ $albi->links() }}
        </div>
    </div>
</div>
@endsection