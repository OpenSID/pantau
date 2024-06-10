<div class="row mt-4">
    <div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3>Pengguna Aktif Aplikasi OpenDesa</h3>
        </div>
        <div class="card-body">
            <canvas id="myChart"></canvas>
        </div>
    </div>
    </div>
</div>
@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        const ctx = document.getElementById('myChart');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {}
        });

        $.ajax({
            url: 'api/web/chart-usage',
            data: {period : $('input[name=periods]').val()},
            type: "GET",
            success: function(data) {
                myChart.data = data;
                myChart.update();
            }
        }, 'json')
    })
</script>
@endpush