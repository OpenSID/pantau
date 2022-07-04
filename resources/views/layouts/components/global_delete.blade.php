<!-- Global modal delete form -->
<div class="modal modal-danger fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="Delete"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-action" action="" method="post">
                @csrf
                @method('delete')
                <div class="modal-body">
                    <h5 class="text-center">Apakah Anda yakin ingin menghapus data ini?</h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <!-- Confirm delete modal -->
    <script>
        $('#confirm-delete').on('show.bs.modal', function(e) {
            $('#form-action').attr('action', $(e.relatedTarget).data('href'));
        });
    </script>
@endpush
