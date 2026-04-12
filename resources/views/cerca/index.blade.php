@extends('layouts.main')
@section('content')
    <div class="col-4">
        <form action="{{ route('cerca.search') }}">
            @include('cerca.form', [])
                <button type="submit" class="btn btn-primary">Cerca</button>
        </form>
    </div>
@endsection