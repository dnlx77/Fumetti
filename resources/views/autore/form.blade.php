<div class="form-group">
    <label for="cognome">Cognome:</label>
    <input type="text" class="form-control" name="cognome" value="{{ !empty(old('cognome')) ? old('cognome') : (!empty($autore->cognome) ? $autore->cognome : '') }}"/>
</div>
<div class="form-group">
    <label for="nome">Nome:</label>
    <input type="text" class="form-control" name="nome" value="{{ !empty(old('nome')) ? old('nome') : (!empty($autore->nome) ? $autore->nome : '') }}"/>
    <div class="form-group">
        <label for="pseudonimo">Pseudonimo:</label>
        <input type="text" class="form-control" name="pseudonimo" value="{{ !empty(old('pseudonimo')) ? old('pseudonimo') : (!empty($autore->pseudonimo) ? $autore->pseudonimo : '') }}"/>
</div>