<div class="form-group">
    <label for="autore">Autore:</label>
    <select id="autore-select" name="autore">
        <option value=""></option>
        @foreach ($lista_autori as $current_autore)
            <option value="{{ $current_autore->id }}">{{ !$current_autore->pseudonimo ? $current_autore->nome.' '.$current_autore->cognome : $current_autore->nome.' \''.$current_autore->pseudonimo.'\' '.$current_autore->cognome }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <label for="ruolo">Ruolo:</label>
    <select id="ruolo-select" name="ruolo[]" multiple="multiple">
        @foreach ($lista_ruoli as $current_ruolo)
            <option value="{{ $current_ruolo->id }}">{{ $current_ruolo->descrizione }}</option>
        @endforeach
    </select>
</div>
<script>
    $(document).ready(function(){
        $('#autore-select').select2();
        var autore_select_value = "{{ !empty(old('autore')) ? old('autore') : (!empty($current_autore->id) ? $current_autore->id : '') }}";
        $('#autore-select').val(autore_select_value).trigger('change');
        $('#ruolo-select').select2();
    });
</script>