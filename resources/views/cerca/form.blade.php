@section ('select_tipo_ricerca')
    <select class="tipo-ricerca-select" name="tipo_ricerca">
        <option value="iniziaPer">Inizia per</option>
        <option value="contiene">Contiene</option>
        <option value="esatta">Esatta</option>
    </select>
@endsection

@section ('radio-stato-lettura')
    <input type="radio" class="radio-lettura" id="da-leggere" name="stato_lettura" value="leggere">
    <label for="da leggere">Da leggere</label>
    <input type="radio" class="radio-lettura" id="letti" name="stato_lettura" value="letti">
    <label for="letti">Letti</label>
    <input type="radio" class="radio-lettura" id="tutto" name="stato_lettura" value="tutto">
    <label for="tutto">Tutto</label>
@endsection

<div class="form-group">
    <label for="cerca">Cerca:</label>
    <select id="cerca-select" name="cerca_in">
    @foreach ($lista_campi_ricerca as $current_campo)
        <option value="{{ $current_campo }}">{{ $current_campo }}</option>
    @endforeach
    </select>

    <label for="per">per</label>

    <select class="select-class" id="albo-cerca-per-select" name="cerca_per">
        @foreach ($lista_campi_per_albo as $current_per_albo)
            <option value="{{ $current_per_albo }}">{{ $current_per_albo }}</option>
        @endforeach
    </select>

    <select class="select-class" id="storia-cerca-per-select" name="cerca_per">
        @foreach ($lista_campi_per_storia as $current_per_storia)
            <option value="{{ $current_per_storia }}">{{ $current_per_storia }}</option>
        @endforeach
    </select>

    <select class="select-class" id="autore-cerca-per-select" name="cerca_per">
        @foreach ($lista_campi_per_autore as $current_per_autore)
            <option value="{{ $current_per_autore }}">{{ $current_per_autore }}</option>
        @endforeach
    </select>

    <select class="select-class" id="cerca-collane" name="ricerca">
        @foreach ($lista_collane as $collana)
            <option value="{{ $collana->id }}">{{ $collana->nome}}</option>
        @endforeach
    </select>

    <div id="div-select-cerca-storie-autori">
        <select class="select-class" id="cerca-storie-autori" name="ricerca">
            @foreach ($lista_autori as $autori)
                <option value="{{ $autori->id }}">{{ $autori->cognome}} @if ($autori->pseudonimo)'{{$autori->pseudonimo}}' @endif {{ $autori->nome}}</option>
            @endforeach
        </select>

        <select class="select-class" id="cerca-storie-ruoli" name="ruoli">
            <option value=""></option>
            @foreach ($lista_ruoli as $ruoli)
                <option value="{{ $ruoli->id }}">{{ $ruoli->descrizione}}</option>
            @endforeach
        </select>
    </div>

    <div id="label-cerca">
        <label class="cerca-label" for="ricerca">albo da cercare:</label>
        <input type="search" class="form-control" name="ricerca"/>
    </div>

    <div id="tipo-ricerca">
        <label for="tipo-ricerca-select">Tipo di ricerca</label>
        @yield('select_tipo_ricerca')
    </div>

    <div id="stato-lettura">
        <label for="radio-stato-lettura">Cerca:</label>
        @yield('radio-stato-lettura')
    </div>
   
    <div id="data-pubblicazione">
        <label id="label-data-iniziale" for="data pubblicazione">Data iniziale di pubblicazione:</label>
        <input class="data-pub" type="text" class="form-control" name="data_pub_iniziale"/>
        <label id="label-data-finale" for="data pubblicazione">Data finale di pubblicazione:</label>
        <input class="data-pub" type="text" class="form-control" name="data_pub_finale"/>
    </div>
</div>

