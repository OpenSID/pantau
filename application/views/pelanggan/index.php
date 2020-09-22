<style type="text/css">
	.table-responsive {
    margin-top: 10px;
	}
</style>

<div class="content-wrapper">
	<section class="content-header">
		<h1>Pelanggan<small>Daftar Pelanggan OpenDesa</small></h1>
		<ol class="breadcrumb">
			<li><a href="<?= site_url(); ?>"><i class="fa fa-home"></i> Dashboard</a></li>
			<li class="active">Daftar Pelanggan</li>
		</ol>
	</section>
	<section class="content container-fluid">
		<div class="box box-info">
			<div class="box-header with-border">
				<a href="<?= site_url('pelanggan/form'); ?>" class="btn btn-social btn-flat bg-olive btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block" title="Tambah Pelanggan Baru"><i class="fa fa-plus"></i> Tambah Pelanggan</a>
			</div>
			<div class="box-body">
				<div class="dataTables_wrapper form-inline dt-bootstrap no-footer">
					<form id="mainform" name="mainform" action="<?=site_url("pelanggan/index")?>" method="post">
						<select class="form-control input-sm" name="filter[jenis]" onchange="$('#mainform').submit();">
							<option value="">Pilih Jenis</option>
							<?php foreach ($jenis_pelanggan as $key => $jenis): ?>
								<option value="<?= $key ?>" <?php $selected_filter && $key == $selected_filter['jenis'] && print('selected') ?>><?= ucwords($jenis) ?></option>
							<?php endforeach ?>
						</select>
						<select class="form-control input-sm" name="filter[status]" onchange="$('#mainform').submit();">
							<option value="">Pilih Status</option>
							<?php foreach ($status_langganan as $key => $status): ?>
								<option value="<?= $key ?>" <?php $selected_filter && $key == $selected_filter['status'] && print('selected') ?>><?= ucwords($status) ?></option>
							<?php endforeach ?>
						</select>
						<select class="form-control input-sm" name="filter[pelaksana]" onchange="$('#mainform').submit();">
							<option value="">Pilih Pelaksana</option>
							<?php foreach ($pelaksana as $key => $nama): ?>
								<option value="<?= $key ?>" <?php $selected_filter && $key == $selected_filter['pelaksana'] && print('selected') ?>><?= ucwords($nama) ?></option>
							<?php endforeach ?>
						</select>
					</form>
					<div class="table-responsive">
						<table id="pelanggan" class="table table-bordered table-striped dataTable table-hover tabel-daftar">
							<thead class="bg-gray color-palette">
								<tr>
									<th>No</th>
									<th>Aksi</th>
									<th>Domain</th>
									<th>Desa</th>
									<th>Kontak</th>
									<th>No. HP</th>
									<th>Jenis Langganan</th>
									<th>Tgl Akhir</th>
									<th>Status Langganan</th>
									<th>Pelaksana</th>
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
		var url = "<?= site_url('pelanggan/ajax_list_pelanggan')?>";
		table = $('#pelanggan').DataTable({
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
					"targets": [ 0, 1, 5 ], //first column / numbering column
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