<div class="form-group">
    <label for="storia">Storia:</label>
    <select id="storia-select" name="storia">
        <option value=""></option>
        @foreach ($lista_storie as $current_storia)
            <option value="{{ $current_storia->id }}">{{ $current_storia->nome }}</option>
        @endforeach
    </select>
</div>
<script>
    $(document).ready(function(){
        $('#storia-select').select2();
        var storia_select_value = "{{ !empty(old('storia')) ? old('storia') : (!empty($id_storia_default) ? $id_storia_default : '') }}";
        $('#storia-select').val(storia_select_value).trigger('change');
    });
</script>