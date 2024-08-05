@push('css')
<style>
    .table-custom {
        width: 100%;
    }
    .table-custom td {
        width: 60%;
        border: none;
        padding: 8px 0;
    }
    .table-custom .progress {
        width: 100%;
    }
</style>
@endpush

<img src="{{ asset('assets/img/opensid_logo.png') }}" width="20" alt="Logo">
<span class="text-black">Daftar Desa Baru Install</span>
<div class="row">
    <div class="col-md-6 mt-2">
        <div class="small-box bg-green m-0">
            <div class="inner text-center p-0">
                <p class="text-white m-0">235 <br> Online</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mt-2">
        <div class="small-box bg-red m-0">
            <div class="inner text-center p-0">
                <p class="text-white m-0">235 <br> Offline</p>
            </div>
        </div>
    </div>
</div>
<hr>
<table class="table table-responsive table-custom" style="height:230px">
    <tbody>
        <tr>
            <td>
                <h6 class="m-0">Bau-bau</h6>
                <p class="m-0" style="font-size: 14px;">Sulawesi Selatan, WAJO</p>
                <p class="m-0" style="font-size: 14px;">Install 12.02 | versi 2407.0.0</p>
            </td>
            <td style="text-align-last: right;">
                <span class="badge badge-success">Online</span>
            </td>
        </tr>
        <tr>
            <td>
                <h6 class="m-0">Desa Lain</h6>
                <p class="m-0" style="font-size: 14px;">Sulawesi Selatan, WAJO</p>
                <p class="m-0" style="font-size: 14px;">Install 12.02 | versi 2407.0.0</p>
            </td>
            <td style="text-align-last: right;">
                <span class="badge badge-success">Online</span>
            </td>
        </tr>                
    </tbody>
</table>