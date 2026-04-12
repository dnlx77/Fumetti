<div class="form-group">
    <label for="num_pagine">Numero di pagine:</label>
    <input type="number" class="form-control" name="num_pagine" value="{{ !empty(old('num_pagine')) ? old('num_pagine') : (!empty($albo->num_pagine) ? $albo->num_pagine : '') }}"/>
</div>
<div class="form-group">
    <label for="barcode">Barcode:</label>
    <input type="text" class="form-control" name="barcode" value="{{ !empty(old('barcode')) ? old('barcode') : (!empty($albo->barcode) ? $albo->barcode : '') }}"/>
</div>
<div class="form-group">
    <label for="prezzo">Prezzo in euro:</label>
    <input type="number" class="form-control" name="prezzo" step="any" value="{{ !empty(old('prezzo')) ? old('prezzo') : (!empty($albo->prezzo) ? $albo->prezzo : '') }}"/>
</div>
<div class="form-group">
    <label for="prezzo_lire">Prezzo in lire:</label>
    <input type="number" class="form-control" name="prezzo_lire" step="any" value="{{ !empty(old('prezzo_lire')) ? old('prezzo_lire') : (!empty($albo->prezzo_lire) ? $albo->prezzo_lire : '') }}"/>
</div>
<div class="form-group">
    <label for="numero">Numero albo:</label>
    <input type="number" class="form-control" name="numero" value="{{ !empty(old('numero')) ? old('numero') : (!empty($albo->numero) ? $albo->numero : '') }}"/>
</div>
<div class="form-group">
    <label for="titolo">Titolo albo:</label>
    <input type="text" class="form-control" name="titolo" value="{{ !empty(old('titolo')) ? old('titolo') : (!empty($albo->titolo) ? $albo->titolo : '') }}"/>
</div>
<div class="form-group">
    <label for="data_pubblicazione">Data pubblicazione:</label>
    <input type="text" class="form-control" name="data_pubblicazione" value="{{ !empty(old('data_pubblicazione')) ? old('data_pubblicazione') : (!empty($albo->data_pubblicazione) ? date('d-m-Y', strtotime($albo->data_pubblicazione)) : '') }}"/>
</div>
<div class="form-group">
    <label for="copertina">Copertina albo:</label>
    <input type="file" class="form-control" name="copertina"/>
</div>
<div class="form-group">
    <label for="autori_copertina">Autori copertina:</label>
    <select id="autori_copertina-select" name="autori_copertina[]" multiple="multiple">
        <option value=""></option>
        @foreach ($lista_autori as $current_autore)
            @if ($current_autore->pseudonimo)
                <option value="{{ $current_autore->id }}">{{ $current_autore->nome }} '{{ $current_autore->pseudonimo }}' {{ $current_autore->cognome }}</option>
            @else
                <option value="{{ $current_autore->id }}">{{ $current_autore->nome }} {{ $current_autore->cognome }}</option>
            @endif
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="collana">Collana albo:</label>
    <select id="collana-select" name="collana">
        <option value=""></option>
        @foreach ($lista_collane as $current_collana)
            <option value="{{ $current_collana->id }}">{{ $current_collana->nome }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="editore">Editore albo:</label>
    <select id="editore-select" name="editore">
        <option value=""></option>
        @foreach ($lista_editori as $current_editore)
            <option value="{{ $current_editore->id }}">{{ $current_editore->nome }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="storie">Storie albo:</label>
    <select id="storie-select" name="storie[]" multiple="multiple">
        <option value=""></option>
        @foreach ($lista_storie as $current_storia)
            <option value="{{ $current_storia->id }}">{{ $current_storia->nome }}</option>
        @endforeach
    </select>
</div>
<script>
    $(document).ready(function(){
        $('[name=data_pubblicazione]').datepicker({
            format: 'dd-mm-yyyy',
            todayHighlight: true,
            weekStart: 1
        });
        $('#collana-select').select2();
            var collana_select_default = "{{ !empty(old('collana')) ? old('collana') : (!empty($albo->collana_id) ? $albo->collana_id : '') }}";
            $('#collana-select').val(collana_select_default).trigger('change');

        $('#editore-select').select2();
            var editore_select_default = "{{ !empty(old('editore')) ? old('editore') : (!empty($albo->editore_id) ? $albo->editore_id : '') }}";
            $('#editore-select').val(editore_select_default).trigger('change');

        $('#storie-select').select2();
        var storie_select_default = [{{ !empty(old('storie')) ? implode(',', old('storie')) : (!empty($storie_array) ? implode(',', $storie_array) : '') }}];
        $('#storie-select').val(storie_select_default).trigger('change');

        $('#autori_copertina-select').select2();
        var autoricopertina_select_default = [{{ !empty(old('autori_copertina')) ? implode(',', old('autori_copertina')) : (!empty($autoricopertina_array) ? implode(',', $autoricopertina_array) : '') }}];
        $('#autori_copertina-select').val(autoricopertina_select_default).trigger('change');
    });
</script>
