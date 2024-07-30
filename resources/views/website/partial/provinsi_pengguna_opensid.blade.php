@push('css')
<style>
    .table-custom {
        width: 100%;
    }
    .table-custom td {
        width: 10%;
        border: none;
        padding: 0;
    }
    .table-custom .progress {
        width: 100%;
    }
</style>
@endpush

<div class="container mt-3">
    <div class="card mt-3">
        <div class="card-body">
            <table class="table table-responsive table-custom">
                <tbody>
                    <tr>
                        <td>
                            Nusa Tenggara Barat
                        </td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">100</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Provinsi Kedua
                        </td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100">75</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Provinsi ketiga
                        </td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Provinsi Keempat
                        </td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25</div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>