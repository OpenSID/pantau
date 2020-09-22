<!-- Pecahan ajax untuk daftar aksi di tabel notifikasi -->
<a href="<?= site_url('pelanggan/form/'.$data['id']); ?>" class="btn bg-orange btn-flat btn-sm" title="Ubah Data"><i class='fa fa-edit'></i></a>
<a href="#" data-href="<?= site_url('pelanggan/remove/'.$data['id']); ?>" class="btn bg-maroon btn-flat btn-sm" title="Hapus Data" data-toggle="modal" data-target="#confirm-delete"><i class="fa fa-trash-o"></i></a>
