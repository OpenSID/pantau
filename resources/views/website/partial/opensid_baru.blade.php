<div class="row">
    <!-- <div class="col-md">
        <div class="card bg-white">
            <div class="card-header bg-blue text-white text-center">
                <h4>OpenKab</h4>
            </div>
            <div class="card-body" id="openkab-baru">
                <div class="pl-4">#. Nama Wilayah</div>
                <hr>
                <ol>
                    
                </ol>
            </div>
        </div>
    </div> -->
    <div class="col-md">
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
                </div>
                <a href="{{ url('web/opendk') }}" id="view-more-button-opendk" class="btn btn-outline-dark btn-block" style="display: none;">Lihat Selengkapnya...</a>
            </div>
        </div>
    </div>
    <div class="col-md">
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
                </div>
            </div>
        </div>
    </div>
    <div class="col-md">
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
                </div>
            </div>
        </div>
    </div>
    <div class="col-md">
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
                </div>
            </div>
        </div>
    </div>    
</div>
<style>
    #table-opendk-baru thead, #table-opensid-baru thead , #table-keloladesa-baru thead, #table-layanandesa-baru thead {
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
        });
        $('#table-keloladesa-baru thead').hide();
    });
</script>
@endpush
