@extends('layouts.main')
@section('content')
<form enctype="multipart/form-data" method="post" action="{{ route('storia.store_autore', $id_storia) }}">
    @csrf
    @include('rel_storia_autore_ruolo.form', [])
    <button type="submit" class="btn btn-primary">Salva</button>
</form>
<script>
    $(document).ready(function(){
        $('[name=autore]').on('change', function(){
            $.ajax({
            url:"/storia/{{ $id_storia }}/" + $(this).val() + "/services/get-ruoli-json",
            method:"GET",
            data:{},
            dataType: 'json',
            success:function(result)
            {
                console.log(result);
                //var $el =$("#ruolo_select");
                var result_value = result;
                $("#ruolo-select").val(result_value).trigger('change');
                //$el.val(result);
            },
            error:function()
            {
                console.log(error);
            }
            });
        });
        @if (!empty($id_autore_default)) 
            $('[name=autore]').val({{ $id_autore_default }});
            $('[name=autore]').trigger('change');
        @endif
    });
</script>
@endsection