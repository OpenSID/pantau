<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Notifikasi
			<small>Edit Notifikasi</small>
		</h1>
	</section>

	<!-- Main content -->
	<section class="content container-fluid">

		<!--------------------------
			| Your Page Content Here |
			-------------------------->

			<?= form_open('notifikasi/edit/'.$notifikasi['id'],array("class"=>"form-horizontal")); ?>

				<div class="form-group">
					<label for="aktif" class="col-md-4 control-label"><span class="text-danger">*</span>Aktif</label>
					<div class="col-md-8">
						<select name="aktif" class="form-control">
							<option value="">select</option>
							<?php 
							$aktif_values = array(
								'1'=>'Aktif',
								'0'=>'Tidak aktif',
							);

							foreach($aktif_values as $value => $display_text)
							{
								$selected = ($value == $notifikasi['aktif']) ? ' selected="selected"' : "";

								echo '<option value="'.$value.'" '.$selected.'>'.$display_text.'</option>';
							} 
							?>
						</select>
						<span class="text-danger"><?= form_error('aktif');?></span>
					</div>
				</div>
				<div class="form-group">
					<label for="frekuensi" class="col-md-4 control-label"><span class="text-danger">*</span>Frekuensi</label>
					<div class="col-md-8">
						<input type="text" name="frekuensi" value="<?= ($this->input->post('frekuensi') ? $this->input->post('frekuensi') : $notifikasi['frekuensi']); ?>" class="form-control" id="frekuensi" />
						<span class="text-danger"><?= form_error('frekuensi');?></span>
					</div>
				</div>
				<div class="form-group">
					<label for="kode" class="col-md-4 control-label"><span class="text-danger">*</span>Kode</label>
					<div class="col-md-8">
						<input type="text" name="kode" value="<?= ($this->input->post('kode') ? $this->input->post('kode') : $notifikasi['kode']); ?>" class="form-control" id="kode" />
						<span class="text-danger"><?= form_error('kode');?></span>
					</div>
				</div>
				<div class="form-group">
					<label for="judul" class="col-md-4 control-label"><span class="text-danger">*</span>Judul</label>
					<div class="col-md-8">
						<input type="text" name="judul" value="<?= ($this->input->post('judul') ? $this->input->post('judul') : $notifikasi['judul']); ?>" class="form-control" id="judul" />
						<span class="text-danger"><?= form_error('judul');?></span>
					</div>
				</div>
				<div class="form-group">
					<label for="jenis" class="col-md-4 control-label"><span class="text-danger">*</span>Jenis</label>
					<div class="col-md-8">
						<input type="text" name="jenis" value="<?= ($this->input->post('jenis') ? $this->input->post('jenis') : $notifikasi['jenis']); ?>" class="form-control" id="jenis" />
						<span class="text-danger"><?= form_error('jenis');?></span>
					</div>
				</div>
				<div class="form-group">
					<label for="server" class="col-md-4 control-label"><span class="text-danger">*</span>Server</label>
					<div class="col-md-8">
						<input type="text" name="server" value="<?= ($this->input->post('server') ? $this->input->post('server') : $notifikasi['server']); ?>" class="form-control" id="server" />
						<span class="text-danger"><?= form_error('server');?></span>
					</div>
				</div>
				<div class="form-group">
					<label for="isi" class="col-md-4 control-label"><span class="text-danger">*</span>Isi</label>
					<div class="col-md-8">
						<textarea name="isi" class="form-control" id="isi"><?= ($this->input->post('isi') ? $this->input->post('isi') : $notifikasi['isi']); ?></textarea>
						<span class="text-danger"><?= form_error('isi');?></span>
					</div>
				</div>
				
				<div class="form-group">
					<div class="col-sm-offset-4 col-sm-8">
						<button type="submit" class="btn btn-success">Save</button>
					</div>
				</div>
				
			<?= form_close(); ?>

	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->