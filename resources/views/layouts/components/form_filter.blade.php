@if (array_key_exists('kode_provinsi', $fillters))
<div class="col-sm">
    <div class="form-group">
        <label>Provinsi</label>
        <select class="select2 form-control-sm" id="provinsi" name="provinsi"
            data-placeholder="Semua Provinsi" style="width: 100%;">
            <option value="" selected>Semua Provinsi</option>
        </select>
    </div>
</div>
@endif

@if (array_key_exists('kode_kabupaten', $fillters))
    <div class="col-sm">
        <div class="form-group">
            <label>Kabupaten</label>
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
            <label>Kecamatan</label>
            <select class="select2 form-control-sm" id="kecamatan" name="kecamatan"
                data-placeholder="Semua Kecamatan" style="width: 100%;" disabled>
                <option value="" selected>Semua Kecamatan</option>
            </select>
        </div>
    </div>
@endif

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
