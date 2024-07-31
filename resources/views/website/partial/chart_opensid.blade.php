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
        data: {},
        options: {
            animation: {
                duration: 0,
                onComplete: function () {
                    let ctxz = this.ctx;
                    ctxz.font = Chart.helpers.fontString(Chart.defaults.font.size, Chart.defaults.font.style, Chart.defaults.font.family);
                    ctxz.textAlign = 'center';
                    ctxz.textBaseline = 'bottom';
                    chartinst = this;
                    this.data.datasets.forEach(function (dataset, i) {
                        if (chartinst.isDatasetVisible(i)) {
                            var meta = chartinst.getDatasetMeta(i);
                            meta.data.forEach(function (bar, index) {
                                var data = dataset.data[index];
                                ctxz.fillText(data, bar.x, bar.y - 10);
                            });
                        }
                    });
                }
            },
            plugins: {
                annotation: {
                    annotations: {
                    
                    }
                },                    
                tooltip: {
                    enabled: false,
                },
            },




        }


    });
</script>
@endpush