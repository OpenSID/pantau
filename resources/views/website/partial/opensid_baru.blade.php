<div class="container-fluid" id="menu-center">
        <div class="row justify-content-center">
    <div class="col-md-2">
        <div class="card bg-white">
            <div class="card-header bg-blue text-white text-center">
                <h4>OpenKab</h4>
            </div>
            <div class="card-body" id="openkab-baru">
                <div class="pl-2">#<span class="pl-4">Nama Wilayah</span></div>
                <div class="table-responsive">
                    <table class="table" id="table-openkab-baru">
                        <tbody></tbody>
                    </table>
                </div><br>
                <a href="{{ url('web/openkab-data') }}" id="view-more-button-openkab" class="btn btn-outline-dark btn-block" style="display: none;">lihat selengkapnya</a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-white">
            <div class="card-header bg-blue text-white text-center">
                <h4>OpenDK</h4>
            </div>
            <div class="card-body" id="opendk-baru">
                <div class="pl-2">#<span class="pl-4">Nama Wilayah</span></div>
                <div class="table-responsive">
                    <table class="table" id="table-opendk-baru">
                        <tbody></tbody>
                    </table>
                </div><br>
                <a href="{{ url('web/opendk/detail') }}" id="view-more-button-opendk" class="btn btn-outline-dark btn-block" style="display: none;">lihat selengkapnya</a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-white">
            <div class="card-header bg-blue text-white text-center">
                <h4>OpenSID</h4>
            </div>
            <div class="card-body" id="opensid-baru">
                <div class="pl-2">#<span class="pl-4">Nama Wilayah</span></div>
                <div class="table-responsive">
                    <table class="table" id="table-opensid-baru">
                        <tbody></tbody>
                    </table>
                </div><br>
                <a href="{{ url('web/opensid-data') }}" id="view-more-button-opensid" class="btn btn-outline-dark btn-block" style="display: none;">lihat selengkapnya</a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-white">
            <div class="card-header bg-blue text-white text-center">
                <h4>Layanan Desa</h4>
            </div>
            <div class="card-body" id="layanandesa-baru">
                <div class="pl-2">#<span class="pl-4">Nama Wilayah</span></div>
                <div class="table-responsive">
                    <table class="table" id="table-layanandesa-baru">
                        <tbody></tbody>
                    </table>
                </div><br>
                <a href="{{ url('web/layanandesa/detail') }}" id="view-more-button-layanandesa" class="btn btn-outline-dark btn-block" style="display: none;">lihat selengkapnya</a>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card bg-white">
            <div class="card-header bg-blue text-white text-center">
                <h4>Kelola Desa</h4>
            </div>
            <div class="card-body" id="keloladesa-baru">
                <div class="pl-2">#<span class="pl-4">Nama Wilayah</span></div>
                <div class="table-responsive">
                    <table class="table" id="table-keloladesa-baru">
                        <tbody></tbody>
                    </table>
                </div><br>
                <a href="{{ url('web/keloladesa/detail') }}" id="view-more-button-keloladesa" class="btn btn-outline-dark btn-block" style="display: none;">lihat selengkapnya</a>
            </div>
        </div>
    </div>   
    <div class="col-md-2">
        <div class="card bg-white">
            <div class="card-header bg-blue text-white text-center">
                <h4>PBB</h4>
            </div>
            <div class="card-body" id="pbb-baru">
                <div class="pl-2">#<span class="pl-4">Nama Wilayah</span></div>
                <div class="table-responsive">
                    <table class="table" id="table-pbb-baru">
                        <tbody></tbody>
                    </table>
                </div><br>
                <a href="{{ url('web/pbb-data') }}" id="view-more-button-pbb" class="btn btn-outline-dark btn-block" style="display: none;">lihat selengkapnya</a>
            </div>
        </div>
    </div>    
</div>
<style>
    #table-opendk-baru thead, #table-opensid-baru thead , #table-keloladesa-baru thead, #table-layanandesa-baru thead, #table-pbb-baru thead {
        display: none;
    }
</style>
@push('js')
<script>
    $(document).ready(function() {
        $('#table-opendk-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: `{{ route('datatables:opendk-baru') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'region'
                },
                {
                    data: 'tanggal'
                },
            ],
            dom: 't<"bottom">', 
            drawCallback: function(settings) {
                var api = this.api();
                var data = api.rows({ page: 'current' }).data().length;

                if (data > 0) {
                    $('#view-more-button-opendk').show();
                } else {
                    $('#view-more-button-opendk').hide();
                }
            }
        });
        $('#table-opendk-baru thead').hide();

        $('#table-openkab-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: `{{ route('datatables:openkab-baru') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'region'
                },
                {
                    data: 'tanggal'
                },
            ],
            dom: 't<"bottom">', 
            drawCallback: function(settings) {
                var api = this.api();
                var data = api.rows({ page: 'current' }).data().length;

                if (data > 0) {
                    $('#view-more-button-openkab').show();
                } else {
                    $('#view-more-button-openkab').hide();
                }
            }
        });
        $('#table-openkab-baru thead').hide();
        
        $('#table-opensid-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: `{{ route('datatables:opensid-baru') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'region'
                },
                {
                    data: 'tanggal'
                },
            ],
            dom: 't<"bottom">', 
            drawCallback: function(settings) {
                var api = this.api();
                var data = api.rows({ page: 'current' }).data().length;
                if (data > 0) {
                    $('#view-more-button-opensid').show();
                } else {
                    $('#view-more-button-opensid').hide();
                }
            }
        });
        $('#table-opensid-baru thead').hide();

        
        $('#table-layanandesa-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: `{{ route('datatables:layanandesa-baru') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'region'
                },
                {
                    data: 'tanggal'
                },
            ],
            dom: 't<"bottom">', 
            drawCallback: function(settings) {
                var api = this.api();
                var data = api.rows({ page: 'current' }).data().length;
                if (data > 0) {
                    $('#view-more-button-layanandesa').show();
                } else {
                    $('#view-more-button-layanandesa').hide();
                }
            }
        });
        $('#table-layanandesa-baru thead').hide();

        
        $('#table-keloladesa-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: `{{ route('datatables:keloladesa-baru') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'region'
                },
                {
                    data: 'tanggal'
                },
            ],
            dom: 't<"bottom">', 
            drawCallback: function(settings) {
                var api = this.api();
                var data = api.rows({ page: 'current' }).data().length;
                if (data > 0) {
                    $('#view-more-button-keloladesa').show();
                } else {
                    $('#view-more-button-keloladesa').hide();
                }
            }
        });
        $('#table-keloladesa-baru thead').hide();

        $('#table-pbb-baru').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ordering: false,
            ajax: {
                url: `{{ route('datatables:pbb-baru') }}`,
                method: 'get',
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                },
                {
                    data: 'region'
                },
                {
                    data: 'tanggal'
                },
            ],
            dom: 't<"bottom">', 
            drawCallback: function(settings) {
                var api = this.api();
                var data = api.rows({ page: 'current' }).data().length;
                if (data > 0) {
                    $('#view-more-button-pbb').show();
                } else {
                    $('#view-more-button-pbb').hide();
                }
            }
        });
        $('#table-pbb-baru thead').hide();
    });
</script>
@endpush
