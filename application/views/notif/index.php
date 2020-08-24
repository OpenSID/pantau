<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Notifikasi
			<small>Daftar Notifkasi ke OpenSID</small>
		</h1>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<!--------------------------
			| Your Page Content Here |
			-------------------------->
			<form id="mainform" name="mainform" action="<?=site_url("notifikasi/index")?>" method="post">
			<div class="pull-left">
				<select class="form-control input-sm " name="jenis" onchange="$('#mainform').submit();">
					<option value="semua" <?php $selected_filter=='semua' and print('selected') ?>>Semua</option>
					<?php foreach($combo_jenis as $combo): ?>
					<option value="<?= $combo['jenis'] ?>" <?php $combo['jenis']==$selected_filter and print('selected') ?>><?= ucwords($combo['jenis']) ?></option>
					<?php endforeach ?>
				</select>
			</div>
			<div class="col-sm-3">
				<div class="box-tools">
					<div class="input-group input-group-sm pull-right">
						<input name="cari" id="cari" class="form-control" placeholder="Cari..." type="text" value="<?=html_escape($keyword)?>" onkeypress="if (event.keyCode == 13):$('#mainform').submit();endif">
						<div class="input-group-btn">
							<button type="submit" class="btn btn-default" onclick="$('#mainform').submit();"><i class="fa fa-search"></i></button>
						</div>
					</div>
				</div>
			</div>

			<div class="pull-right">
				<a href="<?= site_url('notifikasi/add'); ?>" class="btn btn-success">Add</a> 
			</div>
			</form>
			<table class="table table-striped table-bordered">
				<tr>
					<th>ID</th>
					<th>Aktif</th>
					<th>Frekuensi</th>
					<th>Kode</th>
					<th>Judul</th>
					<th>Jenis</th>
					<th>Server</th>
					<th>Isi</th>
					<th>Actions</th>
				</tr>
				<?php foreach($notifikasi as $n): ?>
				<tr>
					<td><?= $n['id']; ?></td>
					<td><?= $n['aktif']?'Ya' : 'Tidak'  ?></td>
					<td><?= $n['frekuensi']; ?></td>
					<td><?= $n['kode']; ?></td>
					<td><?= $n['judul']; ?></td>
					<td><?= $n['jenis']; ?></td>
					<td><?= $n['server']; ?></td>
					<td><?= $n['isi']; ?></td>
					<td>
						<a href="<?= site_url('notifikasi/edit/'.$n['id']); ?>" class="btn btn-info btn-xs">Edit</a> 
						<a href="<?= site_url('notifikasi/remove/'.$n['id']); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Hapus notifikasi?');">Delete</a>
					</td>
				</tr>
				<?php endforeach ?>
			</table>
			<div class="pull-right">
				<?= $this->pagination->create_links(); ?>    
			</div>

	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->