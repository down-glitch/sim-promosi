/**
 * Sistem Promosi JavaScript Utilities (SPJU)
 * Fungsi-fungsi utilitas untuk meningkatkan UI/UX
 */

// Fungsi untuk menampilkan loading spinner
function showLoading(element) {
    const originalContent = element.innerHTML;
    element.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Loading...
    `;
    element.disabled = true;
    element.originalContent = originalContent;
}

// Fungsi untuk menyembunyikan loading spinner
function hideLoading(element) {
    if (element.originalContent) {
        element.innerHTML = element.originalContent;
        element.disabled = false;
    }
}

// Fungsi untuk menampilkan notifikasi toast
function showToast(message, type = 'info') {
    // Hapus toast lama jika ada
    const oldToast = document.querySelector('.toast-notification');
    if (oldToast) {
        oldToast.remove();
    }

    // Buat elemen toast
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    toastContainer.style.zIndex = '10000';
    
    const toastDiv = document.createElement('div');
    toastDiv.className = `toast toast-notification bg-${type === 'error' ? 'danger' : type} text-white`;
    toastDiv.setAttribute('role', 'alert');
    toastDiv.innerHTML = `
        <div class="toast-body d-flex justify-content-between align-items-center">
            <span>${message}</span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toastDiv);
    document.body.appendChild(toastContainer);
    
    // Tampilkan toast
    const bsToast = new bootstrap.Toast(toastDiv, { delay: 5000 });
    bsToast.show();
    
    // Hapus toast setelah selesai
    toastDiv.addEventListener('hidden.bs.toast', function() {
        toastContainer.remove();
    });
}

// Fungsi untuk menangani form submission dengan loading
function handleFormSubmit(formId, submitCallback) {
    const form = document.getElementById(formId);
    if (!form) {
        console.error(`Form dengan ID ${formId} tidak ditemukan`);
        return;
    }
    
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            showLoading(submitBtn);
        }
        
        // Jalankan callback setelah loading ditampilkan
        setTimeout(() => {
            submitCallback(e);
        }, 100);
    });
}

// Fungsi untuk menangani konfirmasi sebelum aksi penting
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Fungsi untuk menangani search dengan debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Fungsi untuk format angka
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Fungsi untuk format tanggal
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

// Fungsi untuk menangani infinite scroll (jika diperlukan)
function initInfiniteScroll(containerSelector, loadMoreCallback) {
    const container = document.querySelector(containerSelector);
    if (!container) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                loadMoreCallback();
            }
        });
    });
    
    // Observasi elemen terakhir sebagai trigger
    const sentinel = document.createElement('div');
    container.appendChild(sentinel);
    observer.observe(sentinel);
}

// Inisialisasi komponen-komponen saat DOM siap
document.addEventListener('DOMContentLoaded', function() {
    // Tambahkan event listener untuk semua tombol hapus
    document.querySelectorAll('[data-confirm]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melakukan tindakan ini?';
            const url = this.getAttribute('href') || this.getAttribute('data-url');

            if (url) {
                confirmAction(message, () => {
                    window.location.href = url;
                });
            }
        });
    });

    // Tambahkan event listener untuk form dengan loading
    document.querySelectorAll('form[data-loading]').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                showLoading(submitBtn);
            }
        });
    });

    // Inisialisasi tooltip Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Tambahkan keyboard navigation untuk dropdown
    document.querySelectorAll('.dropdown-toggle').forEach(dropdown => {
        dropdown.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Tambahkan skip link untuk aksesibilitas
    const skipLink = document.createElement('a');
    skipLink.href = '#mainContent';
    skipLink.textContent = 'Lewati ke konten utama';
    skipLink.className = 'skip-link';
    document.body.insertBefore(skipLink, document.body.firstChild);

    // Tambahkan landmark roles
    const mainContent = document.getElementById('mainContent');
    if (mainContent) {
        mainContent.setAttribute('role', 'main');
    }

    // Tambahkan ARIA labels untuk tombol tanpa teks
    document.querySelectorAll('button').forEach(button => {
        if (!button.textContent.trim() && button.querySelector('i')) {
            const icon = button.querySelector('i');
            const iconClass = icon.className;

            if (iconClass.includes('bi-trash')) {
                button.setAttribute('aria-label', 'Hapus');
            } else if (iconClass.includes('bi-pencil')) {
                button.setAttribute('aria-label', 'Edit');
            } else if (iconClass.includes('bi-eye')) {
                button.setAttribute('aria-label', 'Lihat Detail');
            } else if (iconClass.includes('bi-plus')) {
                button.setAttribute('aria-label', 'Tambah');
            }
        }
    });
});

// Fungsi untuk fokus ke elemen tertentu
function focusOnElement(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.focus();
        if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
            element.select();
        }
    }
}

// Fungsi untuk announce pesan ke screen reader
function announceToScreenReader(message) {
    let alert = document.getElementById('screen-reader-alert');
    if (!alert) {
        alert = document.createElement('div');
        alert.id = 'screen-reader-alert';
        alert.setAttribute('aria-live', 'polite');
        alert.setAttribute('aria-atomic', 'true');
        alert.className = 'sr-only';
        document.body.appendChild(alert);
    }
    alert.textContent = message;
}

// Ekspor fungsi-fungsi agar bisa digunakan di tempat lain
window.SPJU = {
    showLoading,
    hideLoading,
    showToast,
    handleFormSubmit,
    confirmAction,
    debounce,
    formatNumber,
    formatDate,
    initInfiniteScroll,
    focusOnElement,
    announceToScreenReader
};