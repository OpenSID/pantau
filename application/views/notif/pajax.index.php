<!-- Pecahan ajax untuk daftar aksi di tabel notifikasi -->
<a href="<?= site_url('notifikasi/form/'.$data['id']); ?>" class="btn bg-orange btn-flat btn-sm" title="Ubah Data"><i class='fa fa-edit'></i></a>
<?php if ($data['aktif'] == '0'): ?>
	<a href="<?= site_url("notifikasi/lock/{$data['id']}/{$data['aktif']}")?>" class="btn bg-navy btn-flat btn-sm" title="'Aktifkan" ><i class="fa fa-lock"></i></a>
<?php elseif ($data['aktif'] == '1'): ?>
	<a href="<?= site_url("notifikasi/lock/{$data['id']}/{$data['aktif']}")?>" class="btn bg-navy btn-flat btn-sm" title="Non-aktifkan"><i class="fa fa-unlock"></i></a>
<?php endif; ?>
<a href="#" data-href="<?= site_url('notifikasi/remove/'.$data['id']); ?>" class="btn bg-maroon btn-flat btn-sm" title="Hapus Data" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash-o"></i></a>
