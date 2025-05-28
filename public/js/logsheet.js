// Mengatur CSRF token untuk semua request AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function() {
    // Hapus DataTable yang ada jika sudah ada
    if ($.fn.DataTable.isDataTable('table')) {
        $('table').DataTable().destroy();
    }

    // Inisialisasi DataTable
    const table = $('table').DataTable({
        processing: true,
        serverSide: false,
        destroy: true,
        scrollX: true,
        scrollCollapse: true,
        "order": [],
        dom: "<'dt-container'<'dataTables_length'l><'dataTables_filter'f>>" +
             "<'dataTables_scroll't>" +
             "<'dt-footer'<'dataTables_info'i><'dataTables_paginate'p>>",
        language: {
            processing: "Sedang memproses...",
            search: "Pencarian:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
            infoFiltered: "(disaring dari _MAX_ total data)",
            zeroRecords: "Tidak ditemukan data yang sesuai",
            emptyTable: "Tidak ada data yang tersedia",
            paginate: {
                first: "Pertama",
                previous: "Sebelumnya",
                next: "Selanjutnya",
                last: "Terakhir"
            }
        },
        columnDefs: [
            {
                targets: [0, -1],
                orderable: false
            }
        ]
    });

    // Hubungkan input pencarian dan panjang tabel
    $('#table-search').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#table-length').on('change', function() {
        table.page.len(this.value).draw();
    });

    // Project selection handler
    $('#project_id, #edit_project_id').on('change', function() {
        const prefix = $(this).attr('id').startsWith('edit_') ? 'edit_' : '';
        const selectedOption = $(this).find('option:selected');
        
        $(`#${prefix}coa`).val(selectedOption.data('coa'));
        $(`#${prefix}customer`).val(selectedOption.data('customer'));
        $(`#${prefix}activity`).val(selectedOption.data('activity'));
        $(`#${prefix}prodi`).val(selectedOption.data('prodi'));
        $(`#${prefix}grade`).val(selectedOption.data('grade'));
        $(`#${prefix}rate_1`).val(selectedOption.data('rate1'));
        $(`#${prefix}rate_2`).val(selectedOption.data('rate2'));
    });

    // Calculate amounts when quantity or rate changes
    function calculateAmounts(prefix = '') {
        const quantity1 = parseFloat($(`#${prefix}quantity_1`).val()) || 0;
        const rate1 = parseFloat($(`#${prefix}rate_1`).val()) || 0;
        const quantity2 = parseFloat($(`#${prefix}quantity_2`).val()) || 0;
        const rate2 = parseFloat($(`#${prefix}rate_2`).val()) || 0;

        const revenue = quantity1 * rate1;
        const cost = quantity2 * rate2;

        $(`#${prefix}revenue`).val(revenue.toFixed(2));
        $(`#${prefix}cost`).val(cost.toFixed(2));
    }

    // Auto calculate for add form
    $('#quantity_1, #rate_1, #quantity_2, #rate_2').on('input', function() {
        calculateAmounts();
    });

    // Auto calculate for edit form
    $('#edit_quantity_1, #edit_rate_1, #edit_quantity_2, #edit_rate_2').on('input', function() {
        calculateAmounts('edit_');
    });

    // Form submission handlers
    $('#logsheetForm, #editLogsheetForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const isEdit = form.attr('id') === 'editLogsheetForm';
        const formData = new FormData(this);

        // Untuk edit, tambahkan method PUT
        if (isEdit) {
            formData.append('_method', 'PUT');
        }

        // Cek role user dari data yang disimpan di body
        const isAdmin = $('body').data('role') === 'admin';

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.message || response.success) {
                    if (isAdmin) {
                        // Notifikasi untuk admin
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: isEdit ? 'Data logsheet berhasil diperbarui' : 'Data logsheet berhasil ditambahkan',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3B82F6'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        // Notifikasi untuk user biasa
                        Swal.fire({
                            html: `
                                <div class="text-center">
                                    <div class="mb-4">
                                        <i class="fas fa-info-circle" style="font-size: 48px; color: #60A5FA;"></i>
                                    </div>
                                    <h2 class="text-xl font-semibold mb-4" style="color: #374151;">Permintaan Terkirim</h2>
                                    <p style="color: #6B7280;">Permintaan ${isEdit ? 'perubahan' : 'penambahan'} data telah dikirim untuk ditinjau.</p>
                                </div>
                            `,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#3B82F6',
                            background: '#FFFFFF'
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage = errors.join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Delete confirmation handler
    $(document).on('click', '.delete-logsheet', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        // Cek role user dari data yang disimpan di body
        const isAdmin = $('body').data('role') === 'admin';

        if (isAdmin) {
            // Konfirmasi hapus untuk admin
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Data logsheet berhasil dihapus',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3B82F6'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menghapus data. Silakan coba lagi.'
                            });
                        }
                    });
                }
            });
        } else {
            // Konfirmasi hapus untuk user biasa
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Permintaan penghapusan akan dikirim untuk ditinjau. Lanjutkan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Kirim',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            Swal.fire({
                                html: `
                                    <div class="text-center">
                                        <div class="mb-4">
                                            <i class="fas fa-info-circle" style="font-size: 48px; color: #60A5FA;"></i>
                                        </div>
                                        <h2 class="text-xl font-semibold mb-4" style="color: #374151;">Permintaan Terkirim</h2>
                                        <p style="color: #6B7280;">Permintaan penghapusan telah dikirim untuk ditinjau</p>
                                    </div>
                                `,
                                showConfirmButton: true,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3B82F6',
                                background: '#FFFFFF'
                            }).then(() => {
                                window.location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal mengirim permintaan. Silakan coba lagi.'
                            });
                        }
                    });
                }
            });
        }
    });

    // Event handler untuk tombol edit
    $(document).on('click', '.edit-logsheet', function() {
        const logsheetId = $(this).data('logsheet-id');
        
        // Tampilkan loading
        Swal.fire({
            title: 'Loading...',
            text: 'Mengambil data logsheet',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Ambil data logsheet
        $.ajax({
            url: `/logsheets/${logsheetId}/edit`,
            method: 'GET',
            success: function(response) {
                // Set action URL untuk form edit
                $('#editLogsheetForm').attr('action', `/logsheets/${logsheetId}`);
                
                // Isi form dengan data
                $('#edit_id').val(logsheetId);
                $('#edit_project_id').val(response.project_id);
                $('#edit_coa').val(response.coa);
                $('#edit_customer').val(response.customer);
                $('#edit_activity').val(response.activity);
                $('#edit_prodi').val(response.prodi);
                $('#edit_grade').val(response.grade);
                $('#edit_seq').val(response.seq);
                $('#edit_quantity_1').val(response.quantity_1);
                $('#edit_rate_1').val(response.rate_1);
                $('#edit_revenue').val(response.revenue);
                $('#edit_ar_status').val(response.ar_status);
                $('#edit_tutor').val(response.tutor);
                $('#edit_quantity_2').val(response.quantity_2);
                $('#edit_rate_2').val(response.rate_2);
                $('#edit_cost').val(response.cost);
                $('#edit_ap_status').val(response.ap_status);

                // Tutup loading dan tampilkan modal
                Swal.close();
                
                // Tampilkan modal edit
                const editModal = document.getElementById('edit-logsheet-modal');
                editModal.classList.remove('hidden');
                editModal.setAttribute('aria-hidden', 'false');
                editModal.style.display = 'flex';
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengambil data logsheet',
                    icon: 'error'
                });
            }
        });
    });

    // Mengubah teks "Tampilkan" dan "entries"
    $('.dataTables_length label').contents().filter(function() {
        return this.nodeType === 3;
    }).replaceWith(function() {
        return this.textContent.replace('Show', 'Tampilkan').replace('entries', 'data');
    });

    // Mengubah teks "Search"
    $('.dataTables_filter label').contents().filter(function() {
        return this.nodeType === 3;
    }).replaceWith('Pencarian: ');

    // Menyembunyikan scrollbar header
    $('.dataTables_scrollHead').css('overflow', 'hidden');

    // Tambahkan kelas Tailwind ke elemen DataTable
    $('.dataTables_wrapper .dataTables_filter input').addClass('bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500');
    $('.dataTables_wrapper .dataTables_length select').addClass('bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500');
}); 