<div class="modal fade" id="modalSelengkapnya" tabindex="-1" role="dialog" aria-labelledby="modalSelengkapnyaLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalSelengkapnyaLabel">Pengguna OpenSID Seluruh Wilayah</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="row mb-2">
          <div class="col-md-6">
            <button class="btn btn-secondary btn-sm" data-toggle="collapse" href="#collapse-filter-form" aria-expanded="false" aria-controls="collapse-filter-form">
              <i class="fa fa-filter"></i> Filter
            </button>
            {{-- <a class="btn btn-sm btn-success" id="btn-export" role="button" data-href="{{ url('datatables/semua-desa') }}"><i class="fas fa-file-excel"></i> Excel<a> --}}
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
              <form name="filter-form" method="GET">
                  <div class="row">
                      <div class="col-md-12">
                          @include('layouts.components.form_filter_modal')
                      </div>
                  </div>
              </form>
          </div>
        </div>

        @include('website.partial.tabel_opensid_detail')
      </div>
    </div>
  </div>
</div>

@include('layouts.components.select2_wilayah')
