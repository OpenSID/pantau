<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
    <style type="text/css">
        tr.highlight { background-color: orange !important; }
        tr.nonaktif { background-color: #F9E79F !important; }
        td.break {
          word-break: break-all;
        }
    </style>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Desa <?= $desa->nama_desa ?>
        <small><?= "Kec $desa->nama_kecamatan, Kab $desa->nama_kabupaten, Prov $desa->nama_provinsi" ?></small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?= site_url()?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('laporan')?>"><i class="fa fa-dashboard"></i> Daftar Desa</a></li>
        <li class="active">Rincian Desa</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid" id="main">
			<div class="box box-info">
				<div class="box-header with-border">
					<a href="<?= site_url('laporan')?>" class="btn btn-social btn-flat btn-info btn-sm visible-xs-block visible-sm-inline-block visible-md-inline-block visible-lg-inline-block"><i class="fa fa-arrow-circle-left"></i> Kembali Ke Daftar Desa</a>
					<?php if ($error): ?>
						<div class="container-fluid">
					    <div class="row">
				        <div class="col-md-10 col-md-offset-1">
									<div id="alert" class="alert alert-danger" role="alert">
										<?= $error;?>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="box-body form-horizontal">
					<div class="form-group">
						<label for="customerid" class="col-md-2 control-label"><span class="text-danger"></span>Kode Desa</label>
						<div class="col-md-4">
							<input type="text" readonly class="form-control" value="<?= $desa->region_code ?: $desa->kode_desa.' (isian desa)' ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="customerid" class="col-md-2 control-label"><span class="text-danger"></span>Kode Pos</label>
						<div class="col-md-4">
							<input type="text" readonly class="form-control" value="<?= $desa->kode_pos ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="customerid" class="col-md-2 control-label"><span class="text-danger"></span>Alamat Kantor</label>
						<div class="col-md-4">
							<input type="text" readonly class="form-control" value="<?= $desa->alamat_kantor ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="customerid" class="col-md-2 control-label"><span class="text-danger"></span>Lat</label>
						<div class="col-md-4">
							<input type="text" readonly class="form-control" value="<?= $desa->lat ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="customerid" class="col-md-2 control-label"><span class="text-danger"></span>Lng</label>
						<div class="col-md-4">
							<input type="text" readonly class="form-control" value="<?= $desa->lng ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="customerid" class="col-md-2 control-label"><span class="text-danger"></span>Email</label>
						<div class="col-md-4">
							<input type="text" readonly class="form-control" value="<?= $desa->email_desa ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="customerid" class="col-md-2 control-label"><span class="text-danger"></span>Telepon</label>
						<div class="col-md-4">
							<input type="text" readonly class="form-control" value="<?= $desa->telepon ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="customerid" class="col-md-2 control-label"><span class="text-danger"></span>IP Lokal</label>
						<div class="col-md-4">
							<input type="text" readonly class="form-control" value="<?= $desa->ip_lokal ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="customerid" class="col-md-2 control-label"><span class="text-danger"></span>IP Hosting</label>
						<div class="col-md-4">
							<input type="text" readonly class="form-control" value="<?= $desa->ip_hosting ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="customerid" class="col-md-2 control-label"><span class="text-danger"></span>Web</label>
						<div class="col-md-4">
							<input type="text" readonly class="form-control" value="<?= $desa->url_hosting ?>"/>
						</div>
					</div>
				</div>

			</div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

    <?php $adminlte = 'vendor/almasaeed2010/adminlte/'; ?>
    <script src="<?= base_url($adminlte.'bower_components/jquery/dist/jquery.min.js')?>"></script>
    <script src="<?= base_url($adminlte.'bower_components/bootstrap/dist/js/bootstrap.min.js')?>"></script>
    <script src="<?= base_url($adminlte.'dist/js/adminlte.min.js')?>"></script>

    <!-- Ambil confirmation dialog dari https://ethaizone.github.io/Bootstrap-Confirmation/#install
    -->
    <script src="<?php echo base_url('assets/js/popper.js')?>"></script> <!-- diperlukan bootstrap -->
    <script src="<?php echo base_url('assets/js/bootstrap-tooltip.js') ?>"></script> <!-- diperlukan bootstrap-confirmation -->
    <script src="<?php echo base_url('assets/js/bootstrap-confirmation.js') ?>"></script>
    <script src="<?php echo base_url('assets/datatables/js/jquery.dataTables.min.js')?>"></script>
    <script src="<?php echo base_url('assets/datatables/js/dataTables.bootstrap.min.js')?>"></script>
    <script src="<?php echo base_url('assets/bootstrap/js/moment.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/bootstrap/js/bootstrap-datetimepicker.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/bootstrap/js/select2.full.min.js') ?>"></script>
    <script src="<?php echo base_url('assets/js/script.js') ?>"></script>


