<div class="form-group">
    <label for="collana">Collana:</label>
    <input type="text" class="form-control" name="nome" value="{{ !empty(old('nome')) ? old('nome') : (!empty($collane->nome)? $collane->nome : '') }}"/>
</div>  

