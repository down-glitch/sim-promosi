@props([
    'title' => 'Konfirmasi Aksi',
    'message' => 'Apakah Anda yakin ingin melakukan tindakan ini?',
    'confirmText' => 'Ya, Lanjutkan',
    'cancelText' => 'Batal',
    'confirmVariant' => 'danger',
    'cancelVariant' => 'secondary',
    'showIcon' => true,
])

<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="confirmationModalLabel">
                    @if($showIcon)<i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>@endif
                    {{ $title }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ $message }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-{{ $cancelVariant }}" data-bs-dismiss="modal">{{ $cancelText }}</button>
                <button type="button" class="btn btn-{{ $confirmVariant }}" id="confirmActionButton">{{ $confirmText }}</button>
            </div>
        </div>
    </div>
</div>

<script>
function showConfirmationModal(message, callback) {
    // Update pesan
    document.querySelector('#confirmationModal .modal-body p').textContent = message || "{{ $message }}";
    
    // Hapus event listener sebelumnya
    const confirmBtn = document.getElementById('confirmActionButton');
    confirmBtn.replaceWith(confirmBtn.cloneNode(true));
    const newConfirmBtn = document.getElementById('confirmActionButton');
    
    // Tambahkan event listener baru
    newConfirmBtn.onclick = function() {
        callback();
        bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
    };
    
    // Tampilkan modal
    const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
    modal.show();
}
</script>