<script>
    $(document).ready(function(){

        $('.data-pub').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            weekStart: 1
        })

        $('#cerca-storie-autori').select2();
        $('#cerca-storie-ruoli').select2();

        /* Per default visualizziamo la ricerca per albo */

        $('.select-class').hide();
        $('#div-select-cerca-storie-autori').hide();
        $('#da-leggere').prop('checked', true);
        $('.select-class').prop('disabled' ,true);
        $('#albo-cerca-per-select').show();
        $('#albo-cerca-per-select').prop('disabled', false);

        /* Modifichiamo il form a seconda di cosa cerchiamo (es. albi, autori, ...) */

        $('#cerca-select').on('change', function(){
            
            var valore_select = $('#cerca-select :selected').text();

            console.log(valore_select);

            switch(valore_select){
                case 'autori':
                    $('.select-class').hide();
                    $('#div-select-cerca-storie-autori').hide();
                    $('.select-class').prop('disabled', true);
                    $('#stato-lettura').hide();
                    $('.radio-lettura').prop('disabled', true);
                    $('#data-pubblicazione').hide();
                    $('.data-pub').prop('disabled', true);
                    $('#label-cerca').show();
                    $('#label-cerca :input').prop('disabled', false);
                    $('#autore-cerca-per-select').show();
                    $('#autore-cerca-per-select').prop('disabled', false);
                    $('#autore-cerca-per-select').val("cognome").change();
                    $('#tipo-ricerca').show();
                    $('#tipo-ricerca *').contents().prop('disabled', false);
                    break;

                case 'storie':
                    $('.select-class').hide();
                    $('#div-select-cerca-storie-autori').hide();
                    $('.select-class').prop('disabled', true);
                    $('#label-cerca').show();
                    $('#label-cerca :input').prop('disabled', false);
                    $('#label-data-iniziale').text("Data iniziale di lettura:");
                    $('#label-data-finale').text("Data finale di lettura:");
                    $('#stato-lettura').show();
                    $('.radio-lettura').prop('disabled', false);
                    $('#da-leggere').prop('checked', true);
                    $('#data-pubblicazione').show();
                    $('.data-pub').prop('disabled', false);
                    $('#storia-cerca-per-select').show();
                    $('#storia-cerca-per-select').prop('disabled', false);
                    $('#storia-cerca-per-select').val("nome").change();
                    $('#tipo-ricerca').show();
                    $('#tipo-ricerca *').contents().prop('disabled', false);
                    break;

                case 'albi':
                    $('.select-class').hide();
                    $('#div-select-cerca-storie-autori').hide();
                    $('.select-class').prop('disabled', true);
                    $('#label-cerca').show();
                    $('#label-cerca :input').prop('disabled', false);
                    $('#stato-lettura').show();
                    $('.radio-lettura').prop('disabled', false);
                    $('#da-leggere').prop('checked', true);
                    $('#label-data-iniziale').text("Data iniziale di pubblicazione:");
                    $('#label-data-finale').text("Data finale di pubblicazione:");
                    $('#data-pubblicazione').show();
                    $('.data-pub').prop('disabled', false);
                    $('#albo-cerca-per-select').show();
                    $('#albo-cerca-per-select').prop('disabled', false);
                    $('#albo-cerca-per-select').val("numero").change();
                    $('#tipo-ricerca').show();
                    $('#tipo-ricerca *').contents().prop('disabled', false);
                    break;
            }

            $('.cerca-label').text(valore_select+" da cercare:");

        });

        /* Mostriamo o nascondiamo i campi nelle varie possibili ricerche per albi */

        $('#albo-cerca-per-select').on('change', function(){

            var cerca_per_select = $('#albo-cerca-per-select :selected').text();
            console.log(cerca_per_select);
            switch (cerca_per_select) {
                case 'collana':
                    $('#tipo-ricerca').hide();
                    $('#tipo-ricerca *').contents().prop('disabled', true);
                    $('#label-cerca').hide();
                    $('#label-cerca :input').prop('disabled', true);
                    $('#cerca-collane').show();
                    $('#cerca-collane').prop('disabled', false);
                    break;
                
                case 'tutto':
                    $('#tipo-ricerca').hide();
                    $('#tipo-ricerca *').contents().prop('disabled', true);
                    $('#label-cerca').hide();
                    $('#label-cerca :input').prop('disabled', true);
                    $('#cerca-collane').hide();
                    $('#cerca-collane').prop('disabled', true);
                    break;

                default:
                    $('#cerca-collane').hide();
                    $('#cerca-collane').prop('disabled', true);
                    $('#tipo-ricerca').show();
                    $('#tipo-ricerca *').contents().prop('disabled', false);
                    $('#label-cerca').show();
                    $('#label-cerca :input').prop('disabled', false);
                    break;
            }
        });

        /* Mostriamo o nascondiamo i campi nelle varie possibili ricerche per storie */

        $('#storia-cerca-per-select').on('change', function(){

            var cerca_per_select = $('#storia-cerca-per-select :selected').text();
            console.log(cerca_per_select);
            switch (cerca_per_select) {
                case 'autore':
                    $('#tipo-ricerca').hide();
                    $('#tipo-ricerca *').contents().prop('disabled', true);
                    $('#label-cerca').hide();
                    $('#label-cerca :input').prop('disabled', true);
                    $('#div-select-cerca-storie-autori').show();
                    $('#cerca-storie-autori').prop('disabled', false);
                    $('#cerca-storie-ruoli').prop('disabled', false);
                    break;

                case 'tutto':
                    $('#tipo-ricerca').hide();
                    $('#tipo-ricerca *').contents().prop('disabled', true);
                    $('#label-cerca').hide();
                    $('#label-cerca :input').prop('disabled', true);
                    $('#div-select-cerca-storie-autori').hide();
                    $('#cerca-storie-autori').prop('disabled', true);
                    $('cerca-storie-ruoli').prop('diasbled', true);
                    break;

                default:
                    $('#div-select-cerca-storie-autori').hide();
                    $('#cerca-storie-autori').prop('disabled', true);
                    $('cerca-storie-ruoli').prop('diasbled', true);
                    $('#tipo-ricerca').show();
                    $('#tipo-ricerca *').contents().prop('disabled', false);
                    $('#label-cerca').show();
                    $('#label-cerca :input').prop('disabled', false);
                    break;
            }
        });

        /* Mostriamo o nascondiamo i campi nelle varie possibili ricerche per autori */

        $('#autore-cerca-per-select').on('change', function(){

            var cerca_per_select = $('#autore-cerca-per-select :selected').text();
            console.log(cerca_per_select);
            switch (cerca_per_select) {
                case 'tutto':
                    $('#tipo-ricerca').hide();
                    $('#tipo-ricerca *').contents().prop('disabled', true);
                    $('#label-cerca').hide();
                    $('#label-cerca :input').prop('disabled', true);
                    break;

                default:
                    $('#tipo-ricerca').show();
                    $('#tipo-ricerca *').contents().prop('disabled', false);
                    $('#label-cerca').show();
                    $('#label-cerca :input').prop('disabled', false);
                    break;
            }
        });

        /* Se effettuiamo una ricerca di storie per autore, visualizziamo solo i ruoli ricoperti dall'autore */

        $('#cerca-storie-autori').on('change', function(){
            $.ajax({
            url:"/storia/" + $(this).val() + "/services/get-only-ruoli-json",
            method:"GET",
            data:{},
            dataType: 'json',
            success:function(result)
            {
                console.log(result);
                
                var result_value = result;
                var option = '';
                                
                option += '<option value=""></option>';
                $('#cerca-storie-ruoli').empty();
                for (i in result) {
                    option += '<option value="' + i + '">' + result[i] + '</option>';
                }
                console.log(option);
                $('#cerca-storie-ruoli').append(option);
            },
            error:function()
            {
                console.log(error);
            }
            });
        });

    });
</script>