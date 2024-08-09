@push('css')
    <style>
        .chart-container {
            width: 100%;
            height: 90vh; /* Atur tinggi sesuai kebutuhan */
        }

        #myChart {
            width: 100%; /* Mengisi kontainer */
            height: 100%; /* Mengisi kontainer */
        }
    </style>
@endpush
<div class="row mt-4">
    <div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3>Pengguna Aktif Aplikasi OpenDesa</h3>
        </div>
        <div class="card-body chart-container">
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
        data: {},
        options: {
            layout: {
                padding: 20
            },
            elements: {
                point: {
                    radius: 5
                }
            },
            scales: {
                x: {
                    ticks: {
                        padding: 10
                    }
                },
                y: {
                    ticks: {
                        padding: 10
                    }
                }
            }
        },
        responsive: true,
        maintainAspectRatio: false
    });    
</script>
@endpush