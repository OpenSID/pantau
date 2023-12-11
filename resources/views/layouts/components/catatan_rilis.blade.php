<style>
    .modal-backdrop {
        z-index: 1030!important;
    }
</style>
<!-- Add this modal to your HTML -->
<div class="modal fade" id="catatanrilisModal" tabindex="-1" role="dialog" aria-labelledby="catatanrilisModalLabel" aria-hidden="true" style="margin-top:3%">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="catatanrilisModalLabel">Catatan Rilis</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <!-- Your modal content goes here -->
            @php
                $files = base_path('catatan_rilis.md');
            @endphp
            {!! parsedown($files) !!}
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
        </div>
    </div>
</div>

@push('js')
    <script type="application/javascript">
        // Add an event listener to the document for the button click
        document.addEventListener('DOMContentLoaded', function () {
            var releaseNotesButton = document.getElementById('releaseNotesButton');
            if (releaseNotesButton) {
                releaseNotesButton.addEventListener('click', function () {
                    openReleaseNotesPopup();
                });
            }
        });

        function openReleaseNotesPopup() {
            // Use Bootstrap modal to show the popup
            $('#catatanrilisModal').modal('show');
        }
    </script>
@endpush