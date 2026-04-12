@extends('layouts.main')
@section('content')

<div class="table-container">
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                @foreach ($num_albi_per_anno AS $anno => $numero_albi)
                    <th>{{ $anno }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach ($num_albi_per_anno AS $anno => $numero_albi)
                    <td> <a href="{{ route('statistiche.albi_mese', $anno)}}">{{ $numero_albi }}</a></td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

<div>
    <canvas id="myChart"></canvas>
</div>
  
<script>
    $(document).ready(function() {
        

        $.ajax({
            url:"/statistiche/services/get-anni",
            method:"GET",
            data:{},
            dataType: 'json',
            success:function(result)
            {
                console.log(Object.keys(result));
                console.log(Object.values(result));
                const ctx = document.getElementById('myChart');
                new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(result),
                    datasets: [{
                    label: '# albi per anno',
                    data: Object.values(result),
                    backgroundColor: '#78faff',
                    borderWidth: 0,
                    }]
                },
                options: {
                    scales: {
                    y: {
                        beginAtZero: true
                    }
                    }
                }
                });
            },
            error:function()
            {
                console.log(error);
            }
            }); 
    });
</script>
  
@endsection