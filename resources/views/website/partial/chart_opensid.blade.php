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
<script src="{{ asset('vendor/chartjs/js/chart.min.js') }}"></script>
<script src="{{ asset('vendor/chartjs/plugin/chartjs-plugin-annotation.min.js') }}"></script>
<script>
    const ctx = document.getElementById('myChart');
    const annotation1 = {
        type: 'line',
        borderColor: 'gray',
        borderWidth: 1,
        label: {
            display: true,
            backgroundColor: 'transparent',
            borderColor: 'black',
            color: "black",
            position: 'start',
            borderRadius: 10,
            borderWidth: 0,
            content: (ctx) => `{{date('Y')}}`,
            rotation: false
        },
        scaleID: 'x',
        value: `Jan-{{date('y')}}`
    };
    const annotation2 = {
        type: 'line',
        borderColor: 'gray',
        borderWidth: 1,
        label: {
            display: true,
            backgroundColor: 'transparent',
            borderColor: 'black',
            color: "black",
            position: 'start',
            borderRadius: 10,
            borderWidth: 0,
            content: (ctx) => `{{date('Y') - 1}}`,
            rotation: false
        },
        scaleID: 'x',
        value: `Jan-{{date('y') - 1}}`
    };

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
                        annotation1,
                        annotation2
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
