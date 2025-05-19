// Mengatur CSRF token untuk semua request AJAX
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// Inisialisasi DataTable
$(document).ready(function() {
    const logsheetTable = $('#logsheet-table').DataTable({
        processing: true,
        serverSide: false,
        destroy: true,
        scrollX: true,
        scrollCollapse: true,
        "order": [],
        dom: "<'dt-container'<'dataTables_length'l><'dataTables_filter'f>>" +
             "<'dataTables_scroll't>" +
             "<'dt-footer'<'dataTables_info'i><'dataTables_paginate'p>>",
        columnDefs: [
            {
                targets: [0],
                orderable: false
            }
        ],
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
        }
    });

    // Handler untuk konfirmasi penghapusan
    $(document).on('click', '.delete-logsheet', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data logsheet akan dihapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        _method: 'DELETE'
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data logsheet berhasil dihapus',
                            icon: 'success',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Gagal menghapus data: ' + (xhr.responseJSON?.message || error),
                            icon: 'error'
                        });
                    }
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
                $('#edit_ar_status').val(response.ar_status);
                $('#edit_tutor').val(response.tutor);
                $('#edit_quantity_2').val(response.quantity_2);
                $('#edit_rate_2').val(response.rate_2);
                $('#edit_ap_status').val(response.ap_status);

                // Tutup loading
                Swal.close();
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

    // Project selection change handler
    $('#project_id, #edit_project_id').change(function() {
        const selectedOption = $(this).find('option:selected');
        const prefix = $(this).attr('id').startsWith('edit_') ? 'edit_' : '';
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

    // Bind calculation events
    ['quantity_1', 'rate_1', 'quantity_2', 'rate_2'].forEach(field => {
        $(`#${field}, #edit_${field}`).on('input', function() {
            calculateAmounts($(this).attr('id').startsWith('edit_') ? 'edit_' : '');
        });
    });

    // Success message handler
    if ($('body').data('success')) {
        Swal.fire({
            title: 'Berhasil!',
            text: $('body').data('success'),
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // Error message handler
    if ($('body').data('error')) {
        Swal.fire({
            title: 'Error!',
            text: $('body').data('error'),
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }
});

// Flag untuk mencegah submit ganda
let isSubmitting = false;

// Handler submit form tambah logsheet
$('#logsheetForm').on('submit', function(e) {
    e.preventDefault();
    
    if (isSubmitting) return false;
    isSubmitting = true;

    const form = $(this);
    const formData = new FormData(this);
    const jsonData = {};
    formData.forEach((value, key) => {
        jsonData[key] = value;
    });

    // Tampilkan loading state
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: jsonData,
        success: function(response) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data logsheet berhasil ditambahkan',
                icon: 'success',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Error!',
                text: 'Gagal menambahkan data: ' + (xhr.responseJSON?.message || error),
                icon: 'error'
            });
        },
        complete: function() {
            isSubmitting = false;
        }
    });
});

// Handler submit form edit logsheet
$('#editLogsheetForm').on('submit', function(e) {
    e.preventDefault();
    
    if (isSubmitting) return false;
    isSubmitting = true;

    const form = $(this);
    const formData = new FormData(this);
    const jsonData = {};
    formData.forEach((value, key) => {
        jsonData[key] = value;
    });
    jsonData._method = 'PUT';

    Swal.fire({
        title: 'Menyimpan...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: `/logsheets/${formData.get('id')}`,
        method: 'POST',
        data: jsonData,
        success: function(response) {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data logsheet berhasil diperbarui',
                icon: 'success',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Error!',
                text: 'Gagal memperbarui data: ' + (xhr.responseJSON?.message || error),
                icon: 'error'
            });
        },
        complete: function() {
            isSubmitting = false;
        }
    });
});