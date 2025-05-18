<div id="collapse-filter" class="collapse">
    <div class="row">
        @if (array_key_exists('kode_provinsi', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label class="text-white">Provinsi</label>
                    <select class="select2 form-control-sm" id="provinsi" name="provinsi" data-placeholder="Semua Provinsi"
                        style="width: 100%;">
                        <option value="" selected>Semua Provinsi</option>
                        @if ($fillters['kode_provinsi'] && is_array($fillters['kode_provinsi']))
                            <option value="{{ $fillters['kode_provinsi']['id'] ?? '' }}" selected>
                                {{ $fillters['kode_provinsi']['name'] ?? '' }}</option>
                        @endif
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('kode_kabupaten', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label class="text-white">Kabupaten</label>
                    <select class="select2 form-control-sm" id="kabupaten" name="kabupaten"
                        data-placeholder="Semua Kabupaten" style="width: 100%;" disabled>
                        <option value="" selected>Semua Kabupaten</option>
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('kode_kabupaten', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label class="text-white">Kecamatan</label>
                    <select class="select2 form-control-sm" id="kecamatan" name="kecamatan"
                        data-placeholder="Semua Kecamatan" style="width: 100%;" disabled>
                        <option value="" selected>Semua Kecamatan</option>
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('online', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label class="text-white">Jenis Server</label>
                    <select class="select2 form-control-sm" id="status" name="online"
                        data-placeholder="Semua Status" style="width: 100%;">
                        <option selected value="0">Semua Status</option>
                        <option value="1">Online</option>
                        <option value="2">Offline</option>
                        <option value="3">Premium Terbaru</option>
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('status', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label>Jenis Server</label>
                    <select class="select2 form-control-sm" id="status" name="online"
                        data-placeholder="Semua Status" style="width: 100%;">
                        <option selected value="0">Semua Status</option>
                        <option value="1">Online</option>
                        <option value="2">Offline</option>
                        <option value="3">Premium Terbaru</option>
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('akses', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label>Akses Terakhir</label>
                    <select class="select2 form-control-sm" id="akses" name="akses"
                        data-placeholder="Semua Status" style="width: 100%;">
                        <option selected value="0">Semua Status</option>
                        <option value="5">Desa aktif hanya offline</option>
                        <option value="4">Sejak tujuh hari yang lalu</option>
                        <option value="2">Sejak dua bulan yang lalu</option>
                        <option value="1">Sebelum dua bulan yang lalu</option>
                        <option value="3">Sebelum empat bulan yang lalu</option>
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('tte', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label>Modul TTE</label>
                    <select class="select2 form-control-sm" id="tte" name="tte"
                        data-placeholder="Semua Status" style="width: 100%;">
                        <option selected value="empty">Semua Status</option>
                        <option value="1">Modul TTE Aktif</option>
                        <option value="0">Modul TTE Tidak Aktif</option>
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('aktif', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label>Status OpenSID</label>
                    <select class="select2 form-control-sm" id="aktif" name="aktif"
                        data-placeholder="Semua Status" style="width: 100%;">
                        <option selected value="empty">Semua Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('akses_mobile', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label>Akses Terakhir</label>
                    <select class="select2 form-control-sm" id="akses_mobile" name="akses_mobile"
                        data-placeholder="Semua Status" style="width: 100%;">
                        <option selected value="0">Semua Status</option>
                        <option value="1">Sejak tujuh hari yang lalu</option>
                        <option value="2">Sejak dua bulan yang lalu</option>
                        <option value="3">Sebelum dua bulan yang lalu</option>
                        <option value="4">Sebelum empat bulan yang lalu</option>
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('akses_opendk', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label>Akses Terakhir</label>
                    <select class="select2 form-control-sm" id="akses_opendk" name="akses_opendk"
                        data-placeholder="Semua Status" style="width: 100%;">
                        <option selected value="0">Semua Status</option>
                        <option value="1">Sejak tujuh hari yang lalu</option>
                        <option value="2">Sejak dua bulan yang lalu</option>
                        <option value="3">Sebelum dua bulan yang lalu</option>
                        <option value="4">Sebelum empat bulan yang lalu</option>
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('versi_opendk', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label>Status OpenSID</label>
                    <select class="select2 form-control-sm" id="versi_opendk" name="versi_opendk"
                        data-placeholder="Semua Versi" style="width: 100%;">
                        <option selected value="0">Semua Versi</option>
                    </select>
                </div>
            </div>
        @endif

        @if (array_key_exists('suku', $fillters))
            <div class="col-sm">
                <div class="form-group">
                    <label class="text-white">Suku</label>
                    <select class="select2 form-control-sm" id="suku" name="suku"
                        data-placeholder="Semua Suku" style="width: 100%;">
                        <option value="" selected>Semua Suku</option>
                        @if ($fillters['suku'] && is_array($fillters['suku']))
                            <option value="{{ $fillters['suku']['id'] ?? '' }}" selected>
                                {{ $fillters['suku']['name'] ?? '' }}</option>
                        @endif
                    </select>
                </div>
            </div>
        @endif

    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <div class="input-group">
                    <div class="btn-group btn-group-sm btn-block">
                        <button type="button" id="reset" class="btn btn-secondary"><span
                                class="fas fa-ban"></span></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <div class="input-group">
                    <div class="btn-group btn-group-sm btn-block">
                        <button type="button" id="filter" class="btn btn-primary"><span
                                class="fas fa-search"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr class="mt-0">
</div>
