<style type="text/css">
	.teks-form {
		padding-top: 7px;
		padding-left: 0px;
	}
	textarea {
		resize: vertical;
	}
</style>
<div class="content-wrapper">
	<section class="content-header">
		<h1>Notifikasi <small>Tambah Notifikasi</small></h1>
		<ol class="breadcrumb">
			<li><a href="<?= site_url('')?>"><i class="fa fa-home"></i> Dashboard</a></li>
			<li><a href="<?= site_url('notifikasi')?>"> Daftar Notifikasi</a></li>
			<li class="active">Tambah Notifikasi</li>
		</ol>
	</section>
	<section class="content container-fluid" id="maincontent">
		<div class="box box-info">
			<div class="box-header with-border">
				<a href="<?= site_url('notifikasi')?>" class="btn btn-social btn-flat btn-info btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block"><i class="fa fa-arrow-circle-left"></i> Kembali Ke Daftar Notifikasi</a>
			</div>
			<form id="validasi" action="<?= site_url("notifikasi/form/{$notifikasi['id']}"); ?>" method="POST" class="form-horizontal">
				<input type="hidden" name='id' value="<?= $notifikasi['id']?>">
				<div class="box-body">
					<div class="form-group">
						<label for="aktif" class="col-md-2 control-label"><span class="text-danger">*</span>Aktif</label>
						<div class="col-md-3">
							<select name="aktif" class="form-control">
								<option value="">Pilih Status Aktif</option>
								<?php foreach ($status_aktif as $key => $nama): ?>
									<option value="<?= $key ?>" <?= selected($key, $this->input->post('aktif') ?: $notifikasi['aktif']) ?>><?= $nama ?></option>
								<?php endforeach; ?>
							</select>
							<span class="text-danger"><?= form_error('aktif');?></span>
						</div>
					</div>
					<div class="form-group">
						<label for="frekuensi" class="col-md-2 control-label"><span class="text-danger">*</span>Frekuensi</label>
						<div class="col-md-3">
							<input type="text" name="frekuensi" class="form-control" placeholder="Contoh: setiap 30, 60, 90 hari" value="<?= $this->input->post('frekuensi') ?: $notifikasi['frekuensi']?>"/>
							<span class="text-danger"><?= form_error('frekuensi');?></span>
						</div>
						<span class="col-md-1 teks-form"> hari </span>
					</div>
					<div class="form-group">
						<label for="kode" class="col-md-2 control-label"><span class="text-danger">*</span>Kode</label>
						<div class="col-md-3">
							<input type="text" name="kode" value="<?= $this->input->post('kode') ?: $notifikasi['kode'] ?>" class="form-control" placeholder="Kode unik untuk notifikasi" />
							<span class="text-danger"><?= form_error('kode');?></span>
						</div>
					</div>
					<div class="form-group">
						<label for="judul" class="col-md-2 control-label"><span class="text-danger">*</span>Judul</label>
						<div class="col-md-8">
							<input type="text" name="judul" value="<?= $this->input->post('judul') ?: $notifikasi['judul'] ?>" class="form-control" placeholder="Judul yang ditampilkan di popup pengumuman"/>
							<span class="text-danger"><?= form_error('judul');?></span>
						</div>
					</div>
					<div class="form-group">
						<label for="jenis" class="col-md-2 control-label"><span class="text-danger">*</span>Jenis</label>
						<div class="col-md-3">
							<select name="jenis" class="form-control">
								<option value="">Pilih Jenis Notifikasi</option>
								<?php foreach ($jenis_notif as $nama): ?>
									<option value="<?= $nama ?>" <?= selected($nama, $this->input->post('jenis') ?: $notifikasi['jenis']) ?>><?= ucwords($nama) ?></option>
								<?php endforeach; ?>
							</select>
							<span class="text-danger"><?= form_error('jenis');?></span>
						</div>
					</div>
					<div class="form-group">
						<label for="server" class="col-md-2 control-label"><span class="text-danger">*</span>Server</label>
						<div class="col-md-3">
							<select name="server" class="form-control">
								<option value="">Pilih Server Notifikasi</option>
								<?php foreach ($server_notif as $nama): ?>
									<option value="<?= $nama ?>" <?= selected($nama, $this->input->post('server') ?: $notifikasi['server']) ?>><?= $nama ?></option>
								<?php endforeach; ?>
							</select>
							<span class="text-danger"><?= form_error('server');?></span>
						</div>
					</div>
					<div class="form-group">
						<label for="isi" class="col-md-2 control-label"><span class="text-danger">*</span>Isi</label>
						<div class="col-md-8">
							<textarea name="isi" class="form-control" placeholder="Teks pengumuman"><?= $this->input->post('isi') ?: $notifikasi['isi'] ?></textarea>
							<span class="text-danger"><?= form_error('isi');?></span>
						</div>
					</div>
					<div class="form-group">
						<label for="aksi_ya" class="col-md-2 control-label">Aksi Tombol Ya</label>
						<div class="col-md-8">
							<input name="aksi_ya" class="form-control" placeholder="Aksi OpenSID apabila peringatan disetujui" value="<?= $this->input->post('aksi_ya') ?: $notifikasi['aksi_ya'] ?>">
							<span class="text-danger"><?= form_error('aksi_ya');?></span>
						</div>
					</div>
					<div class="form-group">
						<label for="aksi_tidak" class="col-md-2 control-label">Aksi Tombol Tidak</label>
						<div class="col-md-8">
							<input name="aksi_tidak" class="form-control" placeholder="Aksi OpenSID apabila peringatan tidak disetujui" value="<?= $this->input->post('aksi_tidak') ?: $notifikasi['aksi_tidak'] ?>">
							<span class="text-danger"><?= form_error('aksi_tidak');?></span>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="reset" class="btn btn-social btn-flat btn-danger btn-sm"><i class="fa fa-times"></i> Batal</button>
					<button type="submit" class="btn btn-social btn-flat btn-info btn-sm pull-right"><i class="fa fa-check"></i> Simpan</button>
				</div>
			</form>
		</div>
	</section>
</div>
