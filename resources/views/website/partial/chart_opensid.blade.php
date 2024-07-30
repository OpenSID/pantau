<div class="row mt-3">
    <div class="col-md-12">
    <div class="card">
        <div class="card-body">
            <canvas id="myChart"></canvas>
        </div>
    </div>
    </div>
</div>
@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>    
    const ctx = document.getElementById('myChart');
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {}
    });    
</script>
@endpush