/**
 * Integrasi API Sekolah Indonesia untuk SIM-PROMOSI
 * Fungsi-fungsi untuk autocomplete dan verifikasi sekolah
 */

document.addEventListener('DOMContentLoaded', function() {
    // Cek apakah kita berada di halaman create/edit roadshow
    const formRoadshow = document.getElementById('formRoadshow');
    if (!formRoadshow) return; // Keluar jika bukan halaman yang sesuai

    // Inisialisasi elemen-elemen form
    const provinsiInput = document.getElementById('provinsiInput');
    const provinsiHidden = document.getElementById('provinsi');
    const kabupatenInput = document.getElementById('kabupaten');
    const sekolahInput = document.getElementById('sekolah');
    const sekolahIdInput = document.getElementById('sekolah_id');
    const sekolahNamaInput = document.getElementById('sekolah_nama');

    // Cek apakah elemen sekolahInput ada
    if (!sekolahInput) {
        console.log('Sekolah input tidak ditemukan, mungkin bukan halaman yang sesuai');
        return;
    }

    // Tambahkan elemen dropdown untuk sekolah jika belum ada
    let sekolahList = document.getElementById('sekolahList');
    if (!sekolahList) {
        sekolahList = document.createElement('div');
        sekolahList.id = 'sekolahList';
        sekolahList.className = 'list-group position-absolute rounded shadow';
        sekolahList.style.cssText = 'z-index: 1000; width: 100%; max-height: 300px; overflow-y: auto; display: none; background: white; border: 1px solid #ddd;';
        sekolahInput.parentNode.appendChild(sekolahList);
    }

    // ========== INTEGRASI API SEKOLAH INDONESIA ==========

    // 1. Autocomplete Sekolah dari API
    let sekolahAutocompleteTimeout;
    sekolahInput.addEventListener('input', function() {
        clearTimeout(sekolahAutocompleteTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            sekolahList.style.display = 'none';
            return;
        }

        sekolahAutocompleteTimeout = setTimeout(() => {
            // Ambil provinsi dan kabupaten dari form
            // Kita asumsikan bahwa provinsi dan kabupaten sudah dipilih sebelumnya
            const provinsi = provinsiHidden ? provinsiHidden.value : '';
            const kabupaten = document.getElementById('kabupaten_hidden') ?
                document.getElementById('kabupaten_hidden').value :
                (kabupatenInput ? kabupatenInput.value : '');

            if (!provinsi || !kabupaten) {
                // Jika provinsi dan kabupaten belum dipilih, cari secara umum
                console.log('Provinsi atau kabupaten belum dipilih, mencari secara umum');
            }

            // Cari sekolah menggunakan API
            fetch(`/api/sekolah/autocomplete?q=${encodeURIComponent(query)}&limit=10`)
                .then(response => response.json())
                .then(data => {
                    sekolahList.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(school => {
                            const item = document.createElement('div');
                            item.className = 'list-group-item cursor-pointer border-bottom';
                            item.innerHTML = `
                                <div><strong>${school.text}</strong></div>
                                <small class="text-muted">${school.alamat || 'Alamat tidak tersedia'}, ${school.kabupaten || ''}, ${school.provinsi || ''}</small>
                            `;
                            item.onclick = () => {
                                sekolahInput.value = school.text;
                                if (sekolahIdInput) sekolahIdInput.value = school.id;
                                if (sekolahNamaInput) sekolahNamaInput.value = school.text;
                                sekolahList.style.display = 'none';

                                // Tampilkan nama sekolah di display jika ada
                                const sekolahNameDisplay = document.getElementById('sekolahNameDisplay');
                                if (sekolahNameDisplay) {
                                    sekolahNameDisplay.textContent = school.text;
                                }
                            };
                            sekolahList.appendChild(item);
                        });
                        sekolahList.style.display = 'block';
                    } else {
                        sekolahList.innerHTML = '<div class="list-group-item text-center text-muted">Sekolah tidak ditemukan</div>';
                        sekolahList.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error searching schools:', error);
                    sekolahList.innerHTML = '<div class="list-group-item text-center text-danger">Error memuat data sekolah</div>';
                    sekolahList.style.display = 'block';
                });
        }, 300);
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (event.target !== sekolahInput && !sekolahList.contains(event.target)) {
            sekolahList.style.display = 'none';
        }
    });

    // Tambahkan event listener untuk menutup dropdown saat menekan ESC
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            sekolahList.style.display = 'none';
        }
    });
});