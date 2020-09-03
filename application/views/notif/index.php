<style type="text/css">
	.table-responsive {
    margin-top: 10px;
	}
</style>

<div class="content-wrapper">
	<section class="content-header">
		<h1>Notifikasi<small>Daftar Notifkasi ke OpenSID</small></h1>
		<ol class="breadcrumb">
			<li><a href="<?= site_url(); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
			<li class="active">Data Suplemen</li>
		</ol>
	</section>
	<section class="content container-fluid">
		<div class="box box-info">
			<div class="box-header with-border">
				<a href="<?= site_url('notifikasi/form'); ?>" class="btn btn-social btn-flat bg-olive btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Tambah Notifikasi Baru"><i class="fa fa-plus"></i> Tambah Notifikasi</a>
			</div>
			<div class="box-body">
				<div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
					<form id="mainform" name="mainform" action="<?=site_url("notifikasi/index")?>" method="post">
						<select class="form-control input-sm " name="jenis" onchange="$('#mainform').submit();">
							<option value="">Pilih Jenis</option>
							<?php foreach ($combo_jenis as $combo): ?>
								<option value="<?= $combo['jenis'] ?>" <?php $combo['jenis'] == $selected_filter and print('selected') ?>><?= ucwords($combo['jenis']) ?></option>
							<?php endforeach ?>
						</select>
					</form>
					<div class="table-responsive">
						<table id="notifikasi" class="table table-bordered table-striped dataTable table-hover tabel-daftar">
							<thead class="bg-gray color-palette">
								<tr>
									<th>No</th>
									<th>Aksi</th>
									<th>Frekuensi</th>
									<th>Kode</th>
									<th>Judul</th>
									<th>Jenis</th>
									<th>Server</th>
									<th>Isi</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
							<tfoot>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<?php $this->load->view('global/confirm_delete');?>

<?php $adminlte = 'vendor/almasaeed2010/adminlte/'; ?>
<script src="<?= base_url($adminlte.'bower_components/jquery/dist/jquery.min.js')?>"></script>

<script type="text/javascript">
	$(document).ready(function() {
		var url = "<?= site_url('notifikasi/ajax_list_notifikasi')?>";
		table = $('#notifikasi').DataTable({
			'processing': true,
			'serverSide': true,
			"pageLength": 10,
			'order': [],
			"ajax": {
				"url": url,
				"type": "POST"
			},

			//Set column definition initialisation properties.
			"columnDefs": [
				{
					"targets": [ 0, 1, 2, 7 ], //first column / numbering column
					"orderable": false, //set not orderable
				},
        {
          className: "padat",
          "targets": [ 1 ]
        },
			],
			'language': {
				'url': BASE_URL + 'assets/datatables/js/dataTables.indonesian.lang'
			},
			'drawCallback': function (){
					$('.dataTables_paginate > .pagination').addClass('pagination-sm no-margin');
			}
		});
	} );
</script>