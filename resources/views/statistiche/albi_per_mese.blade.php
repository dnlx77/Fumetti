@extends('layouts.main')
@section('content')

<div class="form-group">
    <label for="anno">Anno:</label>
    <select id="anno-select" name="anno">
        @for ($i=$primo_anno;$i<=$ultimo_anno;$i++ )
            <option value="{{ $i }}">{{ $i }}</option>
        @endfor
    </select>
</div>

<div>
    <span>Albi totali pubblicati nell'anno: {{ $num_albi_anno }} </span>
</div>

<div class="table-container">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                @foreach ($num_albi_per_mese AS $mese => $numero_albi)
                    <th>{{ $mese }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($num_albi_per_mese AS $mese => $numero_albi)
                    <td> <a href="{{ route('statistiche.get_albi_mese_anno', array( date_parse($mese)['month'], last( request()->segments() ) ) ) }}">{{ $numero_albi }}</a></td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

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
            </tr>
        @endforeach
        </tbody>
    </table>

        {{ $albi->links() }}

</div>

<script>
    $(document).ready(function() {
        
        var pathname = window.location.pathname;
        var anno = pathname.substr(pathname.length-4,4);
       
        $('#anno-select').val(anno);
        
        $('#anno-select').on('change', function() {
            var selVal=$(this).val();
            window.location.href = "{{ route('statistiche.albi_mese', '') }}" + "/" + selVal;
            
            /* La riga sotto è equivalente a quella sopra ma quella soptra è più legibile */
            //window.location.href=selVal;
        });
    });
</script>
@endsection